<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $password5 = str_random(5);
        $response = $this->json('POST', 'register', [
            "name" => "$password5",
            "email" => "$password5@$password5.com",
            "password" => "$password5",
            "password_confirmation" => "$password5",
            "_token" => csrf_token(),
        ]);
        $response->assertStatus(422);
        $password = str_random(6);
        
        $response = $this->json('POST', 'register', [
            "name" => "$password",
            "email" => "$password@Sally.com",
            "password" => "$password",
            "password_confirmation" => "$password",
            "_token" => csrf_token(),
        ])->assertStatus(302);

        
        $response = $this->json('POST', 'login', [
            "email" => "$password@Sally.com",
            "password" => "$password",
            "_token" => csrf_token(),
        ])->assertStatus(302);

        $response = $this->json('POST', 'login', [
            "email" => "$password@Sally.com",
            "password" => "asd",
            "_token" => csrf_token(),
        ])->assertStatus(302);

        $response = $this->json('POST', 'logout', [])->assertStatus(302);

    }
}
