<?php
namespace App\Library\_Class {

    use Carbon\Carbon;
    use App\Product;
    use Log;

	class ProductUtil {

        static public function checkQuantity($products)
        {
            $result = [];
            foreach($products as $p) {
                $product = Product::find($p['product_id']);
                $over = $product->inventory->where('quantity', '<', $p['amount'])->count();
                if($over) {
                    array_push($result, [
                        'product' => $product->first(),
                        'input' => $p['amount'],
                        'quantity' => $product->inventory->first()->quantity,
                        'over' => $p['amount'] - $product->inventory->first()->quantity,
                    ]);
                }
            }

            return $result;
        }
    }
}
