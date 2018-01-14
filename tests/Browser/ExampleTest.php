<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee('Login');
            $browser->visit('login')->assertSee('Login');
            $browser->visit('register')->assertSee('Register');
            $password5 = str_random(5);
            $password6 = str_random(6);
            $browser->type('name', $password5);
            $browser->type('email', $password5.'@laravel.com');
            $browser->type('password', $password5);
            $browser->type('password_confirmation', $password5);
            $browser->click('@register-submit')->assertSee('Register');
            $browser->type('name', $password6);
            $browser->type('email', $password6.'@laravel.com');
            $browser->type('password', $password6);
            $browser->type('password_confirmation', $password6);
            $browser->click('@register-submit')->assertSee('ShenLaiYe');
            $browser->type('name', '小阿姨');
            $browser->click('@search')->waitForText('小阿姨的家 HOUSE 303')->assertSee('小阿姨的家 HOUSE 303');
            
        });
    }
}
