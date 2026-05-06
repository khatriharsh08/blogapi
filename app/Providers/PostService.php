<?php

namespace App\Providers;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function getAll(){
        // Return paginated posts eagerly loading the associated user
        return Post::with('user')->latest()->paginate(10);
    }

    public function create($data){
        return DB::transaction(function () use ($data) {
            return auth()->user()->posts()->create($data);
        });
    }

    public function find($id){
        return Post::with('user')->find($id);
    }

    public function update(Post $post, $data){
        return DB::transaction(function () use ($post, $data) {
            $post->update($data);
            return $post;
        });
    }

    public function delete(Post $post){
        return DB::transaction(function () use ($post) {
            $post->delete();
            return true;
        });
    }
}
