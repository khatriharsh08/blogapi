<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CommentData;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService) {}

    public function index(Request $request, Post $post): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $comments = $this->commentService->getForPost($post, $perPage);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $this->commentService->create($post, CommentData::fromArray($validated));

        return $this->success(
            new CommentResource($comment->load('user')),
            'Comment created successfully',
            201
        );
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $this->commentService->delete($comment);

        return $this->success(null, 'Comment deleted successfully', 200);
    }
}
