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
        รายการซื้อ
        <a class="btn btn-primary float-right" href="{{ url('purchase_create') }}">
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
            <div class="col-md-3 text-right">
                <p>ประเภท : </p>
            </div>
            <div class="col-md-4">
                <p><strong>ซื้อสินค้าเข้า</strong></p>
            </div>
            </div>
            <div class="row">
                <div class="col-md-3 text-right">
                    <p>รายการ : </p>
                </div>
                <div class="col-md-4">
                    <p>
                        INV201808312333001
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 text-right">
                    <p>วันที่ทำรายการ : </p>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <p>
                            12/231/12312
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 text-right">
                    <p>อ้างอิง : </p>
                </div>
                <div class="col-md-4">
                    <p> - </p>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

@endsection


@section('page_script')
<script src="{{ asset('js/purchase_view.js') }}"></script>
@endsection
