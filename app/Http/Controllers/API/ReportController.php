<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::get();
        $result = [];
        foreach($products as $p){
            $inv = Product::find($p->product_id)->inventory;
            if(count($inv) <= 0) continue;
            array_push($result, [
                'product_id' => $p->product_id,
                'product_code' => $p->code,
                'name' => $p->name,
                'quantity' => $inv[0]->quantity,
                'costTotal' => $inv[0]->quantity * $inv[0]->costPrice,
                'saleTotal' => $inv[0]->quantity * $inv[0]->salePrice
            ]);
        }
        print_r($result);
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
}
