<?php

namespace Tests\Feature\Posts;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class EditTest extends TestCase
{
    public function test_guest_user_cannot_edit_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $response = $this->get(route('posts.edit', ['post' => $post->id]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_authorized_users_can_edit_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $response = $this->actingAs($user)->get(route('posts.edit', ['post' => $post->id]));

        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
        $response->assertViewHas('post', $post);
    }

    public function test_authorized_users_cannot_update_another_user_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $anotherPostFromUser = Post::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.edit', ['post' => $anotherPostFromUser->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);

    }
}
