<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        
        $response = $this->get('login');
        $response->assertStatus(200);

        $response = $this->get('register');
        $response->assertStatus(200);
    }
}
