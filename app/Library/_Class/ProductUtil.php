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

            $newProducts = [];
            $productIndex = [];
            foreach ($products as $index => $p)
            {
                if (!array_key_exists($p['product_id'], $productIndex))
                {
                    array_push($newProducts, $p);
                    $productIndex[$p['product_id']] = $index;

                } else {

                    $index = $productIndex[$p['product_id']];
                    $newProducts[$index]['amount'] += $p['amount'];
                }
            }

            foreach ($newProducts as $p)
            {
                $product = Product::where('product_id', $p['product_id'])->where('user_id', \Auth::id());
                $inventory = Inventory::where('product_id', $p['product_id'])->where('warehouse_id', $wh_id);
                $over = $inventory->where('quantity', '<', $p['amount'])->count();

                if($over) 
                {
                    array_push($result, [
                        'product' => $product->first(),
                        'input' => intval($p['amount']),
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
