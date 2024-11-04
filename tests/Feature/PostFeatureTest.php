<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_welcome_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // test welcome page can be guest and see login and register link
    public function test_welcome_page_contain_login_and_register_link(): void
    {
        $response = $this->get('/');

        $response->assertSee('Login');
        $response->assertSee('Register');
    }

    // test welcome page login as user, create 1 post, and check in welcome page to see his own post
    public function test_welcome_page_contain_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);
        
        $response = $this->actingAs($user)->get('/');

        $response->assertSee($post->title);
    }

    // test route dibawah ini return 200
    public function test_posts_index_page_is_displayed(): void
    {
        $response = $this->get('/posts');

        $response->assertStatus(200);
    }

    // /posts/create cek this route protect by login
    public function test_posts_create_page_is_protect_by_login(): void
    {
        $response = $this->get('/posts/create');

        $response->assertStatus(302);
    }

    public function test_show_post_by_id(): void
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);

        // Act: Kunjungi route show untuk post tersebut
        $response = $this->get(route('posts.show', ['post' => $post]));

        // Assert: Pastikan respons berhasil (status 200) dan judul post tampil
        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertSee($post->title);
        $response->assertSee($post->content);
    }

    public function test_authenticated_user_can_access_the_edit_page_for_existing_post()
    {
        // Arrange: Buat user yang terotentikasi dan post
        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);

        // Act: Autentikasi user dan akses halaman edit untuk post yang ada
        $this->actingAs($user);
        $response = $this->get(route('posts.edit', ['post' => $post]));

        // Assert: Pastikan respons berhasil (status 200) dan view yang benar ditampilkan
        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
        $response->assertViewHas('post', $post); // Pastikan variabel `post` tersedia di view
    }

    public function test_authenticated_user_cant_access_the_edit_page_from_another_user()
    {
        // Arrange: Buat user yang terotentikasi dan post
        $user = User::factory()->create();
        $userTwo = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);


        $this->actingAs($userTwo);
        $response = $this->get(route('posts.edit', ['post' => $post]));

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_a_post_with_valid_data()
    {
        // Arrange: Buat user dan post
        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);

        // Act: Otentikasi user dan update post
        $response = $this->actingAs($user)->patch(route('posts.update', ['post' => $post]), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'publish_date' => now()->toDateString() 
        ]);

        $response->assertSessionHasNoErrors();

        $post->refresh();

        $this->assertSame('Updated Title', $post->title);
        $this->assertSame('Updated content', $post->content);
    }

    public function test_only_authenticated_users_can_delete_his_own_posts()
    {
        // Arrange: Membuat post
        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);

        $this->actingAs($user);
        $response = $this->delete(route('posts.destroy', ['post' => $post]));

        $response->assertStatus(302);
    }

    public function test_user_cant_delete_other_users_posts()
    {
        // Arrange: Membuat post
        $user = User::factory()->create();
        $userTwo = User::factory()->create();
        $post = Post::create(['user_id' => $user->id,'title'=> 'test post', 'content' => 'test content', 'publish_date' => now()]);

        $this->actingAs($userTwo);
        $response = $this->delete(route('posts.destroy', ['post' => $post]));

        $response->assertStatus(403);
    }
    
}
