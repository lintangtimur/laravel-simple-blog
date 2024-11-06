<?php

namespace Tests\Feature\Posts;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class StoreTest extends TestCase
{
    public function test_guest_user_cannot_store_post()
    {
        $response = $this->post(route('posts.store'))->assertRedirect('/login');
        $response->assertSee('Redirecting to');
    }

    public function test_authenticated_user_can_store_post()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        $postData['publish_date'] = "2024-11-05";

        $response = $this->actingAs($user)->post(route('posts.store'), $postData);

        $response->assertRedirect(route('posts.create'));
        $postData['user_id'] = $user->id;
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $postData);
    }

    public function test_authenticated_user_can_store_post_with_valid_data()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/posts', [
            'title' => 'My title',
            'content' => 'My content',
            'user_id' => $user->id,
            'publish_date' => now()->toDateString(),
            'is_draft' => false,
        ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'My title',
            'content' => 'My content',
        ]);
    }

    public function test_authenticated_user_cannot_store_post_with_invalid_data()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => '',
            'content' => '',
            'user_id' => $user->id,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'content','publish_date']);
        $this->assertDatabaseMissing('posts', [
            'user_id' => $user->id,
            'title' => '',
            'content' => '',
        ]);
    }
}
