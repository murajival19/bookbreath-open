<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Post;
use App\Book;
use Illuminate\Support\Str;

class BookTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBookindex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testBookshow()
    {
        $response = $this->get('/books/1');
        $response->assertStatus(200);
    }

    public function testBooksearch()
    {
        $response = $this->post('/books/search', [
            'search' => '雪国',
        ]);
        $response->assertStatus(200);
    }

    public function testBookexternalsearch()
    {
        $response = $this->post('/books/externalSearch', [
            'search' => '雪国',
        ]);
        $response->assertStatus(200);
    }

}
