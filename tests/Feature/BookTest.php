<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookTest extends TestCase
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
    public function testBookIndex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testBookShow()
    {
        $response = $this->get('/books/1');
        $response->assertStatus(200);
    }

    public function testBookSearch()
    {
        $response = $this->post('/books/search', [
            'search' => '雪国',
        ]);
        $response->assertStatus(200);
    }

    public function testBookExternalsearch()
    {
        $response = $this->post('/books/externalSearch', [
            'search' => '雪国',
        ]);
        $response->assertStatus(200);
    }
}
