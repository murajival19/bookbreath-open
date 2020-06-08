<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPostIndex()
    {
        $response = $this->get('/posts');
        $response->assertStatus(200);
    }

    public function testPostShow()
    {
        $response = $this->get('/posts/1');
        $response->assertStatus(200);
    }

    public function testPostCreate_noAuth()
    {
        $response = $this->get('/posts/create');
        $response->assertStatus(302);
    }

    public function testPostEdit_noAuth()
    {
        $response = $this->get('/posts/1/edit');
        $response->assertStatus(302);
    }

    public function testPostCreate_Auth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/posts/create');
        $response->assertStatus(200);
    }

    public function testPostEdit_Auth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/posts/1/edit');
        $response->assertStatus(200);
    }

    public function testPostStore()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/posts', [
            'content' => '本文本文本文',
            'user_id' => $user->id,
            'book_id' => 1,
        ]);
        $response->assertRedirect('/books/1');
    }

    public function testPostUpdate_sameid()
    {
        $user = User::find(1);
        $post = Post::where('user_id', $user->id)->first();
        $response = $this->actingAs($user)->put('/posts/' . $post->id, [
            'content' => '更新しました',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('/posts/' . $user->id);
    }

    public function testPostUpdate_differentid()
    {
        $user = factory(User::class)->create();
        $post = Post::orderBy('created_at', 'desc')->first();
        $response = $this->actingAs($user)->put('/posts/' . $post->id, [
            'content' => '更新しました',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('posts');
    }
}
