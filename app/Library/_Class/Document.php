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

                            $lineItemId = \App\DocumentLineItems::create([
                                "document_id" => $doc_id,
                                "product_id" => $product_id,
                                "amount" => $amount
                            ])->id;

                            if ($doc_source_wh_id == null && $doc_target_wh_id != null) {
                                InventoryClass::increase($product_id, $doc_target_wh_id, $amount);
                            } elseif ($doc_source_wh_id != null && $doc_target_wh_id == null) {
                                InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
                            } else {
                                InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
                                InventoryClass::increase($product_id, $doc_target_wh_id, $amount);
                            }

                            $currentQuantity = ProductUtil::sumQuantity($product_id);
                            \App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);
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

                        $checkResult = ProductUtil::checkQuantity($lineitems);
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
                            \App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);

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
                            \App\DocumentLineItems::where('id', $lineItemId)->update(['quantity' => $currentQuantity]);
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
                        // $status = $detail['status'];

                        // $documentDetail->update($detail);

                        // print_r($lineitems);
                        // return 0;

                        // if ($status == 'cancel') {
                        //     foreach ($lineitems as $item) {
                        //         $oldAmount = \App\DocumentLineItems::where('id', $item['id'])->first(['amount'])->amount;
                        //         $oldProductId = \App\DocumentLineItems::where('id', $item['id'])->first(['product_id'])->amount;
                        //         $oldCreateAt = \App\DocumentLineItems::where('id', $item['id'])->first(['created_at'])->amount;

                        //         if ($doc_source_wh_id == null && $doc_target_wh_id != null) {
                        //             $currentQuantity = InventoryClass::decrease($oldProductId, $doc_target_wh_id, $oldAmount);
                        //             if ($currentQuantity != false) {
                        //                 \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->decrement('quantity', $diff);
                        //             }
                        //         } elseif ($doc_source_wh_id != null && $doc_target_wh_id == null) {
                        //             $currentQuantity = InventoryClass::increase($oldProductId, $doc_source_wh_id, $oldAmount);
                        //             if ($currentQuantity != false) {
                        //                 \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->increment('quantity', $diff);
                        //             }
                        //         } else {
                        //             InventoryClass::increase($oldProductId, $doc_source_wh_id, $oldAmount);
                        //             InventoryClass::decrease($oldProductId, $doc_target_wh_id, $oldAmount);
                        //         }
                        //     }
                        // }
                        
                    break;

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
                        foreach($lineitems as $item){
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

                        $checkResult = ProductUtil::checkQuantity($itemsExpandAmount);
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
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->decrement('quantity', $diff);
                                }
                            } elseif ($oldAmount > $amount) {
                                $currentQuantity = InventoryClass::increase($oldProductId, $doc_source_wh_id, $diff);
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->increment('quantity', $diff);
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

                        $checkResult = ProductUtil::checkQuantity($itemsExpandAmount);
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
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->decrement('quantity', $diff);
                                }
                            } elseif ($oldAmount < $amount) {
                                $currentQuantity = InventoryClass::increase($oldProductId, $doc_target_wh_id, $diff);
                                if ($currentQuantity != false) {
                                    \App\DocumentLineItems::where('product_id', $oldProductId)->where('created_at', '>=', $oldCreateAt)->increment('quantity', $diff);
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

                            $product_id = $item['product_id'];
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
                            } else {
                                InventoryClass::increase($product_id, $doc_source_wh_id, $amount);
                                InventoryClass::decrease($product_id, $doc_target_wh_id, $amount);
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
                        return ['updated' => false, 'message' => 'Document type not support'];
                }

                return [
                    'updated' => true,
                    'message' => 'updated ' . $type,
                    'document_id' => $id
                ];

            } catch (\Exception $e) {

                Log::error($e);

                return ['updated' => false, 'message' => 'Error to update document please contact admin.'];
            }
        }

        static public function quickTransfer($source_wh_id, $target_wh_id, $lineitems, $comment = "")
        {
            $detail = [
                "number" => DocumentUtil::genDocNumber('tf'),
                "customer_id" => null,
                "ref_id" => null,
                "source_wh_id" => $source_wh_id,
                "target_wh_id" => $target_wh_id,
                "type" => "tf",
                "tax_type" => "",
                "comment" => $comment,
                "status" => "complete",
                "date" => Carbon::now()->toDateString()
            ];

            $result = self::create('tf', $detail, $lineitems);

            return $result;
        }
    }
}
