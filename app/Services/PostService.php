<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PostData;
use App\Filters\AuthorFilter;
use App\Filters\SearchFilter;
use App\Filters\SortFilter;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

readonly class PostService
{
    public function getAll(array $filters = [], int $perPage = 15): CursorPaginator
    {
        $limit = min($perPage, 100);

        /** @var Builder $query */
        $query = app(Pipeline::class)
            ->send(Post::query()->with('user'))
            ->through([
                AuthorFilter::class,
                SearchFilter::class,
                SortFilter::class,
            ])
            ->thenReturn();

        // Cursor pagination REQUIRES deterministic ordering
        // Fallback to latest() if sort filter is not applied
        if (! isset($filters['sort'])) {
            $query->latest();
        }

        return $query->cursorPaginate($limit);
    }

    public function create(PostData $data): Post
    {
        return DB::transaction(function () use ($data) {
            return auth()->user()->posts()->create([
                'title' => $data->title,
                'content' => $data->content,
            ]);
        });
    }

    public function getPost(int $id): ?Post
    {
        // Cache individual post to prevent repetitive DB queries
        // Tags allow invalidating just this post later
        return Cache::tags(['posts'])->remember("post.{$id}", now()->addMinutes(15), function () use ($id) {
            return Post::with('user')->find($id);
        });
    }

    public function update(Post $post, PostData $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update($data->toArray());

            // Invalidate cache
            Cache::tags(['posts'])->forget("post.{$post->id}");

            return $post;
        });
    }

    public function delete(Post $post): bool
    {
        return DB::transaction(function () use ($post) {
            $post->delete();

            // Invalidate cache
            Cache::tags(['posts'])->forget("post.{$post->id}");

            return true;
        });
    }
}
