<?php
namespace App\Library\_Class {
    use Carbon\Carbon;
    use Log;

	class Inventory {

        static public function increase($product_id, $wh_id, $amount)
        {
            $inventory = \App\Inventory::where('product_id', $product_id)->where('warehouse_id', intval($wh_id));
            $quantity = $inventory->first()->quantity;
            $total = $quantity + intval($amount);
            $inventory->update([
                'quantity' => $total
            ]);
            return $total;
        }
    
        static public function decrease($product_id, $wh_id, $amount)
        {
            $inventory = \App\Inventory::where('product_id', $product_id)->where('warehouse_id', intval($wh_id));
            $quantity = $inventory->first()->quantity;
            if($quantity - $amount < 0) {
                return false;
            } else {
                $total = $quantity - intval($amount);
                $inventory->update([
                    'quantity' => $total
                ]);
                return $total;
            }
        }
    }
}
