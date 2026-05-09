<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CommentData;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\CursorPaginator;

readonly class CommentService
{
    public function getForPost(Post $post, int $perPage = 15): CursorPaginator
    {
        return $post->comments()
            ->with('user')
            ->latest() // Important: required for cursor pagination determinism
            ->cursorPaginate($perPage);
    }

    public function create(Post $post, CommentData $data): Comment
    {
        return $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $data->content,
        ]);
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}
