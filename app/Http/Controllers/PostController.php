<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\PostData;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['author_id', 'search', 'sort']);
        $perPage = (int) $request->input('per_page', 15);

        $posts = $this->postService->getAll($filters, $perPage);

        // Return standard Laravel Resource response
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->create(PostData::fromArray($request->validated()));

        return $this->success(new PostResource($post), 'Post created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        // Offload caching to the Service layer
        $post = $this->postService->getPost($id);

        if (! $post) {
            return $this->error('Post not found', 404);
        }

        return $this->success(new PostResource($post), 'Post fetched successfully');
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->update($post, PostData::fromArray($request->validated()));

        return $this->success(new PostResource($updatedPost), 'Post updated successfully');
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $this->postService->delete($post);

        return $this->success(null, 'Post deleted successfully');
    }
}
