<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

use App\Product;
use App\ProductCategory;
use App\ProductHasWH;
use App\Warehouse;
use App\Inventory;
use App\Library\Log\Inventory as LogInventory;
use App\Library\_Class\ProductUtil;
use \App\Library\_Class\Document;


class ProductController extends Controller
{
    // TODO: max, min Level it will create in next sprent
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

        $warehouseId = $productDetail['warehouse_id'];

        //Check product code
        if (!Product::where('code', $product['code'])->count()){

            $quantity = $productDetail['quantity'];
            $cPrice = $productDetail['costPrice'];
            $sPrice = $productDetail['salePrice'];
            
            //Check category is exist
            if (!ProductCategory::find($product['category_id'])){
                return response()->json(['created' => false, 'message' => 'This category isn\'t exist']);
            }
            else if (!Warehouse::where('warehouse_id', $warehouseId)->count()) {
                return response()->json(['created' => false, 'message' => 'This warehouse isn\'t exist']);
            } else {
                try {

                    $productId = Product::create($product)->product_id;
                    $quantity = empty($quantity) ? 0 : intval($quantity);
                    ProductHasWH::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId
                    ]);

                    $invenId = Inventory::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'quantity' => 0,
                        'minLevel' => 0,
                        'maxLevel' => 0,
                        'costPrice' => empty($cPrice) ? 0.0 : floatval($cPrice),
                        'salePrice' => empty($sPrice) ? 0.0 : floatval($sPrice)
                    ])->id;

                    $result = Document::quickTransfer(null, $warehouseId, [['product_id' => $productId, 'amount' => $quantity]]);
                    if($result['created'] == false) return response()->json(['created' => false, 'message' => 'cant create document']);

                    return response()->json(['created' => true, 'product_id' => $productId, 'inventory_id' => $invenId]);

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

        if($product->count()) {
            $product[0]->inventory = Product::find($product[0]->product_id)->inventory;
            return response()->json($product[0]);
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

        // TODO ทาง ui ต้องส่ง warehouse id มาด้วยเพื่อค้นหา inventory แต่ตอนนี้ไม่เป็นไรเพราะเรามี warehouse เดียว
        $warehouseId = 1; // TODO ลบ fix warehouse ออกด้วย

        try {
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
            $inventory = Inventory::where('product_id', $id)->where('warehouse_id', $warehouseId);
            $oldQuantity = $inventory->first()->quantity;
            $diff = abs($oldQuantity - $quantity);
            $lineItem = [['product_id' => $id, 'amount' => $diff]];
            if($oldQuantity < $quantity) Document::quickTransfer(null, $warehouseId, $lineItem);
            if($oldQuantity > $quantity) Document::quickTransfer($warehouseId, null, $lineItem);

            $inventory->update([
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
                $invens = Inventory::where('product_id', $id);
                $invens->delete();
                ProductHasWH::where('product_id', $id)->delete();
                Product::where('product_id', $id)->delete();
                return response()->json(['destroyed' => true]);
            } catch(\Exception $e) {
                Log::error($e);
                return response()->json(['destroyed' => false]);
            }
        } else {
            return response()->json(['message' => 'can\'t found this product']);
        }
    }

    /**
     * Generate Product Code
     *
     * @return JSON
     */
    public function genProductCode()
    {
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

    /**
     * Get Product Price
     *
     * @return JSON
     */
    public function getProductPrice($id)
    {

        $price = Product::where('product_id', $id)->first()->inventory;
        return response()->json([
            "product_id" => $id,
            "price" => $price[0]['salePrice']
        ]);
    }

    /**
     * Get Product Transaction
     *
     * @return JSON
     */
    public function getTransaction($id)
    {
        $lineItems = \App\DocumentLineItems::where('product_id', $id)->get();

        $allDocuments = [];
        foreach($lineItems as $item)
        {
            
        }
    }

    public function autoComplete(Request $request)
    {
        $q = $request->input('q');

        if ($request->input('searchType') == '0') {
            $products = Product::where('code', 'like', $q)
            ->orWhere('code', 'like', '%' . $q)
            ->orWhere('code', 'like', $q . '%')
            ->orWhere('code', 'like', '%' . $q . '%')->get();
        } elseif ($request->input('searchType') == '0') {
            $products = Product::where('name', 'like', $q)
            ->orWhere('name', 'like', '%' . $q)
            ->orWhere('name', 'like', $q . '%')
            ->orWhere('name', 'like', '%' . $q . '%')->get();
        } else {
            $products = Product::where('name', 'like', $q)
            ->orWhere('name', 'like', '%' . $q)
            ->orWhere('name', 'like', $q . '%')
            ->orWhere('name', 'like', '%' . $q . '%')
            ->orWhere('code', 'like', $q)
            ->orWhere('code', 'like', '%' . $q)
            ->orWhere('code', 'like', $q . '%')
            ->orWhere('code', 'like', '%' . $q . '%')->get();
        }

        foreach($products as $index => $product){
            $products[$index]['sumQuantity'] = ProductUtil::sumQuantity($product['product_id']);
            $products[$index]['costPrice'] = $product->inventory[0]['costPrice'];
            $products[$index]['salePrice'] = $product->inventory[0]['salePrice'];
        }

        return response()->json($products);
    }
}
