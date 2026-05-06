<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Pagination\CursorPaginator;
use App\Filters\AuthorFilter;
use App\Filters\SearchFilter;
use App\Filters\SortFilter;

readonly class PostService
{
    public function getAll(array $filters = [], int $perPage = 15): CursorPaginator
    {
        $limit = min($perPage, 100);

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = app(Pipeline::class)
            ->send(Post::query()->with('user'))
            ->through([
                AuthorFilter::class,
                SearchFilter::class,
                SortFilter::class,
            ])
            ->thenReturn();

        return $query->cursorPaginate($limit);
    }

    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            return auth()->user()->posts()->create($data);
        });
    }

    public function getPostWithComments(int $id): ?array
    {
        // Cache the heavily hit 'show' endpoint payload for 15 minutes.
        // Cache tag invalidation should be used in large-scale apps when posts update.
        return \Illuminate\Support\Facades\Cache::remember("post.{$id}.with_comments", now()->addMinutes(15), function () use ($id) {
            $post = Post::with('user')->find($id);

            if (!$post) return null;

            $comments = $post->comments()
                ->with('user')
                ->latest()
                ->cursorPaginate(15);

            // Serialize immediately so we cache a native array, not enormous Eloquent objects
            return (new \App\Http\Resources\PostResource($post))->additional([
                'comments' => \App\Http\Resources\CommentResource::collection($comments)->response()->getData(true)
            ])->resolve();
        });
    }

    public function find(int $id): ?Post
    {
        return Post::with('user')->find($id);
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update($data);
            return $post;
        });
    }

    public function delete(Post $post): bool
    {
        return DB::transaction(function () use ($post) {
            $post->delete();
            return true;
        });
    }
}
