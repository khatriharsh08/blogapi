<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/posts', [
            'title' => 'My First Post',
            'content' => 'This is the content of my first post.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'My First Post');

        $this->assertDatabaseHas('posts', [
            'title' => 'My First Post',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_fetch_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }
}
