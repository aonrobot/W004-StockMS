<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\User;
use App\InventoryLog;

class apiInventoryLogTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate_BadInvenId()
    {
        $user = User::find(1);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/invenLog', [
            "inventory_id" => -1,
            "type"=> "increase",
            "amount"=> 4,
            "remark"=> "eiei",
            "date"=> "2018-09-03"
        ]);

        $response->assertJsonStructure(['error']);
    }

    public function testCreate_AddNewDate()
    {
        factory(InventoryLog::class)->make();
    }
}
