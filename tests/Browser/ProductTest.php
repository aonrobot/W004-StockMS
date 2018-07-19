<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class ProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_canSee_autoGenProductCode()
    {
        $this->browse(function (Browser $browser){
            $user = User::find(1);
            $browser->loginAs($user)
            ->visit('/home')
            ->press('เพิ่มสินค้า')
            ->waitForText('เพิ่มสินค้า')
            ->assertSee('P0');
        });
    }

    /** @test */
    public function testAdd_onlyName_1()
    {
        $this->browse(function (Browser $browser){
            $user = User::find(1);
            $browser->loginAs($user)
            ->visit('/home')
            ->press('เพิ่มสินค้า')
            ->waitForText('เพิ่มสินค้า')
            ->value('#prod_name', '[DuskTest] Product 1')
            ->press('Submit')
            ->assertSeeIn('#prod_table', '[DuskTest] Product 1');
        });
    }

    /** @test */
    public function testAdd_onlyName_2()
    {
        $this->browse(function (Browser $browser){
            $user = User::find(1);
            $browser->loginAs($user)
            ->visit('/home')
            ->press('เพิ่มสินค้า')
            ->waitForText('เพิ่มสินค้า')
            ->value('#prod_name', '[DuskTest] Product 2')
            ->press('Submit')
            ->assertSeeIn('#prod_table', '[DuskTest] Product 2');
        });
    }

    /** @test */
    // public function testRemove_someProduct_and_refresh()
    // {
    //     $this->browse(function (Browser $browser){
    //         $user = User::find(1);
    //         $browser->loginAs($user)
    //         ->visit('/home')
    //         ->press('เพิ่มสินค้า')
    //         ->waitForText('เพิ่มสินค้า')
    //         ->value('#prod_name', '[DuskTest] Product 2')
    //         ->press('Submit')
    //         ->assertSeeIn('#prod_table', '[DuskTest] Product 2');
    //     });
    // }


}
