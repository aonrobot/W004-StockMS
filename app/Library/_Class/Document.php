<?php
namespace App\Library\_Class {

    use App\Library\_Class\DocumentUtil;
    use App\Library\_Class\Inventory as InventoryClass;
    use App\Library\_Class\ProductUtil;
    use Carbon\Carbon;
    use Log;

	class Document {

        static public function create($type, $detail, $lineitems)
        {
            try
            {
                //if ui sent empty number it will auto gen
                if ($detail['number'] == "") {
                    $detail['number'] = DocumentUtil::genDocNumber($type);
                }
                if (!isset($detail['status']) || $detail['status'] == "") {
                    $detail['status'] = "create";
                }

                $doc_number = $detail['number'];
                $doc_source_wh_id = $detail['source_wh_id'];
                $doc_target_wh_id = $detail['target_wh_id'];

                switch($type)
                {
                    /*

                        ████████╗██████╗  █████╗ ███╗   ██╗███████╗███████╗███████╗██████╗ 
                        ╚══██╔══╝██╔══██╗██╔══██╗████╗  ██║██╔════╝██╔════╝██╔════╝██╔══██╗
                           ██║   ██████╔╝███████║██╔██╗ ██║███████╗█████╗  █████╗  ██████╔╝
                           ██║   ██╔══██╗██╔══██║██║╚██╗██║╚════██║██╔══╝  ██╔══╝  ██╔══██╗
                           ██║   ██║  ██║██║  ██║██║ ╚████║███████║██║     ███████╗██║  ██║
                           ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝╚══════╝╚═╝     ╚══════╝╚═╝  ╚═╝

                            swh_id = source warehouse id
                            twh_id = target warehouse id

                            typeName                     source          target      inventory
                            ___________________________________________________________________________________________________
                            transferIn           ->      null            *           increase target
                            transferOut          ->      *               null        decrease source
                            transferBetween      ->      *               *           decrease from source and increase target
                    */
                    case 'tf' :

                        $detail['customer_id'] = null;
                        $detail['tax_type'] = null;

                        $doc_id = \App\DocumentDetail::create($detail)->id;

                        foreach($lineitems as $item)
                        {
                            $product_id = $item['product_id'];
                            $amount = $item['amount'];

                            if ($amount < 0) continue;

                            $warehouseId = 0;
                            if ($doc_source_wh_id == null && $doc_target_wh_id != null) {
                                InventoryClass::increase($product_id, $doc_target_wh_id, $amount);

                                $warehouseId = $doc_target_wh_id;

                            } elseif ($doc_source_wh_id != null && $doc_target_wh_id == null) {
                                InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);

                                $warehouseId = $doc_source_wh_id;

                            } elseif ($doc_source_wh_id != null && $doc_target_wh_id != null) {
                                InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
                                InventoryClass::increase($product_id, $doc_target_wh_id, $amount);

                                $warehouseId = $doc_target_wh_id;
                            } else {
                                return ['created' => false, 'message' => 'source warehouse or target warehouse id could not [null]'];
                            }

                            $product_price = \App\Inventory::where('product_id', $product_id)
                                ->where('warehouse_id', $warehouseId)->first(['costPrice'])->costPrice;

                            $lineItemId = \App\DocumentLineItems::create([
                                "document_id" => $doc_id,
                                "product_id" => $product_id,
                                "amount" => $amount,
                                "price" => 0,
                                "discount" => 0,
                                "total" => 0
                            ])->id;

                            $currentQuantity = ProductUtil::sumQuantity($product_id);
                            //\App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);
                            $transacResult = self::transaction($lineItemId, $currentQuantity);
                            if ($transacResult  == false) {
                                return ['created' => false, 'message' => 'canot create transaction!!'];
                            }
                        }

                    break;

                    /*

                        ██╗ ███╗   ██╗ ██╗   ██╗  ██████╗  ██╗  ██████╗ ███████╗
                        ██║ ████╗  ██║ ██║   ██║ ██╔═══██╗ ██║ ██╔════╝ ██╔════╝
                        ██║ ██╔██╗ ██║ ██║   ██║ ██║   ██║ ██║ ██║      █████╗  
                        ██║ ██║╚██╗██║ ╚██╗ ██╔╝ ██║   ██║ ██║ ██║      ██╔══╝  
                        ██║ ██║ ╚████║  ╚████╔╝  ╚██████╔╝ ██║ ╚██████╗ ███████╗
                        ╚═╝ ╚═╝  ╚═══╝   ╚═══╝    ╚═════╝  ╚═╝  ╚═════╝ ╚══════╝

                    */
                    case 'inv':

                        $checkResult = ProductUtil::checkQuantity($lineitems, $doc_source_wh_id);
                        if (count($checkResult) > 0) return [
                            'created' => false,
                            'message' => $checkResult
                        ];

                        if (empty($detail['tax_type'])) {
                            $detail['tax_type'] = 'withoutTax';
                        }

                        if ($doc_source_wh_id == null) {
                            if ($doc_target_wh_id !== null) {
                                $doc_source_wh_id = $doc_target_wh_id;
                                $doc_target_wh_id = null;

                                $detail['source_wh_id'] = $doc_source_wh_id;
                                $detail['target_wh_id'] = $doc_target_wh_id;
                            } else {
                                return ['created' => false, 'message' => 'source warehouse id could not [null]'];
                            }
                        }

                        $doc_id = \App\DocumentDetail::create($detail)->id;
                        
                        foreach ($lineitems as $item)
                        {
                            $product_id = $item['product_id'];

                            $amount = $item['amount'];
                            $price = $item['price'];
                            $discount = $item['discount'];

                            $item['document_id'] = $doc_id;
                            $item['total'] = ($price * $amount) - $discount;

                            $lineItemId = \App\DocumentLineItems::create($item)->id;

                            InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);

                            $currentQuantity = ProductUtil::sumQuantity($product_id);
                            //\App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);
                            $transacResult = self::transaction($lineItemId, $currentQuantity);
                            if ($transacResult == false) {
                                return ['created' => false, 'message' => 'canot create transaction!!'];
                            }
                        }
                        
                    break;

                    /*

                        ██████╗ ██╗   ██╗██████╗  ██████╗██╗  ██╗ █████╗ ███████╗███████╗     ██████╗ ██████╗ ██████╗ ███████╗██████╗ 
                        ██╔══██╗██║   ██║██╔══██╗██╔════╝██║  ██║██╔══██╗██╔════╝██╔════╝    ██╔═══██╗██╔══██╗██╔══██╗██╔════╝██╔══██╗
                        ██████╔╝██║   ██║██████╔╝██║     ███████║███████║███████╗█████╗      ██║   ██║██████╔╝██║  ██║█████╗  ██████╔╝
                        ██╔═══╝ ██║   ██║██╔══██╗██║     ██╔══██║██╔══██║╚════██║██╔══╝      ██║   ██║██╔══██╗██║  ██║██╔══╝  ██╔══██╗
                        ██║     ╚██████╔╝██║  ██║╚██████╗██║  ██║██║  ██║███████║███████╗    ╚██████╔╝██║  ██║██████╔╝███████╗██║  ██║
                        ╚═╝      ╚═════╝ ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═╝╚═════╝ ╚══════╝╚═╝  ╚═╝

                    */
                    case 'po':

                        if (empty($detail['tax_type'])) {
                            $detail['tax_type'] = 'withoutTax';
                        }

                        if ($doc_target_wh_id == null) {
                            if ($doc_source_wh_id !== null) {
                                $doc_target_wh_id = $doc_source_wh_id;
                                $doc_source_wh_id = null;

                                $detail['source_wh_id'] = $doc_source_wh_id;
                                $detail['target_wh_id'] = $doc_target_wh_id;
                            } else {
                                return ['created' => false, 'message' => 'target warehouse id could not [null]'];
                            }
                        }

                        $doc_id = \App\DocumentDetail::create($detail)->id;
                        
                        foreach ($lineitems as $item)
                        {
                            $product_id = $item['product_id'];

                            $amount = $item['amount'];
                            $price = $item['price'];
                            $discount = $item['discount'];

                            $item['document_id'] = $doc_id;
                            $item['total'] = ($price * $amount) - $discount;

                            $lineItemId = \App\DocumentLineItems::create($item)->id;

                            InventoryClass::increase($product_id, $doc_target_wh_id, $amount);

                            $currentQuantity = ProductUtil::sumQuantity($product_id);
                            //\App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);
                            $transacResult = self::transaction($lineItemId, $currentQuantity);
                            if ($transacResult == false) {
                                return ['created' => false, 'message' => 'canot create transaction!!'];
                            }
                        }
                        
                    break;

                    default:
                        return ['created' => false, 'message' => 'Document type not support'];
                }

                return [
                    'created' => true,
                    'message' => 'create transfer',
                    'document_id' => $doc_id,
                    'document_number' => \App\DocumentDetail::find($doc_id)->number
                ];

            } catch (\Exception $e) {

                if (isset($doc_id)) {
                    \App\DocumentDetail::find($doc_id)->delete();
                }

                Log::error($e);

                return ['created' => false, 'message' => 'Error to create document please contact engineer.'];
            }

            //Update status to complete
            \App\DocumentDetail::where('id', $doc_id)->update(['status' => 'complete']);
        }

        static public function update($id, $detail, $lineitems)
        {
            try
            {

                $documentDetail     = \App\DocumentDetail::where('id', $id);
                $documentLineItems  = \App\DocumentLineItems::where('document_id', $id);

                $doc_number         = $documentDetail->first(['number'])->number;
                $doc_source_wh_id   = $documentDetail->first(['source_wh_id'])->source_wh_id;
                $doc_target_wh_id   = $documentDetail->first(['target_wh_id'])->target_wh_id;

                $type               = $documentDetail->first(['type'])->type;

                switch($type)
                {
                    /*
                        Update
                        ██╗ ███╗   ██╗ ██╗   ██╗  ██████╗  ██╗  ██████╗ ███████╗
                        ██║ ████╗  ██║ ██║   ██║ ██╔═══██╗ ██║ ██╔════╝ ██╔════╝
                        ██║ ██╔██╗ ██║ ██║   ██║ ██║   ██║ ██║ ██║      █████╗  
                        ██║ ██║╚██╗██║ ╚██╗ ██╔╝ ██║   ██║ ██║ ██║      ██╔══╝  
                        ██║ ██║ ╚████║  ╚████╔╝  ╚██████╔╝ ██║ ╚██████╗ ███████╗
                        ╚═╝ ╚═╝  ╚═══╝   ╚═══╝    ╚═════╝  ╚═╝  ╚═════╝ ╚══════╝

                    */
                    case 'inv':

                        $documentDetail->update($detail);

                        $itemsExpandAmount = [];
                        foreach($lineitems as $item)
                        {
                            // $item['id'] == DocumentLineItems id
                            $oldAmount = \App\DocumentLineItems::where('id', $item['id'])->first(['amount'])->amount;
                            //function checkQuantity use product_id
                            $oldProductId = \App\DocumentLineItems::where('id', $item['id'])->first(['product_id'])->product_id;
                            $item['product_id'] = $oldProductId;

                            $amount = $oldAmount;
                            if (isset($item['amount'])) $amount = $item['amount'];

                            if ($oldAmount < $amount) {
                                $item['amount'] = abs($amount - $oldAmount);
                                array_push($itemsExpandAmount, $item);
                            }

                            // TODO if $oldAmount == $amount unset $item to reduce time
                        }

                        $checkResult = ProductUtil::checkQuantity($itemsExpandAmount, $doc_source_wh_id);
                        if (count($checkResult) > 0) return [
                            'updated' => false,
                            'message' => $checkResult
                        ];

                        foreach($lineitems as $item)
                        {
                            $oldItem = \App\DocumentLineItems::where('id', $item['id'])->where('document_id', $id)->first(['id', 'product_id', 'amount', 'price', 'discount', 'created_at']);
                            
                            if($oldItem == null) return ['updated' => false, 'message' => 'Error this lineitems isnt exist'];

                            $oldId = $oldItem->id;
                            $oldProductId= $oldItem->product_id;
                            $oldAmount = $oldItem->amount;
                            $oldPrice = $oldItem->price;
                            $oldDiscount = $oldItem->discount;
                            $oldCreateAt = $oldItem->created_at;

                            unset($item['id']);
                            
                            // set amount, price, discount

                            $amount = $oldAmount;
                            if (isset($item['amount'])) $amount = $item['amount'];
                            $diff = abs($amount - $oldAmount);

                            $price = $oldPrice;
                            if (isset($item['price'])) $price = $item['price'];

                            $discount = $oldDiscount;
                            if (isset($item['discount'])) $discount = $item['discount'];

                            $item['total'] = ($amount * $price) - $discount;

                            // Update documentLineItems
                            $documentLineItems->where('id', $oldId)->update($item);

                            // Adjust Inventory
                            // expand amount
                            if ($oldAmount < $amount) {
                                $currentQuantity = InventoryClass::decrease($oldProductId, $doc_source_wh_id, $diff);
                                // Method 3
                                // self::quickTransfer($doc_source_wh_id, null, [['product_id' => $oldProductId, 'amount' => $diff]], "", $id);
                                // \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->decrement('quantity', $diff);
                                
                                // Method 2
                                // self::quickTransfer($doc_source_wh_id, null, [['product_id' => $oldProductId, 'amount' => $diff]], "", $id);
                                // \App\DocumentLineItems::where('id', $oldId)->where('document_id', $id)->decrement('quantity', $diff);

                                $transacResult = self::transaction($oldId, $currentQuantity);
                                if ($transacResult == false) {
                                    return ['created' => false, 'message' => 'canot create transaction!!'];
                                }

                            } elseif ($oldAmount > $amount) {

                                $currentQuantity = InventoryClass::increase($oldProductId, $doc_source_wh_id, $diff);

                                // Method 3
                                // self::quickTransfer(null, $doc_source_wh_id, [['product_id' => $oldProductId, 'amount' => $diff]], "", $id);
                                // \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->increment('quantity', $diff);

                                // Method 2
                                // self::quickTransfer(null, $doc_source_wh_id, [['product_id' => $oldProductId, 'amount' => $diff]], "", $id);
                                // \App\DocumentLineItems::where('id', $oldId)->where('document_id', $id)->increment('quantity', $diff);

                                $transacResult = self::transaction($oldId, $currentQuantity);
                                if ($transacResult == false) {
                                    return ['created' => false, 'message' => 'canot create transaction!!'];
                                }
                            }
                        }
                        
                    break;

                    /*
                        Update
                        ██████╗ ██╗   ██╗██████╗  ██████╗██╗  ██╗ █████╗ ███████╗███████╗     ██████╗ ██████╗ ██████╗ ███████╗██████╗ 
                        ██╔══██╗██║   ██║██╔══██╗██╔════╝██║  ██║██╔══██╗██╔════╝██╔════╝    ██╔═══██╗██╔══██╗██╔══██╗██╔════╝██╔══██╗
                        ██████╔╝██║   ██║██████╔╝██║     ███████║███████║███████╗█████╗      ██║   ██║██████╔╝██║  ██║█████╗  ██████╔╝
                        ██╔═══╝ ██║   ██║██╔══██╗██║     ██╔══██║██╔══██║╚════██║██╔══╝      ██║   ██║██╔══██╗██║  ██║██╔══╝  ██╔══██╗
                        ██║     ╚██████╔╝██║  ██║╚██████╗██║  ██║██║  ██║███████║███████╗    ╚██████╔╝██║  ██║██████╔╝███████╗██║  ██║
                        ╚═╝      ╚═════╝ ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═╝╚═════╝ ╚══════╝╚═╝  ╚═╝

                    */
                    case 'po':

                        $documentDetail->update($detail);

                        $itemsExpandAmount = [];
                        foreach($lineitems as $item){
                            // $item['id'] == DocumentLineItems id
                            $oldAmount = \App\DocumentLineItems::where('id', $item['id'])->first(['amount'])->amount;

                            //function checkQuantity use product_id
                            $oldProductId = \App\DocumentLineItems::where('id', $item['id'])->first(['product_id'])->product_id;
                            $item['product_id'] = $oldProductId;

                            $amount = $oldAmount;
                            if (isset($item['amount'])) $amount = $item['amount'];

                            if ($oldAmount > $amount) {
                                $item['amount'] = abs($amount - $oldAmount);
                                array_push($itemsExpandAmount, $item);
                            }

                            // TODO if $oldAmount == $amount unset $item to reduce time
                        }

                        $checkResult = ProductUtil::checkQuantity($itemsExpandAmount, $doc_target_wh_id);
                        if (count($checkResult) > 0) return [
                            'updated' => false,
                            'message' => $checkResult
                        ];

                        foreach($lineitems as $item){
                            $oldItem = \App\DocumentLineItems::where('id', $item['id'])->first(['id', 'product_id', 'amount', 'price', 'discount', 'created_at']);
                            $oldId = $oldItem->id;
                            $oldProductId= $oldItem->product_id;
                            $oldAmount = $oldItem->amount;
                            $oldPrice = $oldItem->price;
                            $oldDiscount = $oldItem->discount;
                            $oldCreateAt = $oldItem->created_at;

                            unset($item['id']);

                            $amount = $oldAmount;
                            if (isset($item['amount'])) $amount = $item['amount'];
                            $diff = abs($amount - $oldAmount);

                            $price = $oldPrice;
                            if (isset($item['price'])) $price = $item['price'];

                            $discount = $oldDiscount;
                            if (isset($item['discount'])) $discount = $item['discount'];

                            $item['total'] = ($amount * $price) - $discount;

                            $documentLineItems->where('id', $oldId)->update($item);

                            // expand amount
                            if ($oldAmount > $amount) {
                                $currentQuantity = InventoryClass::decrease($oldProductId, $doc_target_wh_id, $diff);
                                // if ($currentQuantity != false) {
                                //     \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->decrement('quantity', $diff);
                                // }
                                //self::quickTransfer($doc_target_wh_id, null, [['product_id' => $oldProductId, 'amount' => $diff]]);
                                $transacResult = self::transaction($oldId, $currentQuantity);
                                if ($transacResult == false) {
                                    return ['created' => false, 'message' => 'canot create transaction!!'];
                                }
                            } elseif ($oldAmount < $amount) {
                                $currentQuantity = InventoryClass::increase($oldProductId, $doc_target_wh_id, $diff);
                                // if ($currentQuantity != false) {
                                //     \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->increment('quantity', $diff);
                                // }
                                //self::quickTransfer(null, $doc_target_wh_id, [['product_id' => $oldProductId, 'amount' => $diff]]);
                                $transacResult = self::transaction($oldId, $currentQuantity);
                                if ($transacResult == false) {
                                    return ['created' => false, 'message' => 'canot create transaction!!'];
                                }
                            }
                        }
                        
                    break;

                    default:
                        return ['updated' => false, 'message' => 'Document type not support'];
                }

                return [
                    'updated' => true,
                    'message' => 'updated ' . $type,
                    'document_id' => $id,
                    'document_number' => \App\DocumentDetail::find($id)->number
                ];

            } catch (\Exception $e) {

                Log::error($e);

                return ['updated' => false, 'message' => 'Error to update document please contact admin.'];
            }
        }

        static public function delete($id)
        {
            try
            {

                $documentDetail     = \App\DocumentDetail::where('id', $id);
                $documentLineItems  = \App\DocumentLineItems::where('document_id', $id)->get();

                $doc_number         = $documentDetail->first(['number'])->number;
                $doc_source_wh_id   = $documentDetail->first(['source_wh_id'])->source_wh_id;
                $doc_target_wh_id   = $documentDetail->first(['target_wh_id'])->target_wh_id;

                $type               = $documentDetail->first(['type'])->type;

                switch($type)
                {
                    /*
                        Delete
                        ████████╗ ██████╗   █████╗  ███╗   ██╗ ███████╗ ███████╗ ███████╗ ██████╗ 
                        ╚══██╔══╝ ██╔══██╗ ██╔══██╗ ████╗  ██║ ██╔════╝ ██╔════╝ ██╔════╝ ██╔══██╗
                           ██║    ██████╔╝ ███████║ ██╔██╗ ██║ ███████╗ █████╗   █████╗   ██████╔╝
                           ██║    ██╔══██╗ ██╔══██║ ██║╚██╗██║ ╚════██║ ██╔══╝   ██╔══╝   ██╔══██╗
                           ██║    ██║  ██║ ██║  ██║ ██║ ╚████║ ███████║ ██║      ███████╗ ██║  ██║
                           ╚═╝    ╚═╝  ╚═╝ ╚═╝  ╚═╝ ╚═╝  ╚═══╝ ╚══════╝ ╚═╝      ╚══════╝ ╚═╝  ╚═╝

                            typeName                     source          target      inventory
                            ___________________________________________________________________________________________________
                            transferIn           ->      null            *           increase target
                            transferOut          ->      *               null        decrease source
                            transferBetween      ->      *               *           decrease from source and increase target
                    */
                    case 'tf':

                        foreach ($documentLineItems as $item) {
                            $lineitem_id = $item['id'];
                            $product_id = $item['product_id'];
                            $amount = $item['amount'];
                            $createAt = $item['created_at'];

                            \App\Transaction::where('lineitem_id')->delete();

                            if ($doc_source_wh_id == null && $doc_target_wh_id != null) {
                                $currentQuantity = InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->decrement('quantity', $amount);
                                }
                            } elseif ($doc_source_wh_id != null && $doc_target_wh_id == null) {
                                $currentQuantity = InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->increment('quantity', $amount);
                                }
                            } elseif ($doc_source_wh_id == null && $doc_target_wh_id == null) {
                                InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                                InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                            } else {
                                return ['deleted' => false, 'message' => 'source warehouse or target warehouse id could not [null]'];                                
                            }
                        }

                        $documentDetail->delete();
                        
                    break;

                    /*
                        Delete
                        ██╗ ███╗   ██╗ ██╗   ██╗  ██████╗  ██╗  ██████╗ ███████╗
                        ██║ ████╗  ██║ ██║   ██║ ██╔═══██╗ ██║ ██╔════╝ ██╔════╝
                        ██║ ██╔██╗ ██║ ██║   ██║ ██║   ██║ ██║ ██║      █████╗  
                        ██║ ██║╚██╗██║ ╚██╗ ██╔╝ ██║   ██║ ██║ ██║      ██╔══╝  
                        ██║ ██║ ╚████║  ╚████╔╝  ╚██████╔╝ ██║ ╚██████╗ ███████╗
                        ╚═╝ ╚═╝  ╚═══╝   ╚═══╝    ╚═════╝  ╚═╝  ╚═════╝ ╚══════╝

                    */
                    case 'inv':

                        foreach ($documentLineItems as $item) {
                            
                            $product_id = $item['product_id'];
                            $amount = $item['amount'];
                            $createAt = $item['created_at'];

                            $currentQuantity = InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                            if ($currentQuantity != false) {
                                \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->increment('quantity', $amount);
                            }
                        }

                        $documentDetail->delete();
                        
                    break;

                    /*
                        Delete
                        ██████╗ ██╗   ██╗██████╗  ██████╗██╗  ██╗ █████╗ ███████╗███████╗     ██████╗ ██████╗ ██████╗ ███████╗██████╗ 
                        ██╔══██╗██║   ██║██╔══██╗██╔════╝██║  ██║██╔══██╗██╔════╝██╔════╝    ██╔═══██╗██╔══██╗██╔══██╗██╔════╝██╔══██╗
                        ██████╔╝██║   ██║██████╔╝██║     ███████║███████║███████╗█████╗      ██║   ██║██████╔╝██║  ██║█████╗  ██████╔╝
                        ██╔═══╝ ██║   ██║██╔══██╗██║     ██╔══██║██╔══██║╚════██║██╔══╝      ██║   ██║██╔══██╗██║  ██║██╔══╝  ██╔══██╗
                        ██║     ╚██████╔╝██║  ██║╚██████╗██║  ██║██║  ██║███████║███████╗    ╚██████╔╝██║  ██║██████╔╝███████╗██║  ██║
                        ╚═╝      ╚═════╝ ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═╝╚═════╝ ╚══════╝╚═╝  ╚═╝

                    */
                    case 'po':

                        foreach ($documentLineItems as $item) {
                                
                            $product_id = $item['product_id'];
                            $amount = $item['amount'];
                            $createAt = $item['created_at'];

                            $currentQuantity = InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                            if ($currentQuantity != false) {
                                \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->decrement('quantity', $amount);
                            }
                        }

                        $documentDetail->delete();
                        
                    break;

                    default:
                        return ['deleted' => false, 'message' => 'Document type not support'];
                }

                return [
                    'updated' => true,
                    'message' => 'deleted ' . $type,
                    'document_id' => $id
                ];

            } catch (\Exception $e) {

                Log::error($e);

                return ['deleted' => false, 'message' => 'Error to delete document please contact admin.'];
            }
        }

        static public function deleteLineItem($lineItemId)
        {
            try
            {
                //Document Line Item
                $documentLineItems  = \App\DocumentLineItems::where('id', $lineItemId);
                $document_id        = $documentLineItems->first()->document_id;
                $product_id         = $documentLineItems->first()->product_id;
                $amount             = $documentLineItems->first()->amount;
                $createAt           = $documentLineItems->first()->created_at;

                //Docuemnt Detail
                $documentDetail     = \App\DocumentDetail::where('id', $document_id)->first();
                $type               = $documentDetail->type;
                $doc_source_wh_id   = $documentDetail->source_wh_id;
                $doc_target_wh_id   = $documentDetail->target_wh_id;

                switch($type)
                {
                    /*
                        Delete line item
                        ████████╗ ██████╗   █████╗  ███╗   ██╗ ███████╗ ███████╗ ███████╗ ██████╗ 
                        ╚══██╔══╝ ██╔══██╗ ██╔══██╗ ████╗  ██║ ██╔════╝ ██╔════╝ ██╔════╝ ██╔══██╗
                           ██║    ██████╔╝ ███████║ ██╔██╗ ██║ ███████╗ █████╗   █████╗   ██████╔╝
                           ██║    ██╔══██╗ ██╔══██║ ██║╚██╗██║ ╚════██║ ██╔══╝   ██╔══╝   ██╔══██╗
                           ██║    ██║  ██║ ██║  ██║ ██║ ╚████║ ███████║ ██║      ███████╗ ██║  ██║
                           ╚═╝    ╚═╝  ╚═╝ ╚═╝  ╚═╝ ╚═╝  ╚═══╝ ╚══════╝ ╚═╝      ╚══════╝ ╚═╝  ╚═╝

                            typeName                     source          target      inventory
                            ___________________________________________________________________________________________________
                            transferIn           ->      null            *           increase target
                            transferOut          ->      *               null        decrease source
                            transferBetween      ->      *               *           decrease from source and increase target
                    */
                    case 'tf':

                        $amount = $item['amount'];
                        $createAt = $item['created_at'];

                        if ($doc_source_wh_id == null && $doc_target_wh_id != null) {
                            $currentQuantity = InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                            if ($currentQuantity != false) {
                                \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $createAt)->decrement('quantity', $amount);
                            }
                        } elseif ($doc_source_wh_id != null && $doc_target_wh_id == null) {
                            $currentQuantity = InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                            if ($currentQuantity != false) {
                                \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $createAt)->increment('quantity', $amount);
                            }
                        } elseif ($doc_source_wh_id == null && $doc_target_wh_id == null) {
                            InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                            InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                        } else {
                            return ['deleted' => false, 'message' => 'source warehouse or target warehouse id could not [null]'];                                
                        }
                        
                        $documentLineItems->delete();
                        
                    break;

                    /*
                        Delete line item
                        ██╗ ███╗   ██╗ ██╗   ██╗  ██████╗  ██╗  ██████╗ ███████╗
                        ██║ ████╗  ██║ ██║   ██║ ██╔═══██╗ ██║ ██╔════╝ ██╔════╝
                        ██║ ██╔██╗ ██║ ██║   ██║ ██║   ██║ ██║ ██║      █████╗  
                        ██║ ██║╚██╗██║ ╚██╗ ██╔╝ ██║   ██║ ██║ ██║      ██╔══╝  
                        ██║ ██║ ╚████║  ╚████╔╝  ╚██████╔╝ ██║ ╚██████╗ ███████╗
                        ╚═╝ ╚═╝  ╚═══╝   ╚═══╝    ╚═════╝  ╚═╝  ╚═════╝ ╚══════╝

                    */
                    case 'inv':

                        $currentQuantity = InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                        if ($currentQuantity != false) {
                            \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->increment('quantity', $amount);
                        }

                        $documentLineItems->delete();
                        
                    break;

                    /*
                        Delete line item
                        ██████╗ ██╗   ██╗██████╗  ██████╗██╗  ██╗ █████╗ ███████╗███████╗     ██████╗ ██████╗ ██████╗ ███████╗██████╗ 
                        ██╔══██╗██║   ██║██╔══██╗██╔════╝██║  ██║██╔══██╗██╔════╝██╔════╝    ██╔═══██╗██╔══██╗██╔══██╗██╔════╝██╔══██╗
                        ██████╔╝██║   ██║██████╔╝██║     ███████║███████║███████╗█████╗      ██║   ██║██████╔╝██║  ██║█████╗  ██████╔╝
                        ██╔═══╝ ██║   ██║██╔══██╗██║     ██╔══██║██╔══██║╚════██║██╔══╝      ██║   ██║██╔══██╗██║  ██║██╔══╝  ██╔══██╗
                        ██║     ╚██████╔╝██║  ██║╚██████╗██║  ██║██║  ██║███████║███████╗    ╚██████╔╝██║  ██║██████╔╝███████╗██║  ██║
                        ╚═╝      ╚═════╝ ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═╝╚═════╝ ╚══════╝╚═╝  ╚═╝

                    */
                    case 'po':

                        $currentQuantity = InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
                        if ($currentQuantity != false) {
                            \App\DocumentLineItems::where('product_id', $product_id)->where('created_at', '>=', $createAt)->decrement('quantity', $amount);
                        }

                        $documentLineItems->delete();
                        
                    break;

                    default:
                        return ['deleted' => false, 'message' => 'Document type not support'];
                }

                return [
                    'updated' => true,
                    'message' => 'deleted line item ' . $type,
                    'document_id' => $document_id,
                    'lineItem_id' => $lineItemId
                ];

            } catch (\Exception $e) {

                Log::error($e);

                return ['deleted' => false, 'message' => 'Error to delete document please contact admin.'];
            }
        }

        static public function quickTransfer($source_wh_id, $target_wh_id, $lineitems, $comment = "", $ref_id = null)
        {
            $detail = [
                "number" => DocumentUtil::genDocNumber('tf'),
                "customer_id" => null,
                "ref_id" => $ref_id,
                "source_wh_id" => $source_wh_id,
                "target_wh_id" => $target_wh_id,
                "type" => "tf",
                "tax_type" => "",
                "comment" => $comment,
                "status" => "complete",
                "date" => Carbon::now()->toDateString()
            ];

            // lineItem -> [['product_id' => $productId, 'amount' => $quantity]]

            $result = self::create('tf', $detail, $lineitems);

            return $result;
        }

        static public function transaction($lineitem_id, $balance)
        {
            try 
            {
                $docLineitem = \App\DocumentLineItems::where('id', $lineitem_id)->first();
                $document_id = $docLineitem->document_id;

                $docDetail = \App\DocumentDetail::where('id', $document_id)->first();
                $type = $docDetail->type;
                $status = $docDetail->status;
                $source_wh_id = $docDetail->source_wh_id;
                $target_wh_id = $docDetail->target_wh_id;

                \App\Transaction::create([
                    'document_id' => $document_id,
                    'lineitem_id' => $lineitem_id,
                    'type' => $type,
                    'status' => $status,
                    'source_wh_id' => $source_wh_id,
                    'target_wh_id' => $target_wh_id,
                    'balance' => $balance,
                ]);

                return true;

            } catch (\Exception $e) {

                Log::error($e);
                return false;
            }
        }
    }
}
