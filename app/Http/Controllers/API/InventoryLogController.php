<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Library\Log\Inventory as LogInventory;
use App\Inventory;
use App\InventoryLog;

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

        $resultArray = LogInventory::write($invenId, $type, $amount, $remark, $date);

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

    public function showByDate($date){
        $invens = Inventory::get();
        foreach($invens as $index => $inven){
            $invenLog = InventoryLog::where('inventory_id', $inven->id)->where('log_date', $date);
            if($invenLog->count() == 0){
                unset($invens[$index]);
                continue;
            }

            $sumIncrease = InventoryLog::where('inventory_id', $inven->id)->where('type', 'increase')->sum('amount');
            $sumDecrease = InventoryLog::where('inventory_id', $inven->id)->where('type', 'decrease')->sum('amount');
            $diffSum = $sumIncrease - $sumDecrease;

            $invens[$index]['product_detail'] = \App\Product::where('product_id', $inven->product_id)->first()->toArray();
            $invens[$index]['log'] = $invenLog->orderBy('created_at', 'desc')->get()->toArray();
            $invens[$index]['logConclude'] = [
                'sumIncrease' => $sumIncrease,
                'sumDecrease' => $sumDecrease
            ];
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
