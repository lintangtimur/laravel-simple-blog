<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    public function test_guest_user_cannot_delete_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $response = $this->delete(route('posts.destroy', ['post' => $post->id]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_authorized_users_can_delete_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $response = $this->actingAs($user)->delete(route('posts.destroy', ['post' => $post->id]));

        $response->assertStatus(302);
        $response->assertRedirectToRoute('welcome');
        $this->assertSoftDeleted('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);
    }

    public function test_authorized_users_cannot_delete_another_user_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $anotherPostFromUser = Post::factory()->create();

        $response = $this->actingAs($user)->delete(route('posts.destroy', ['post' => $anotherPostFromUser->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);

    }
}
