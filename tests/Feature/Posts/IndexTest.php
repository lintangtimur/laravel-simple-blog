<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()
                    ->hasPosts(3)
                    ->create();

        Post::factory()
            ->for($user)
            ->create([
                'title' => 'This is draft post',
                'is_draft' => true,
            ]);

        Post::factory()
            ->for($user)
            ->create([
                'title' => 'This is scheduled post',
                'published_at' => today()->addDay(),
            ]);
    }

    public function test_get_posts_index(): void
    {
        $response = $this->get('/posts');
        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertSee(Post::first()->title);
        $response->assertDontSee('This is draft post');
        $response->assertDontSee('This is scheduled post');
    }
}
