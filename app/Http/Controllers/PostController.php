<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Providers\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
        // Constructor injection of the PostService
    }

    public function index(){
        $posts = $this->postService->getAll();
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request){
        $post = $this->postService->create($request->validated());
        return response()->json(new PostResource($post), 201);
    }

    public function show($id){
        $post = $this->postService->find($id);

        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, $id){
        $post = Post::findOrFail($id);
        
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->update($id, $request->validated());

        return response()->json(new PostResource($updatedPost), 200);
    }

    public function destroy($id){
        $post = Post::findOrFail($id);
        
        Gate::authorize('delete', $post);

        $this->postService->delete($id);

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
