<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Providers\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
        // Constructor injection of the PostService
    }

    public function index(){
        try {
            $posts = $this->postService->getAll();
            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error('Failed to fetch posts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch posts.'], 500);
        }
    }

    public function store(StorePostRequest $request){
        try {
            $post = $this->postService->create($request->validated());
            return response()->json(new PostResource($post), 201);
        } catch (\Exception $e) {
            Log::error('Failed to create post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create post.'], 500);
        }
    }

    public function show($id){
        try {
            $post = $this->postService->find($id);

            if(!$post){
                return response()->json(['message' => 'Post not found'], 404);
            }

            return new PostResource($post);
        } catch (\Exception $e) {
            Log::error('Failed to fetch post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch post.'], 500);
        }
    }

    public function update(UpdatePostRequest $request, $id){
        try {
            $post = $this->postService->find($id);

            if(!$post){
                return response()->json(['message' => 'Post not found'], 404);
            }

            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $updatedPost = $this->postService->update($id, $request->validated());

            return response()->json(new PostResource($updatedPost), 200);
        } catch (\Exception $e) {
            Log::error('Failed to update post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update post.'], 500);
        }
    }

    public function destroy($id){
        try {
            $post = $this->postService->find($id);

            if(!$post){
                return response()->json(['message' => 'Post not found'], 404);
            }

            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->postService->delete($id);

            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post.'], 500);
        }
    }
}
