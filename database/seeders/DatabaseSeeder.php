<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 10 Random Users
        $users = User::factory(10)->create();

        // One test user explicitly for easy login
        $testUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'admin@demo.com',
            // default password from factory is 'password'
        ]);

        $allUsers = $users->push($testUser);

        // Generate 20 Posts using random users
        $posts = Post::factory(20)->recycle($allUsers)->create();

        // Generate 5 to 50 Comments per Post
        foreach ($posts as $post) {
            Comment::factory(rand(5, 50))
                ->recycle($allUsers) // Randomly assign one of our users
                ->create(['post_id' => $post->id]);
        }
    }
}
