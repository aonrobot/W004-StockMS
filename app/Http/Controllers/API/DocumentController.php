<?php

namespace App\Http\Controllers\API;

use \Carbon\Carbon;

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
    public function index()
    {
        //
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
        //
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
