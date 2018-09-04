<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Carbon\Carbon;
use App\Inventory;
use App\InventoryLog;
use App\Library\Log\Inventory as LogInventory;
use App\Library\_Class\Inventory as ClassInventory;

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
        // TODO: If make warehouse system should (&& where(warehouse_id)) to find inventory
        $invenId = $request->input('inventory_id'); // find product_id + warehouse_id in Inventory
        $type = $request->input('type');
        $amount = $request->input('amount');
        $date = $request->input('date');
        $remark = $request->input('remark');

        $resultArray = LogInventory::write($invenId, $type, $amount, $date, $remark);

        return response()->json($resultArray);
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

    // Dateformat input = DD/MM/YYY or something
    public function showByDate($d, $m, $y){
        try{
            $d = Carbon::create($y, $m, $d);
            $date = $d->format('Y-m-d');
        } catch(\Exception $e){
            Log::error($e);
            return response()->json(['show' => false, 'message' => 'Invalid date format']);
        }
        
        $invens = Inventory::get();
        foreach($invens as $index => $inven){
            $invenLog = InventoryLog::where('inventory_id', $inven->id)->where('log_date', $date);
            if($invenLog->count() == 0){
                unset($invens[$index]);
                continue;
            }

            $sumIncrease = intval($invenLog->where('type', 'increase')->sum('amount'));
            $sumDecrease = intval($invenLog->where('type', 'decrease')->sum('amount'));
            $diffSum = $sumIncrease - $sumDecrease;

            $invens[$index]['product_detail'] = \App\Product::where('product_id', $inven->product_id)->first()->toArray();
            $invens[$index]['log'] = $invenLog->orderBy('created_at', 'desc')->get()->toArray();
            $invens[$index]['logConclude'] = [
                'sumIncrease' => $sumIncrease,
                'sumDecrease' => $sumDecrease
            ];

            $invenTypeCreate = InventoryLog::where('inventory_id', $inven->id)->where('type', 'create');
            if($invenTypeCreate->count() > 0) $diffSum += intval($invenTypeCreate->first(['amount'])->amount);
            $invens[$index]['reCheckQuantity'] = [
                'fromLog' => $diffSum,
                'fromInventory' => $inven->quantity,
                'result' => (($diffSum) == $inven->quantity)
            ];
        }

        return response()->json($invens);
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
        $newAmount = $request->input('amount');
        $remark = $request->input('remark');

        $log = InventoryLog::where('id', $id)->first();

        if(empty($log)) return response()->json(['updated' => false, 'message' => 'This log id not found']);
        if($newAmount < 0) return response()->json(['updated' => false, 'message' => 'Amount must isn\'t negative number!!!']);

        // TODO Unit test
        // !! newAmount must isn't negative number 
        // log(increase) oldAmount = 15, newAmount 20 -> inventory quantity = (+5)
        // log(increase) oldAmount = 20, newAmount 15 -> inventory quantity = (-5)
        // log(decrease) oldAmount = 15, newAmount 20 -> inventory quantity = (-5)
        // log(decrease) oldAmount = 20, newAmount 15 -> inventory quantity = (+5)

        $date = $log->log_date;
        if($date != Carbon::now()->toDateString()) return response()->json(['updated' => false, 'message' => 'You cant edit past log']);

        $invenId = $log->inventory_id;
        $type = $log->type;
        $oldAmount = $log->amount;
        $diffAmount = abs($oldAmount - $newAmount);

        // Update InventoryLog
        $log->update(['amount' => $newAmount, 'remark' => $remark]);

        // Update Inventory
        if($oldAmount < $newAmount) {
            $result = ClassInventory::ajust($invenId, $type, $diffAmount);

            // TODO Update currentQuantity in invantoryLog (newer date)
        } else if ($oldAmount > $newAmount) {
            $result = ClassInventory::ajustInverse($invenId, $type, $diffAmount);

            // TODO Update currentQuantity in invantoryLog (newer date)
        } else {
            $result = ClassInventory::ajust($invenId, $type, $diffAmount);
            
            // TODO Update currentQuantity in invantoryLog (newer date)
        }

        return response()->json($result);
        //$adjustAmount = 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!\App\InventoryLog::where('id', $id)->count()) return ['error' => 'Not found this log'];
        $log = InventoryLog::where('id', $id)->first();
        $invenId = $log->inventory_id;
        $type = $log->type;
        $amount = $log->amount;

        $result = ClassInventory::ajustInverse($invenId, $type, $amount);
        if($result['updated'] == true){
            // TODO Update currentQuantity in invantoryLog (newer date)
            InventoryLog::destroy($id);
            return response()->json($result);
        } else {
            return response()->json($result);
        }
    }
}
