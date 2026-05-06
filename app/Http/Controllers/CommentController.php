<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->cursorPaginate(15);

        return $this->success(
            CommentResource::collection($comments)->response()->getData(true),
            'Comments fetched successfully'
        );
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content']
        ]);

        return $this->success(
            new CommentResource($comment->load('user')),
            'Comment created successfully',
            201
        );
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);
        
        $comment->delete();
        return $this->success(null, 'Comment deleted successfully', 200);
    }
}
