<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InventoryLog;
use Carbon\Carbon;

class InventoryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
            data
                {
                    inventory_id
                    type
                    amount
                    remark
                    log_date
                }
        */
        
        $inven_id = $request->input('inventory_id'); // find product_id + warehouse_id in Inventory
        $date = $request->input('date');
        $type = $request->input('type');
        $amount = $request->input('amount');
        $remark = $request->input('remark');

        $log = InventoryLog::where('inventory_id', $inven_id)->where('log_date', $date)->where('type', $type);
        $count_log = $log->count();
            
        $d = Carbon::now();
        $timeNow = $d->toTimeString();

        // Create New Record
        if(!$count_log) {
            InventoryLog::create([
                'inventory_id' => $inven_id,
                'type' => $type,
                'amount' => $amount, 
                'remark' => $remark,
                'log_date' => $date,
                'log_time' => $timeNow
            ]);
        } else {
            $lastAmount = $log->first(['amount'])->amount;
            $newAmount = $lastAmount + $amount;
            $log->update(['amount' => $newAmount]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
