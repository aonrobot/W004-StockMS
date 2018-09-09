<?php
namespace App\Library\_Class {

    use Carbon\Carbon;
    use App\Product;
    use App\Inventory;
    use Log;

	class ProductUtil {

        static public function checkQuantity($products, $wh_id)
        {
            $result = [];
            foreach($products as $p) {
                $product = Product::find($p['product_id'])->where('user_id', \Auth::id());
                $inventory = Inventory::where('product_id', $p['product_id'])->where('warehouse_id', $wh_id);
                $over = $inventory->where('quantity', '<', $p['amount'])->count();
                if($over) {
                    array_push($result, [
                        'product' => $product->first(),
                        'input' => $p['amount'],
                        'quantity' => $inventory->first()->quantity,
                        'over' => $p['amount'] - $inventory->first()->quantity,
                    ]);
                }
            }

            return $result;
        }

        static public function sumQuantity($product_id){
            return \App\Inventory::where('product_id', $product_id)->sum('quantity');
        }
    }
}
