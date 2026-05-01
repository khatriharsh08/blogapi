<?php

namespace App\Providers;

use App\Models\Post;

class PostService
{
    public function getAll(){
        return Post::with('user')->latest()->get();
    }

    public function create($data){
        $data['user_id'] = auth()->id();
        return Post::create($data);
    }

    public function find($id){
        return Post::with('user')->find($id);
    }



    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
