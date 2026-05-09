<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CommentData;
use App\Http\Requests\StoreCommentRequest;
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
    /**
     * CommentController Constructor.
     *
     * @param CommentService $commentService Handles relational logic and event triggering for comments.
     */
    public function __construct(private readonly CommentService $commentService) {}

    /**
     * Fetch paginated comments for a specific post.
     *
     * @param Request $request Implements per_page constraints.
     * @param Post $post Implicit relation model binding.
     * @return AnonymousResourceCollection Resource mapped comments logic.
     */
    public function index(Request $request, Post $post): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $comments = $this->commentService->getForPost($post, $perPage);

        return CommentResource::collection($comments);
    }

    /**
     * Store and associate a new comment against a given post.
     *
     * Prevents XSS via strict validation policies through StoreCommentRequest.
     *
     * @param StoreCommentRequest $request
     * @param Post $post 
     * @return JsonResponse Created record with a populated author relation.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->commentService->create($post, CommentData::fromArray($request->validated()));

        return $this->success(
            new CommentResource($comment->load('user')),
            'Comment created successfully',
            201
        );
    }

    /**
     * Destructively remove a specific comment.
     *
     * Enforces tight authorization matching checking user ownership boundaries.
     *
     * @param Comment $comment Implicit route bound model.
     * @return JsonResponse Emptied response indicating removal.
     * @throws \Illuminate\Auth\Access\AuthorizationException In cases lacking origin ownership.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $this->commentService->delete($comment);

        return $this->success(null, 'Comment deleted successfully', 200);
    }
}
