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
            Request data
                {
                    date
                    inventory_id
                    type
                    amount
                    remark
                }
        */
        
        // Get data from request
        $invenId = $request->input('inventory_id'); // find product_id + warehouse_id in Inventory
        $date = $request->input('date');
        $type = $request->input('type');
        $amount = $request->input('amount');
        $remark = $request->input('remark');

        // Check 
        //\App\Library\Model::checkIntegrity();
        if(!\App\Inventory::where('id', $invenId)->count()) return response()->json(['error' => 'Not found this inventory id']);

        // Find Log
        $log = InventoryLog::where('inventory_id', $invenId)->where('log_date', $date)->where('type', $type);
        $count_log = $log->count();
        
        // Create time from current time
        $d = Carbon::now();
        $timeNow = $d->toTimeString();

        // Create New Record
        if(!$count_log) {
            $invenLogId = InventoryLog::create([
                'inventory_id' => $invenId,
                'type' => $type,
                'amount' => $amount, 
                'remark' => $remark,
                'log_date' => $date,
                'log_time' => $timeNow
            ])->id;
            
            // Response id
            return response()->json([
                'created' => true,
                'message' => 'create new log',
                'id' => $invenLogId
            ]);
        
        // Update only amount
        } else {
            $lastAmount = $log->first(['amount'])->amount;
            $newAmount = $lastAmount + $amount;
            $log->update(['amount' => $newAmount]);

            // Response amount
            return response()->json([
                'created' => true,
                'message' => 'update log id ' . $log->first(['id'])->id,
                'amount' => $newAmount
            ]);
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
