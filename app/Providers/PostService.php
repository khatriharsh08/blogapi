<?php

namespace App\Providers;

use App\Models\Post;

class PostService
{
    public function getAll(){
        // Return paginated posts eagerly loading the associated user
        return Post::with('user')->latest()->paginate(10);
    }

    public function create($data){
        $data['user_id'] = auth()->id();
        return Post::create($data);
    }

    public function find($id){
        return Post::with('user')->find($id);
    }

    public function update($id, $data){
        $post = Post::find($id);
        if ($post) {
            $post->update($data);
            return $post;
        }
        return null;
    }

    public function delete($id){
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return true;
        }
        return false;
    }
}
