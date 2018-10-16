@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<link rel="stylesheet" href="{{ asset('./css/invoice-style.css') }} "/>
<link rel="stylesheet" href="{{ asset('./css/jquery-ui.css') }}">


<div class="container main" >
    <h3> สร้างรายการขาย </h3>
    <hr />
    <div class="row">
        <div class="col-md-3 text-right">
            <p>ประเภท : </p>
        </div>
        <div class="col-md-4">
            <p>ขายสินค้าออก</p>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 text-right">
            <p>รายการ<sup class="text-danger">*</sup> : </p>
        </div>
        <div class="col-md-4">
            <input class="form-control" type="text" id="invoice_id" />
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 text-right">
            <p>วันที่ทำรายการ<sup class="text-danger">*</sup> : </p>
        </div>
        <div class="col-md-4">
            <div class="input-group date">
                <input class="datepicker form-control" data-date-format="dd/mm/yyyy" id="invoice_date" />
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row form-group">
        <div class="col-md-3 text-right">
            <p>อ้างอิง : </p>
        </div>
        <div class="col-md-4">
            <input class="form-control" type="text" id="invoice_ref" />
        </div>
    </div> -->
    <!-- <div class="row form-group">
        <div class="col-md-3 text-right">
            <p>ช่องทางการขาย<sup class="text-danger">*</sup> : </p>
        </div>
        <div class="col-md-4">
            <input class="form-control" type="text" id="invoice_" />
        </div>
    </div> -->


    <div class="row card m-t-30">
        <div class="table-responsive">
            <table class="table table-striped table-create-IV">
            <thead>
                <tr>
                    <th></th>
                    <th class="text-right">#</th>
                    <th style="width: 12%;">รหัสสินค้า</th>
                    <th style="width: 30%;">ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>มูลค่าต่อหน่วย</th>
                    <th>หน่วย</th>
                    <th>รวม</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="table_body">
                    <tr id="row_1">
                        <td class="td__btn-add">
                            <button class="btn-default btn-circle" 
                                id="btn_1"
                                data-toggle="modal" data-target=".bd-example-modal-lg">

                                <i class="fa fa-list" aria-hidden="true"></i>
                            </button>

                        </td>
                        <td class="text-right td__index">
                            1
                        </td>
                        <td class="td__prodCode">
                            <input type="search" class="form-control"/>
                        </td>
                        <td class="td__prodName">
                            <input type="search" class="form-control"/>
                        </td>
                        
                        <td class="td__unitValue" >
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text pointer" onclick="row_value.minus(1)">
                                        -
                                    </span>
                                </div>

                                <input type="number" id="unitValue_1" value="0" 
                                    class="text-center form-control form__number"
                                    onchange="row_value.total(1)" />

                                <div class="input-group-append">
                                    <span class="input-group-text pointer" onclick="row_value.plus(1)">
                                        +
                                    </span>
                                </div>
                            </div>
                            <small class="float-right"></small>
                        </td>
                        
                        <td class="td__amount">
                            <input type="number" class="form-control text-right" onchange="row_value.total(1)" />
                        </td>
                        
                        <td class="text-right td__unit">
                            
                        </td>
                        <td class="text-right td__total">
                            0.00
                        </td>
                        <td class="td__btn-remove"> 
                            <i class="fa fa-minus-circle pointer btn-remove-row" aria-hidden="true" onclick="removeRow(1)"></i>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-link" onclick="addRow(1)">
                <span>+ เพิ่มสินค้า </span>
            </button>
        </div>
        <!-- Card Footer --> 
        <div class="col-md-12 float-right text-right">
            <h5><strong>มูลค่ารวม : <span class="INVOICE_TOTAL"> 0.00 </span></strong></h5>
        </div>
    </div>
    
    <hr class="m-t-30" />

    <div class="row form-group m-t-30">
        <div class="col-md-3 text-right">
            <p><strong>มูลค่ารวมสุทธิ </strong></p>
            <p>สินค้าจาก</p>
            
        </div>
        <div class="col-md-4">  
            <p><strong><span class="INVOICE_TOTAL"> 0.00 </span></strong></p>
            <select name=""  class="form-control" id="warehouse_select">
                <!-- TODO: fetch data from api -->
            </select>
        </div>
    </div>
    <hr class=" m-t-30" />
    <div class="row m-t-30">
        <div class="col-md-12 text-center">
            <button class="btn btn-lg btn-primary" onclick="createInvoice()">
                บันทึก
                <i class="fa fa-save" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<div id="product_modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">เพิ่มสินค้า</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="modal_prod_table" class="table-style" style="WIDTH:100%;">
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคาขาย</th>
                            <th>จำนวนคงเหลือ</th>
                            <th>หน่วย</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>


<div id="warning_modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header bg-danger">
            <h5 class="modal-title text-white">เพิ่มสินค้า</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p id="warning_text"> กรุณากรอกข้อมูลให้ครบถ้วน </p>
        </div>
    </div>
  </div>
</div>




@endsection


@section('page_script')
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/invoice_create.js') }}"></script>

@endsection
