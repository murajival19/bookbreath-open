<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Post;

class PostTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }
    // use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPostindex()
    {
        $response = $this->get('/posts');
        $response->assertStatus(200);
    }

    public function testPostshow()
    {
        $response = $this->get('/posts/1');
        $response->assertStatus(200);
    }

    public function testPostcreate_noAuth()
    {
        $response = $this->get('/posts/create');
        $response->assertStatus(302);
    }

    public function testPostedit_noAuth()
    {
        $response = $this->get('/posts/1/edit');
        $response->assertStatus(302);
    }

    public function testPostcreate_Auth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/posts/create');
        $response->assertStatus(200);
    }

    public function testPostedit_Auth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/posts/1/edit');
        $response->assertStatus(200);
    }

    public function testPoststore()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/posts', [
            'content' => '本文本文本文',
            'user_id' => $user->id,
            'book_id' => 1,
        ]);
        $response->assertRedirect('/books/1');
    }

    public function testPostupdate_sameid()
    {
        $user = User::find(1);
        $post = Post::where('user_id', $user->id)->first();
        $response = $this->actingAs($user)->put('/posts/'.$post->id, [
            'content' => '更新しました',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('/posts/'.$user->id);
    }

    public function testPostupdate_differentid()
    {
        $user = factory(User::class)->create();
        $post = Post::orderBy('created_at', 'desc')->first();
        $response = $this->actingAs($user)->put('/posts/'.$post->id, [
            'content' => '更新しました',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('posts');
    }

}
