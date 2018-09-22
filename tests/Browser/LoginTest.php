<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class LoginTest extends DuskTestCase
{
    //use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    // public function testLogin()
    // {
    //     $this->browse(function (Browser $browser){
    //         $browser->loginAs(User::find(1))
    //                 ->visit('/home')
    //                 ->assertPathIs('/home');
    //     });
    // }
}
