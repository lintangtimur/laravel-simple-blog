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
                'title' => 'draft post',
                'is_draft' => true,
            ]);

        Post::factory()
            ->for($user)
            ->create([
                'title' => 'scheduled post',
                'publish_date' => today()->addDay(),
            ]);

        Post::factory()
            ->for($user)
            ->create([
                'title' => 'published post',
                'is_draft' => false,
                'publish_date' => today()->subDay(),
            ]);
    }

    public function test_get_posts_index(): void
    {
        $response = $this->get('/posts');
        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertSee(Post::published()->orderBy('publish_date', 'desc')->first()->title);
        $response->assertDontSee('draft post');
        $response->assertDontSee('scheduled post');
    }
}
