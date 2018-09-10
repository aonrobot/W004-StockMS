@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

    
<link rel="stylesheet" href="{{ asset('./css/invoice-style.css') }} "/>

<div class="container main" >
    <h3> 
        รายการขาย 
        <a class="btn btn-primary float-right" href="{{ url('invoice_create') }}">
            สร้างใหม่ <i class="fa fa-plus-circle" aria-hidden="true"></i>
        </a>   
    </h3>
    <hr />
    <div class="row card m-t-30">
        <div class="table-responsive">
            <table id="invoice_view_table" class="table-style" style="WIDTH:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รายการ</th>
                        <th>วันทำรายการ</th>
                        <th>มูลค่า</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="detail_modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xlarge">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">รายละเอียดรายการขาย</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-12 text-right" id="doc_print">
                    
                </div>
            </div>

            <div class="row">
                <div class="col-6 col-sm-6 col-md-3 text-right">
                    <p>ประเภท : </p>
                </div>
                <div class="col-6 col-sm-6 col-md-4">
                    <p><strong>ขายสินค้าออก</strong></p>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6 col-md-3 text-right">
                    <p>รายการ : </p>
                </div>
                <div class="col-6 col-sm-6 col-md-4">
                    <p id="doc_id">
                        INV201808312333001
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6 col-md-3 text-right">
                    <p>วันที่ทำรายการ : </p>
                </div>
                <div class="col-6 col-sm-6 col-md-4">
                    <div class="input-group">
                        <p id="doc_date">
                            12/231/12312
                        </p>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-6 col-sm-6 col-md-3 text-right">
                    <p>อ้างอิง : </p>
                </div>
                <div class="col-6 col-sm-6 col-md-4">
                    <p id="doc_refer"> - </p>
                </div>
            </div> -->

            <!-- TABLE -->
            <div class="table-responsive m-t-30">
                <table class="table table-striped table-create-IV">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-right">#</th>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th class="text-right">จำนวน</th>
                            <th class="text-right">ต้นทุนต่อหน่วย</th>
                            <th class="text-right">มูลค่าต่อหน่วย</th>
                            <th class="text-right">หน่วย</th>
                            <th class="text-right">รวมต้นทุน</th>
                            <th class="text-right">รวม</th>
                        </tr>
                    </thead>
                    <tbody id="doc_detail">
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <p class="m-b-0">
                            จำนวนทั้งหมด : <span id="doc_detail_count"> </span> 
                        </p>
                        <p>
                            ต้นทุนรวม : <span id="doc_detail_cost_total"> </span>
                        </p>
                        <p>
                            <strong>มูลค่ารวม : <span id="doc_detail_total"> </span> </strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

@endsection


@section('page_script')
<script src="{{ asset('js/invoice_view.js') }}"></script>
@endsection
