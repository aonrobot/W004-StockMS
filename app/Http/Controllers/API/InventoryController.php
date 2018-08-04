<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\Inventory;
use App\Library\Log\Inventory as LogInventory;

class InventoryController extends Controller
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
        //
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

    private function increase($inventory, $quantity, $amount){
        $total = $quantity + $amount;
        $inventory->update([
            'quantity' => $total
        ]);
        return $total;
    }

    private function decrease($inventory, $quantity, $amount){
        if($quantity - $amount < 0) {
            return false;
        } else {
            $total = $quantity - $amount;
            $inventory->update([
                'quantity' => $total
            ]);
            return $total;
        }
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
        $product_id = $id;
        $warehouse_id = $request->input('warehouse_id');
        $type = $request->input('type');
        $amount = $request->input('amount');
        $date = $request->input('date');
        $date = isset($date) ? $date : null;
        $remark = $request->input('remark');
        $remark = isset($remark) ? $remark : null;
        
        // Check product id
        if(!\App\Product::where('product_id', $product_id)->count()) return response()->json(['error' => 'Not found this product id']);

        $inventory = Inventory::where('product_id', $product_id); // TODO: If make warehouse system should (&& where(warehouse_id)) to find inventory
        $invenId = $inventory->first(['id'])->id;
        $quantity = $inventory->get(['quantity'])[0]->quantity;

        try{
            switch ($type) {
                case 'increase' :
                    $total = $this->increase($inventory, $quantity, $amount);
                    LogInventory::write($invenId, $type, $amount, null, null);
                break;
    
                case 'decrease' :
                    $total = $this->decrease($inventory, $quantity, $amount);
                    if($total === false){
                        return response()->json(['updated' => false, 'message' => 'Not enought item']);
                    }
                    LogInventory::write($invenId, $type, $amount, null, null);
                break;

                default:
                    return response()->json(['updated' => false, 'message' => 'Type not support']);
            }
            return response()->json(['updated' => true, 'total' => $total]);
        } catch (\Exception $e){
            Log::error($e);
            return response()->json(['updated' => false]);            
        }
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

    public function getSumQuantity(){
        return response()->json(\App\Inventory::all()->sum('quantity'));
    }

    public function getTotalPrice(){
        $inventorys = \App\Inventory::get(['quantity', 'costPrice', 'salePrice']);
        $costTotal = 0;
        $saleTotal = 0;
        foreach($inventorys as $inv){
            $costTotal += $inv->quantity * $inv->costPrice;
            $saleTotal += $inv->quantity * $inv->salePrice;
        }
        return response()->json([
            'total' => [
                'cost' => $costTotal,
                'sale' => $saleTotal
            ]
        ]);
    }
}
