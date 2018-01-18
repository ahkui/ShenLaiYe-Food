<?php

namespace Tests\Unit;

use App\Restaurant;
use App\SearchResult;
use App\User;
use Cache;
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

        $this
            ->get('password/reset')
            ->assertStatus(200);

        $this
            ->get('password/reset/123')
            ->assertStatus(200);

        $password5 = str_random(5);
        $password = str_random(6);

        $this->json('POST', 'register', [
            'name'                  => "$password5",
            'email'                 => "$password5@$password5.com",
            'password'              => "$password5",
            'password_confirmation' => "$password5",
            '_token'                => csrf_token(),
        ])->assertStatus(422);

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
            ->json('POST', 'search', ['name'=>'小阿姨'])
            ->assertJson(SearchResult::where('keyword', 'like', '小阿姨')->get()->toArray());

        $this
            ->json('POST', 'search', ['name'=>'小阿姨'])
            ->assertJson(SearchResult::where('keyword', 'like', '小阿姨')->get()->toArray());

        $this
            ->json('POST', 'search', ['name'=>'小阿姨', 'is_shop'=>'true'])
            ->assertJson(['data'=>SearchResult::where('keyword', 'like', '小阿姨')->get()->toArray()]);

        foreach (SearchResult::get() as $value) {
            $value->delete();
        }

        foreach (Restaurant::get() as $value) {
            $value->delete();
        }

        $this
            ->json('POST', 'search/gps', [
                'longitude'=> 120.646705,
                'latitude' => 24.178820,
            ])
            ->assertJson(SearchResult::get()->toArray());

        $res = Restaurant::first();

        $this
            ->json('POST', 'review', [
                    'id'=> $res->_id,
                ])
            ->assertStatus(200);

        $this
            ->json('POST', 'review', [
                    'id'=> $res->_id,
                ])
            ->assertStatus(200);

        $this
            ->json('PUT', 'review', [
                    'id'     => $res->_id,
                    'rate'   => 5,
                    'comment'=> 'qwe123zxcasd',
                ])
            ->assertStatus(200);

        $this
            ->json('PUT', 'review', [
                    'id'     => $res->_id,
                    'rate'   => 5,
                    'comment'=> 'qwe123zx2casd',
                ])
            ->assertStatus(200);

        $this
            ->json('PUT', 'review', [
                    'id'     => $res->_id,
                    'rate'   => 5,
                    'comment'=> 'qwe123zxc4asd',
                ])
            ->assertStatus(200);

        $this
            ->json('POST', 'review', [
                    'id'=> $res->_id,
                ])
            ->assertStatus(200);

        $this
            ->json('POST', 'suggest', [])
            ->assertStatus(200)
            ->assertSee(Cache::get('suggest', null));

        $this
            ->json('POST', 'suggest', [])
            ->assertStatus(200)
            ->assertSee(Cache::get('suggest', null));
    }
}
