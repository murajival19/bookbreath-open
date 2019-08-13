<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\User;

class UserTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }
    // use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUsershow()
    {
        $response = $this->get('/users/1');
        $response->assertStatus(200);
    }

    public function testUseredit_noAuth()
    {
        $response = $this->get('/posts/1/edit');
        $response->assertStatus(302);
    }

    public function testUserupdate_Auth()
    {
        $user = factory(User::class)->create();
        $user->user_description = Str::random(20);
        $response = $this->actingAs($user)->put('users/'.$user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'user_description' => $user->user_description,
        ]);
        $response->assertRedirect('/users/'.$user->id);
    }
}
