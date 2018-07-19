<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inventory;

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

    public function addQuantity(Request $request){
        $product_id = $request->input('product_id');
        $amount = $request->input('amount');
        $inventory = Inventory::where('product_id', $product_id);
        $quantity = $inventory->get(['quantity'])[0]->quantity;

        $inventory->update([
            'quantity' => $quantity + $amount
        ]);
        return response()->json(['updated' => true]);

    }

    public function removeQuantity(Request $request){
        $product_id = $request->input('product_id');
        $amount = $request->input('amount');
        $inventory = Inventory::where('product_id', $product_id);
        $quantity = $inventory->get(['quantity'])[0]->quantity;

        if($quantity - $amount < 0){
            return response()->json(['updated' => false, 'message' => 'Not enought item']);
        } else {
            $inventory->update([
                'quantity' => $quantity - $amount
            ]);
            return response()->json(['updated' => true]);
        }
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
