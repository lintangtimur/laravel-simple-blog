<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_patch_posts_update_by_guest_user(): void
    {
        $post = Post::factory()
                    ->for(User::factory())
                    ->create();
        $params = Post::factory()->make()->toArray();

        $response = $this->patch('/posts/'.$post->id, $params);
        $response->assertRedirect('/login');
    }

    public function test_patch_posts_update_by_authenticated_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()
                    ->for($user)
                    ->create();
        $params = Post::factory()->make()->toArray();

        $response = $this->actingAs($user)->patch('/posts/'.$post->id, $params);
        $response->assertRedirectToRoute('home');
        $params['user_id'] = $user->id;
        $this->assertDatabaseHas('posts', $params);
    }

    public function test_patch_posts_update_of_other_users_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()
                    ->for(User::factory())
                    ->create();
        $params = Post::factory()->make()->toArray();

        $response = $this->actingAs($user)->patch('/posts/'.$post->id, $params);
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);
        $this->assertDatabaseMissing('posts', [
            'title' => $params['title'],
            'content' => $params['content'],
        ]);
    }
}
