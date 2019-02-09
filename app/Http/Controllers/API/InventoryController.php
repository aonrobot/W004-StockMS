<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\Inventory;
use App\Library\Log\Inventory as LogInventory;
use App\Library\_Class\Inventory as ClassInventory;
use App\Product;

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
        $invenId = $id;
        //$warehouse_id = $request->input('warehouse_id');

        $type = $request->input('type');
        $amount = $request->input('amount');
        $date = $request->input('date');
        $date = isset($date) ? $date : null;
        //Format Date
        if($date !== null) $date = \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        $remark = $request->input('remark');
        $remark = isset($remark) ? $remark : null;
        
        $result = ClassInventory::ajust($invenId, $type, $amount, null);
        if($result['updated'] == true){
            LogInventory::write($invenId, $type, $amount, $date, $remark);
            return response()->json($result);
        } else {
            return response()->json($result);
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

    public function getSumQuantity()
    {
        // $warehouses = \App\Warehouse::where('user_id', \Auth::id())->get(['warehouse_id']);
        // $sum = 0;
        // foreach ($warehouses as $warehouse)
        // {
        //     $sum += \App\Inventory::where('warehouse_id', $warehouse->warehouse_id)->get()->sum('quantity');
        // }

        $products = Product::where('user_id', \Auth::id())->get();

        $sum = 0;
        foreach ($products as $product)
        {
            $sum += Inventory::where('product_id', $product->product_id)->first(['quantity'])->quantity;
        }
        return response()->json($sum);
    }

    public function getTotalPrice()
    {
        // $inventorys = \App\Inventory::get(['quantity', 'costPrice', 'salePrice']);
        // $costTotal = 0;
        // $saleTotal = 0;
        // foreach($inventorys as $inv){
        //     $costTotal += $inv->quantity * $inv->costPrice;
        //     $saleTotal += $inv->quantity * $inv->salePrice;
        // }

        $products = Product::where('user_id', \Auth::id())->get();

        $costTotal = 0;
        $saleTotal = 0;
        foreach ($products as $product)
        {
            $inv = Inventory::where('product_id', $product->product_id)->first(['quantity', 'costPrice', 'salePrice']);
            $costTotal += $inv->quantity * $inv->costPrice;
            $saleTotal += $inv->quantity * $inv->salePrice;
        }

        return response()->json([
            'total' => [
                'user_id' => \Auth::id(),
                'cost' => $costTotal,
                'sale' => $saleTotal
            ]
        ]);
    }
}