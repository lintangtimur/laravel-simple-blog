<?php

namespace Tests\Feature\Posts;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class ShowTest extends TestCase
{
    public function test_guest_user_can_access_detail_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->get(route('posts.show', ['post' => $post->id]));

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertViewHas('post', $post);
    }

    public function test_authorized_users_can_access_detail_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $response = $this->get(route('posts.show', ['post' => $post->id]));

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertViewHas('post', $post);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);
    }
}
