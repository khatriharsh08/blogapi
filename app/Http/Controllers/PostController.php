<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\PostService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
        // Constructor injection of the PostService
    }

    public function index(){
        return response()->json($this->postService->getAll(), 200);
    }

    public function store(StorePostRequest $request){
        $post = $this->postService->create($request->validated());
        return response()->json($post, 201);
    }

    public function show($id){
        $post = $this->postService->find($id);

        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post, 200);
    }

    public function update(UpdatePostRequest $request, $id){
        $post = $this->postService->find($id);

        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $updatedPost = $this->postService->update($id, $request->validated());

        return response()->json($updatedPost, 200);
    }

    public function destroy($id){
        $post = $this->postService->find($id);

        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->postService->delete($id);

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
