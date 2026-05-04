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

    public function show(Post $post){
        $post->loadMissing('user');
        
        // Paginate comments to avoid massive data load
        $comments = $post->comments()
            ->with('user') // Avoid N+1 on comment authors
            ->latest()
            ->paginate();

        return (new PostResource($post))->additional([
            'comments' => \App\Http\Resources\CommentResource::collection($comments)->response()->getData(true)
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post){
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->update($post, $request->validated());

        return response()->json(new PostResource($updatedPost), 200);
    }

    public function destroy(Post $post){
        Gate::authorize('delete', $post);

        $this->postService->delete($post);

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
