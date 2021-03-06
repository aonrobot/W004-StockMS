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

            $docs = DocumentDetail::where('user_id', \Auth::id())->where('type', $type)->get();
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

            $doc = DocumentDetail::where('user_id', \Auth::id())->where('id', $id)->first();
            if($doc == null) return response()->json([]);

            $number = $doc->number;

            return $this->show($number);
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
        // Find and add product_id because ui send product_code but backend use product_id
        foreach ($lineitems as $index => $item) {
            $product_id = \App\Product::where('user_id', \Auth::id())->where('code', $item['product_code'])->first()->product_id;
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
        $doc = DocumentDetail::where('user_id', \Auth::id())->where('number', $id)->first();
        if ($doc == null) return response()->json([]);

        $lineitems = DocumentLineItems::where('document_id', $doc->id)->get();
        

        if ($doc->source_wh_id != null && $doc->target_wh_id == null) {
            $warehouse_id = $doc->source_wh_id;
        } else {
            $warehouse_id = $doc->target_wh_id;
        }

        if($warehouse_id == null) return response()->json(['updated' => false, 'message' => 'warehouse id is dont set in this document']);

        $docProduct = [];
        foreach ($lineitems as $index => $item) {
            $item['product'] = \App\Product::where('product_id', $item['product_id'])->first();
            $item['product']['inventory'] = \App\Inventory::where('product_id', $item['product_id'])->where('warehouse_id', $warehouse_id)->first();
            array_push($docProduct, $item);
        }

        $doc['lineItems'] = $docProduct;

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
        // $id = document number !!!!!!!

        $detail = $request->input('detail');
        $lineitems = $request->input('lineitems');

        if(!isset($detail)) $detail = [];
        if(!isset($lineitems)) $lineitems = [];

        /**
         *  Clean data before sent to create document
         */

        if(isset($detail['date']))
            $detail['date'] = Carbon::createFromFormat('d/m/Y', $detail['date'])->format('Y-m-d');

        $id = DocumentDetail::where('user_id', \Auth::id())->where('number', $id)->first(['id']);
        if (empty($id)) {
            return response()->json(['updated' => false, 'message' => 'Cannot found this document']);
        }
        $id = $id->id;
        
        /**
         *  Create document
         */

        $result = Document::update($id, $detail, $lineitems);

        return response()->json($result);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = DocumentDetail::where('user_id', \Auth::id())->where('number', $id)->first(['id']);
        if (empty($id)) {
            return response()->json(['updated' => false, 'message' => 'Cannot found this document']);
        }
        $id = $id->id;

        /**
         *  Create document
         */

        $result = Document::delete($id);

        return response()->json($result);
    }

    public function destroyLineItem($id)
    {
        $result = Document::deleteLineItem($id);
        
        return response()->json($result);
    }

    public function genDocNumber($type){

        return response()->json(DocumentUtil::genDocNumber($type));
    }

    public function revenue($type){

        $revenue = 0;
        switch ($type) {
            case 'today':

                $docs = DocumentDetail::where('user_id', \Auth::id())->where('type', 'inv')->where('date', Carbon::now()->format('Y-m-d') )->get();
                foreach ($docs as $doc) {
                    $revenue += DocumentLineItems::where('document_id', $doc['id'])->sum('total');
                }

            break;

            case 'thisMonth':

                $docs = DocumentDetail::where('user_id', \Auth::id())->where('type', 'inv')->where('date', 'like', Carbon::now()->format('Y-m') . '%')->get();
                foreach ($docs as $doc) {
                    $revenue += DocumentLineItems::where('document_id', $doc['id'])->sum('total');
                }

            break;

            case 'thisYear':

                $docs = DocumentDetail::where('user_id', \Auth::id())->where('type', 'inv')->where('date', 'like', Carbon::now()->format('Y') . '%')->get();
                foreach ($docs as $doc) {
                    $revenue += DocumentLineItems::where('document_id', $doc['id'])->sum('total');
                }

            break;
        }
        return response()->json($revenue);
    }

    public function yearRevenueChart()
    {
        $result = [];
        $year = Carbon::now()->year;
        $users = \App\User::get();

        $colors_set = [
            [
                'rgba(54, 162, 235, 0.2)',
                'rgba(54, 162, 235, 1)'
            ], [
                'rgba(255, 107, 107, 0.2)',
                'rgba(255, 107, 107, 1.0)'
            ], [
                'rgba(254, 202, 87, 0.2)',
                'rgba(254, 202, 87, 1.0)'
            ], [
                'rgba(29, 209, 161, 0.2)',
                'rgba(29, 209, 161, 1.0)'
            ], [
                'rgba(95, 39, 205, 0.2)',
                'rgba(95, 39, 205, 1.0)'
            ], [
                'rgba(87, 101, 116, 0.2)',
                'rgba(87, 101, 116, 1.0)'
            ]
        ];

        foreach ($users as $index => $user)
        {
            array_push($result, [
                'label' => $user->branchName,
                'data' => [],
                'backgroundColor' => $colors_set[$index][0],
                'borderColor' => $colors_set[$index][1],
                'borderWidth' => 1
            ]);

            for ($i = 1; $i <= 12; $i++)
            {  
                $likeStr            = $year .'-' . str_pad($i, 2, 0, STR_PAD_LEFT) . '-%';
                $documentDetail     = DocumentDetail::where('user_id', $user->id)->where('type', 'inv')->where('date', 'like', $likeStr)->get();
                $total = 0;
                foreach($documentDetail as $doc){
                    $doc_id = $doc['id'];
                    $total += DocumentLineItems::where('document_id', $doc_id)->sum('total');
                }
                array_push($result[$index]['data'], $total);
            }
        }
        

        return response()->json($result);
    }

    public function bestSeller()
    {
        $products = \App\Product::where('user_id', \Auth::id())->get(['product_id']);
        $documentDetail = DocumentDetail::where('user_id', \Auth::id())->where('user_id', \Auth::id())->where('type', 'inv')->get();
        
        $countSell = [];
        foreach($products as $p){
            $countSell[$p['product_id']] = 0;
        }
        foreach($documentDetail as $doc)
        {
            $items = DocumentLineItems::where('document_id', $doc['id'])->get(['product_id', 'amount'])->toArray();
            foreach($items as $item){
                $countSell[$item['product_id']]+=$item['amount'];
            }
        }

        arsort($countSell);

        $seller = [
            'data' => [],
            'label' => []
        ];
        $i = 1;
        foreach($countSell as $key => $value){
            if($value <= 0) continue;
            array_push($seller['data'], $value);
            $product_name = \App\Product::where('product_id', $key)->first(['name'])->name;
            array_push($seller['label'], 'อันดับ ' . $i . ' - ' . $product_name);
            $i++;
        }

        $seller['data'] = array_slice($seller['data'], 0 , 5);
        $seller['label'] = array_slice($seller['label'], 0 , 5);

        return response()->json($seller);
    }
}
