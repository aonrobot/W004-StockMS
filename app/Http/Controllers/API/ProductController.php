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
    //TODO: max, min Level it will create in next sprent
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        foreach($products as $index => $value){
            $inv = Product::find($value->product_id)->inventory;
            if(count($inv) > 0) $products[$index]->inventory = Product::find($value->product_id)->inventory[0];
        }
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
        if (!Product::where('code', $product['code'])->count()){

            $quantity = $productDetail['quantity'];
            $cPrice = $productDetail['costPrice'];
            $sPrice = $productDetail['salePrice'];
            
            //Check category is exist
            if (!ProductCategory::find($product['category_id'])){
                return response()->json(['created' => false, 'message' => 'This category isn\'t exist']);
            }
            else if (!Warehouse::where('warehouse_id', $productDetail['warehouse_id'])->count()){
                return response()->json(['created' => false, 'message' => 'This warehouse isn\'t exist']);
            } else {
                try{
                    $productId = Product::create($product)->product_id;
                    ProductHasWH::create([
                        'product_id' => $productId,
                        'warehouse_id' => $productDetail['warehouse_id']
                    ]);
                    Inventory::create([
                        'product_id' => $productId,
                        'warehouse_id' => $productDetail['warehouse_id'],
                        'quantity' => empty($quantity) ? 0 : intval($quantity),
                        'minLevel' => 0,
                        'maxLevel' => 0,
                        'costPrice' => empty($cPrice) ? 0.0 : floatval($cPrice),
                        'salePrice' => empty($sPrice) ? 0.0 : floatval($sPrice)
                    ]);
                    return response()->json(['created' => true]);

                } catch(\Exception $e) {
                    return response()->json(['created' => false]);
                }
            }
        } else {
            return response()->json(['created' => false, 'message' => 'This product code is exist. please use anathor one']);
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
        $product = Product::where('product_id', $id)->get();
        if($product->count()){
            return response()->json($product);
        } else {
            return response()->json(['message' => 'can\'t found this product']);
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
        $product = $request->input('product');
        $productDetail = $request->input('product.detail');

        try{
            $quantity = $productDetail['quantity'];
            $cPrice = $productDetail['costPrice'];
            $sPrice = $productDetail['salePrice'];

            //Update Product
            Product::where('product_id', $id)->update([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'unitName' => $product['unitName'],
                'description' => $product['description']
            ]);
            //Update Inventory
            Inventory::where('product_id', $id)->update([
                'warehouse_id' => $productDetail['warehouse_id'],
                'quantity' => empty($quantity) ? 0 : intval($quantity),
                'minLevel' => 0,
                'maxLevel' => 0,
                'costPrice' => empty($cPrice) ? 0.0 : floatval($cPrice),
                'salePrice' => empty($sPrice) ? 0.0 : floatval($sPrice)
            ]);
            //Update WH Relation
            ProductHasWH::where('product_id', $id)->update([
                'warehouse_id' => $productDetail['warehouse_id']
            ]);
            return response()->json(['updated' => true]);

        } catch(\Exception $e) {
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
        $product = Product::find($id);
        if($product->count()){
            try{
                Inventory::where('product_id', $id)->delete();
                ProductHasWH::where('product_id', $id)->delete();
                $product->delete();
                return response()->json(['destroyed' => true]);
            } catch(\Exception $e) {
                return response()->json(['destroyed' => false]);
            }
        } else {
            return response()->json(['message' => 'can\'t found this product']);
        }
    }

    /**
     * Generate Product Code
     *
     * @return String
     */
    public function genProductCode(){
        //System code is a string like -> P0001, P0010
        $codes = Product::where('code', 'like', 'P%')->get(['code']);

        $codeList = [];
        foreach($codes as $code){
            $number = intval(substr($code->code, 1));
            array_push($codeList, $number);
        }

        if(count($codeList) <= 0) $codeList = [0];

        return response()->json([
            "code" => 'P' . str_pad((max($codeList) + 1), 4, '0', STR_PAD_LEFT)
        ]);
    }
}
