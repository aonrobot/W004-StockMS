<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Product;
use App\ProductCategory;
use App\ProductHasWH;
use App\Warehouse;
use App\Inventory;

class ProductController extends Controller
{
    /**
     * Generate Product Code
     *
     * @return String
     */
    public function getProductCode(){
        //System code is a string like -> P0001, P0010
        $codes = Product::where('code', 'like', 'P%')->get(['code']);

        $codeList = [];
        foreach($codes as $code){
            $number = intval(substr($code->code, 1));
            array_push($codeList, $number);
        }

        return response()->json([
            "code" => 'P' . str_pad((max($codeList) + 1), 4, '0', STR_PAD_LEFT)
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $request->input('product');
        $productDetail = $request->input('product.detail');

        //Check product code
        if(!Product::where('code', $product['code'])->count()) {
            //Check category is exist
            if(!ProductCategory::find($product['category_id'])) {
                return response()->json(['message' => 'Category isn\'t exist']);
            }
            else if(!Warehouse::where('warehouse_id', $productDetail['warehouse_id'])->count()){
                return response()->json(['message' => 'Warehouse isn\'t exist']);
            } else {
                $productId = Product::create($product)->product_id;
                ProductHasWH::create([
                    'product_id' => $productId,
                    'warehouse_id' => $productDetail['warehouse_id']
                ]);
                Inventory::create([
                    'product_id' => $productId,
                    'warehouse_id' => $productDetail['warehouse_id'],
                    'quantity' => $productDetail['quantity'],
                    'minLevel' => 0,
                    'maxLevel' => 0,
                    'costPrice' => $productDetail['costPrice'],
                    'salePrice' => $productDetail['salePrice']
                ]);
                return response()->json([
                    'product_id' => $productId
                ]);
            }
        } else {
            return response()->json(['message' => 'This product code is exist. please use anathor one']);
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
