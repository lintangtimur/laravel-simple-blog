<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_get_by_home_guest_users(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertSee('Please');
        $response->assertDontSee('Your Posts');
    }

    public function test_get_home_by_authenticated_users(): void
    {
        $user = User::factory()
                    ->hasPosts(3)
                    ->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertDontSee('Please');
        $response->assertSee($user->posts->first()->title);
    }
}
