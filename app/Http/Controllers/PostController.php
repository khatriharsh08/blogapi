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
    /**
     * PostController Constructor.
     *
     * @param PostService $postService Service layer encapsulating domain-specific logic for posts.
     */
    public function __construct(private readonly PostService $postService) {}

    /**
     * Retrieve a paginated list of posts.
     *
     * Serves as the primary content discovery mechanism containing optimizations 
     * like eager-loading, filtering, and standard cursor-based pagination defaults.
     * 
     * @param Request $request
     * @return AnonymousResourceCollection A collection of PostResource objects.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['author_id', 'search', 'sort']);
        $perPage = (int) $request->input('per_page', 15);

        $posts = $this->postService->getAll($filters, $perPage);

        // Return standard Laravel Resource response
        return PostResource::collection($posts);
    }

    /**
     * Persist a new Post entity into the datastore.
     *
     * Validates input layout, maps incoming attributes via DTO transformation, 
     * and strictly connects the post to the globally authenticated author.
     *
     * @param StorePostRequest $request Validated structural payload.
     * @return JsonResponse Created record with HTTP 201 status code.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->create(PostData::fromArray($request->validated()));

        return $this->success(new PostResource($post), 'Post created successfully', 201);
    }

    /**
     * Resolve a single Post by identifier.
     *
     * Optimized for high-throughput reads involving caching mechanisms resolved 
     * at the Service tier. Fallbacks cleanly on Cache miss.
     *
     * @param int $id System identifier for the post.
     * @return JsonResponse Deep-loaded relationships mapping to the Post resource.
     */
    public function show(int $id): JsonResponse
    {
        // Offload caching to the Service layer
        $post = $this->postService->getPost($id);

        if (! $post) {
            return $this->error('Post not found', 404);
        }

        return $this->success(new PostResource($post), 'Post fetched successfully');
    }

    /**
     * Mutate an existing Post entity.
     *
     * Protected explicitly by Gate authorization asserting that only the authentic 
     * author or an escalated privilege entity can alter content. 
     * Cache invalidation is triggered synchronously.
     *
     * @param UpdatePostRequest $request Validated structural permutation payload.
     * @param Post $post The implicitly bound Post entity.
     * @return JsonResponse Updated resource context.
     * @throws \Illuminate\Auth\Access\AuthorizationException If policy check fails.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->update($post, PostData::fromArray($request->validated()));

        return $this->success(new PostResource($updatedPost), 'Post updated successfully');
    }

    /**
     * Delete an existing Post permanently or flag as soft deleted.
     *
     * Scoped securely to authorized originators to prevent destructive tampering.
     *
     * @param Post $post The implicitly bound Post entity.
     * @return JsonResponse Standardized stateless structural teardown response.
     * @throws \Illuminate\Auth\Access\AuthorizationException If capability policy fails.
     */
    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $this->postService->delete($post);

        return $this->success(null, 'Post deleted successfully');
    }
}
