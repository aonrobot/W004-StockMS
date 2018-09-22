<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class ProductTest extends DuskTestCase
{
    //use DatabaseMigrations;

    // public function setUp()
    // {
    //     parent::setUp();
    //     \Artisan::call('migrate:fresh');
    //     \Artisan::call('db:seed');
    // }

    // /** @test */
    // public function test_canSee_autoGenProductCode()
    // {
    //     $this->browse(function (Browser $browser){
    //         $user = User::find(1);
    //         $browser->loginAs($user)
    //         ->visit('/home')
    //         ->press('เพิ่มสินค้า')
    //         ->waitForText('เพิ่มสินค้า')
    //         ->assertSee('P0');
    //     });
    // }

    // /** @test */
    // public function testAdd_onlyName()
    // {
    //     $user = User::find(1);
    //     $this->browse(function (Browser $browser) use($user){
            
    //         $browser->loginAs($user)
    //         ->visit('/home')

    //         //Add Product 1
    //         ->press('เพิ่มสินค้า')
    //         ->waitForText('เพิ่มสินค้า')
    //         ->value('#prod_name', '[DuskTest] Product 1')
    //         ->press('Submit')
    //         ->waitUntilMissing('.modal-dialog')
    //         ->assertSeeIn('#prod_table_wrapper', '[DuskTest] Product 1')

    //         //Add Product 2
    //         ->press('เพิ่มสินค้า')
    //         ->waitForText('เพิ่มสินค้า')
    //         ->value('#prod_name', '[DuskTest] Product 2')
    //         ->press('Submit')
    //         ->waitUntilMissing('.modal-dialog')
    //         ->assertSeeIn('#prod_table_wrapper', '[DuskTest] Product 2');

    //         $response = $this->actingAs($user, 'api')->json('DELETE', 'api/product/2');
    //         $response
    //             ->assertStatus(200)
    //             ->assertJson([
    //                 'destroyed' => true,
    //             ]);

    //         $browser->loginAs($user)
    //         ->visit('/home')
    //         ->assertDontSeeIn('#prod_table_wrapper', '[DuskTest] Product 1');
    //     });

    // }

    /** @test */
    // public function testIncreseQty()
    // {
    //     $user = User::find(1);
    //     $this->browse(function (Browser $browser) use($user){
    //         $browser->loginAs($user)
    //         ->visit('/home')
    //         ->assertDontSeeIn('#prod_table_wrapper', '[DuskTest] Product 1');
    //     });
    // }


}
