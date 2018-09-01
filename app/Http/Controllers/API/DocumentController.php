<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\DocumentDetail;
use App\DocumentLineItems;
use App\Library\_Class\ProductUtil;
use App\Library\_Class\Document;
use App\Library\_Class\DocumentUtil;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->input('type'))) {
            $type = $request->input('type');

            $docs = DocumentDetail::where('type', $type)->get();
            if($docs == null) return response()->json([]);

            foreach ($docs as $index => $doc) {
                $doc_id = $doc['id'];
                $docs[$index]['date'] = Carbon::createFromFormat('Y-m-d', $doc['date'])->format('d/m/Y');
                $lineitems = DocumentLineItems::where('document_id', $doc_id)->get()->toArray();
                $sum = 0.0;
                foreach ($lineitems as $item) {
                    $sum += floatval($item['total']);
                }
                $docs[$index]['total'] = number_format((float) $sum, 2, '.', '');
            }
            
            return response()->json($docs);
        }

        if (!empty($request->input('id'))) {
            $id = $request->input('id');

            $doc = DocumentDetail::where('id', $id)->first();
            if($doc == null) return response()->json([]);

            $lineitems = DocumentLineItems::where('document_id', $doc->id)->get();
            $docProduct = [];
            foreach ($lineitems as $index => $item) {
                $item['product'] = \App\Product::where('product_id', $item['product_id'])->first();
                array_push($docProduct, $item);
            }

            $doc['lineItems'] = $docProduct;

            return response()->json($doc);
        }

        if (!empty($request->input('number'))) {
            $number = $request->input('number');

            return $this->show($number);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $detail = $request->input('detail');
        $lineitems = $request->input('lineitems');

        /**
         *  Clean data before sent to create document
         */

        $detail['date'] = Carbon::createFromFormat('d/m/Y', $detail['date'])->format('Y-m-d');
        // Find and add product_id
        foreach ($lineitems as $index => $item) {
            $product_id = \App\Product::where('code', $item['product_code'])->first()->product_id;
            $lineitems[$index]['product_id'] = $product_id; 
        }

        /**
         *  Create document
         */

        $type = $detail['type'];

        $result = Document::create($type, $detail, $lineitems);

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $number = $id;

        $doc = DocumentDetail::where('number', $number)->first();
        if($doc == null) return response()->json([]);

        $lineitems = DocumentLineItems::where('document_id', $doc->id)->get();
        foreach ($lineitems as $item) {
            $item['product'] = \App\Product::where('product_id', $item['product_id'])->first();
            $doc['lineitems'] = $item;
        }

        return response()->json($doc);
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
        // {
        //     "detail" : {
        //       "date": "29/08/2018"
        //     },
        //     "lineitems" : [
        //         {
        //          "amount": 50,
        //          "price": 100,
        //          "discount": 200
        //         }
        //     ]
        // }
        
        $detail = $request->input('detail');
        $lineitems = $request->input('lineitems');     
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function genDocNumber($type){

        return response()->json(DocumentUtil::genDocNumber($type));
    }
}
