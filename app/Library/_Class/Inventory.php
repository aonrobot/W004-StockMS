<?php
namespace App\Library\_Class {
    use Carbon\Carbon;
    use App\InventoryLog;
    use Log;

	class Inventory {

        static private function increase($inventory, $quantity, $amount){
            $total = $quantity + $amount;
            $inventory->update([
                'quantity' => $total
            ]);
            return $total;
        }
    
        static private function decrease($inventory, $quantity, $amount){
            if($quantity - $amount < 0) {
                return false;
            } else {
                $total = $quantity - $amount;
                $inventory->update([
                    'quantity' => $total
                ]);
                return $total;
            }
        }

        static public function ajust($invenId, $type, $amount, $inverse = false){
            // Check inventory id
            if(!\App\Inventory::where('id', $invenId)->count()) return ['error' => 'Not found a product in this warehouse'];
            // Check Amount must isn't negative number
            if($amount < 0) return ['error' => 'Amount must isn\'t negative number!!!'];
            
            $inventory = \App\Inventory::where('id', $invenId);
            $quantity = $inventory->first(['quantity'])->quantity;

            try{
                switch ($type) {
                    case 'increase':
                        $total = (!$inverse) ? self::increase($inventory, $quantity, $amount) : self::decrease($inventory, $quantity, $amount);
                    break;
        
                    case 'decrease':
                        $total = (!$inverse) ? self::decrease($inventory, $quantity, $amount) : self::increase($inventory, $quantity, $amount);
                        if($total === false){
                            return ['updated' => false, 'message' => 'Not enought item'];
                        }
                    break;

                    default:
                        return ['updated' => false, 'message' => 'Type not support'];
                }
                return ['updated' => true, 'total' => $total];
            } catch (\Exception $e){
                Log::error($e);
                return ['updated' => false];            
            }
        }

        static public function ajustInverse($invenId, $type, $amount){
            return self::ajust($invenId, $type, $amount, true);
        }
    }
}
