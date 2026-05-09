<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_comment_to_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/posts/{$post->id}/comments", [
            'content' => 'Great post!',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.content', 'Great post!');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Great post!',
        ]);
    }

    public function test_post_author_can_delete_comment()
    {
        $postAuthor = User::factory()->create();
        $commentAuthor = User::factory()->create();

        $post = Post::factory()->create(['user_id' => $postAuthor->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $commentAuthor->id,
        ]);

        // Post author deleting the comment
        $response = $this->actingAs($postAuthor)->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
