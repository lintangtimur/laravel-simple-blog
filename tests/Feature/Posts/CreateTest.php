<?php

namespace Tests\Feature\Posts;

use Tests\TestCase;
use App\Models\User;

class CreateTest extends TestCase
{
    public function test_guest_user_cannot_access_create_post_page(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect('/login');
    }

    public function test_authorized_users_can_see_create_post_form()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('posts.create'));
        
        $response->assertSee('Title');
        $response->assertSee('Content');
        $response->assertSee('Publish Date');
        $response->assertSee('Save as Draft'); 
        $response->assertSee('POST');
    }
}
