<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Providers\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

readonly class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['author_id', 'search', 'sort']);
        $perPage = (int) $request->input('per_page', 15);
        
        $posts = $this->postService->getAll($filters, $perPage);
        
        return $this->success(
            PostResource::collection($posts)->response()->getData(true),
            'Posts fetched successfully'
        );
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->create($request->validated());
        return $this->success(new PostResource($post), 'Post created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        // Offload caching and complex relation fetching to the Service layer
        $postData = $this->postService->getPostWithComments($id);

        if (!$postData) {
            return $this->error('Post not found', 404);
        }

        return $this->success($postData, 'Post fetched successfully');
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->update($post, $request->validated());

        return $this->success(new PostResource($updatedPost), 'Post updated successfully');
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $this->postService->delete($post);

        return $this->success(null, 'Post deleted successfully');
    }
}
