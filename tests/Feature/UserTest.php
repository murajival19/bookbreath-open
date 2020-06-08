<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
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
    public function testUserShow()
    {
        $response = $this->get('/users/1');
        $response->assertStatus(200);
    }

    public function testUserEdit_noAuth()
    {
        $response = $this->get('/posts/1/edit');
        $response->assertStatus(302);
    }

    public function testUserUpdate_Auth()
    {
        $user = factory(User::class)->create();
        $user->user_description = Str::random(20);
        $response = $this->actingAs($user)->put('users/' . $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'user_description' => $user->user_description,
        ]);
        $response->assertRedirect('/users/' . $user->id);
    }
}
