<?php
namespace App\Library\_Class {

    use App\Library\_Class\DocumentUtil;
    use App\Library\_Class\Inventory as InventoryClass;
    use Carbon\Carbon;
    use Log;

	class Document {

        static public function create($type, $detail, $lineitems)
        {
            try
            {
                //if ui sent empty number it will auto gen
                if($detail['number'] == "") {
                    $detail['number'] = DocumentUtil::genDocNumber($type);
                }
                if(!isset($detail['status'])) {
                    $detail['status'] = "create";
                }
                if($detail['status'] == "") {
                    $detail['status'] = "create";
                }

                $doc_number = $detail['number'];
                $doc_source_wh_id = $detail['source_wh_id'];
                $doc_target_wh_id = $detail['target_wh_id'];

                switch($type){

                    /*

                        ████████╗██████╗  █████╗ ███╗   ██╗███████╗███████╗███████╗██████╗ 
                        ╚══██╔══╝██╔══██╗██╔══██╗████╗  ██║██╔════╝██╔════╝██╔════╝██╔══██╗
                           ██║   ██████╔╝███████║██╔██╗ ██║███████╗█████╗  █████╗  ██████╔╝
                           ██║   ██╔══██╗██╔══██║██║╚██╗██║╚════██║██╔══╝  ██╔══╝  ██╔══██╗
                           ██║   ██║  ██║██║  ██║██║ ╚████║███████║██║     ███████╗██║  ██║
                           ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝╚══════╝╚═╝     ╚══════╝╚═╝  ╚═╝

                            swh_id = source warehouse id
                            twh_id = target warehouse id

                            type :
                                                            source          target      inventory
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

                            if($amount <= 0) continue;

                            \App\DocumentLineItems::create([
                                "document_id" => $doc_id,
                                "product_id" => $product_id,
                                "amount" => $amount
                            ]);

                            if($doc_source_wh_id == null && $doc_target_wh_id != null) {
                                $currentQuantity = InventoryClass::increase($product_id, $doc_target_wh_id, $amount);
                            } elseif($doc_source_wh_id != null && $doc_target_wh_id == null) {
                                $currentQuantity = InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
                            } else {
                                InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
                                $currentQuantity = InventoryClass::increase($product_id, $doc_target_wh_id, $amount);
                            }
                        }

                    break;

                    /*

                        ██╗███╗   ██╗██╗   ██╗ ██████╗ ██╗ ██████╗███████╗
                        ██║████╗  ██║██║   ██║██╔═══██╗██║██╔════╝██╔════╝
                        ██║██╔██╗ ██║██║   ██║██║   ██║██║██║     █████╗  
                        ██║██║╚██╗██║╚██╗ ██╔╝██║   ██║██║██║     ██╔══╝  
                        ██║██║ ╚████║ ╚████╔╝ ╚██████╔╝██║╚██████╗███████╗
                        ╚═╝╚═╝  ╚═══╝  ╚═══╝   ╚═════╝ ╚═╝ ╚═════╝╚══════╝

                    */
                    case 'inv':

                        $checkResult = ProductUtil::checkQuantity($lineitems);
                        if(count($checkResult) > 0) return [
                            'created' => false,
                            'message' => $checkResult
                        ];

                        if(empty($detail['tax_type'])) {
                            $detail['tax_type'] = 'withoutTax';
                        }

                        $doc_id = \App\DocumentDetail::create($detail)->id;
                        
                        foreach($lineitems as $item)
                        {
                            $product_id = $item['product_id'];

                            $amount = $item['amount'];
                            $price = $item['price'];
                            $discount = $item['discount'];

                            $item['document_id'] = $doc_id;
                            $item['total'] = ($price * $amount) - $discount;
                            \App\DocumentLineItems::create($item);

                            if($doc_source_wh_id == null) {
                                if($doc_target_wh_id !== null) {
                                    $doc_source_wh_id = $doc_target_wh_id;
                                } else {
                                    return ['created' => false, 'message' => 'source warehouse id could not [null]'];
                                }
                            }

                            $currentQuantity = InventoryClass::decrease($product_id, $doc_source_wh_id, $amount);
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

                        if(empty($detail['tax_type'])) {
                            $detail['tax_type'] = 'withoutTax';
                        }
                        
                        $doc_id = \App\DocumentDetail::create($detail)->id;
                        
                        foreach($lineitems as $item)
                        {
                            $product_id = $item['product_id'];

                            $amount = $item['amount'];

                            $item['document_id'] = $doc_id;
                            $item['total'] = 0;
                            \App\DocumentLineItems::create($item);

                            if($doc_target_wh_id == null) {
                                if($doc_source_wh_id !== null) {
                                    $doc_target_wh_id = $doc_source_wh_id;
                                } else {
                                    return ['created' => false, 'message' => 'target warehouse id could not [null]'];
                                }
                            }

                            $currentQuantity = InventoryClass::increase($product_id, $doc_target_wh_id, $amount);
                        }
                        
                    break;
                    
                    return ['created' => false, 'message' => 'Document type not support'];
                }

                \App\Transaction::create([
                    'document_id' => $doc_id,
                    'balance' => $currentQuantity
                ]);

                return [
                    'created' => true,
                    'message' => 'create transfer',
                    'document_id' => $doc_id,
                    'document_number' => \App\DocumentDetail::find($doc_id)->number
                ];

            } catch(\Exception $e) {

                \App\DocumentDetail::find($doc_id)->delete();
                Log::error($e);
                return ['created' => false, 'message' => 'Error to create document please contact engineer.'];
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
