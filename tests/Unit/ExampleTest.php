<?php

namespace Tests\Unit;

use App\SearchResult;
use App\User;
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
        $this->get('/')->assertStatus(302);

        $this->get('login')->assertStatus(200);

        $this->get('register')->assertStatus(200);

        $password5 = str_random(5);
        $this->json('POST', 'register', [
            'name'                  => "$password5",
            'email'                 => "$password5@$password5.com",
            'password'              => "$password5",
            'password_confirmation' => "$password5",
            '_token'                => csrf_token(),
        ])->assertStatus(422);
        $password = str_random(6);

        $this->json('POST', 'register', [
            'name'                  => "$password",
            'email'                 => "$password@Sally.com",
            'password'              => "$password",
            'password_confirmation' => "$password",
            '_token'                => csrf_token(),
        ])->assertStatus(302);

        $this->json('POST', 'login', [
            'email'    => "$password@Sally.com",
            'password' => "$password5",
            '_token'   => csrf_token(),
        ])->assertStatus(302);

        $this
            ->actingAs(User::first())
            ->get('/')
            ->assertStatus(200);
        $this
            ->actingAs(User::first())
            ->json('POST', 'search', ['name'=>'小阿姨'])
            ->assertJson(SearchResult::where('keyword', 'like', '小阿姨')->get()->toArray());
        $this
            ->actingAs(User::first())
            ->json('POST', 'search', ['name'=>'小阿姨'])
            ->assertJson(SearchResult::where('keyword', 'like', '小阿姨')->get()->toArray());
        // ->assertStatus(200);
        foreach (SearchResult::get() as $value) {
            $value->delete();
        }
        $this
            ->actingAs(User::first())
            ->json('POST', 'search/gps', [
                'longitude'=> 120.646705,
                'latitude' => 24.178820,
            ])
            ->assertJson(SearchResult::get()->toArray());
        $this
            ->actingAs(User::first())
            ->json('POST', 'review', [
                    'longitude'=> 120.646705,
                    'latitude' => 24.178820,
                ])
            ->assertJson([[
                    'longitude'=> 120.646705,
                    'latitude' => 24.178820,
                ]]);

        // $this
        //     ->actingAs(User::first())
        //     ->json('POST','search',['name'=>"小阿姨"])
        // $response = $this->json('POST', 'login', [
        //     "email" => "$password@Sally.com",
        //     "password" => "$password5"."wsqer",
        //     "_token" => csrf_token(),
        // ])->assertStatus(302);

        // $response = $this->json('POST', 'login', [
        //     "email" => "$password@Sally.com",
        //     "password" => "asd",
        //     "_token" => csrf_token(),
        // ])->assertStatus(302);

        // $response = $this->json('POST', 'logout', [])->assertStatus(302);
    }
}
