@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif


    <style>
        .page[size="A4"] {

            width: 21cm;
            min-height: 27.6cm;
            display: block;
            position: relative;
            margin: 0 auto;
            background: #FFFFFF;
        }
        .a4-header div {

            border: 1px solid #ccc;
            border-radius: 12px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
            }
        }

    </style>
    <div class="row text-right">
        <div style= " width: 21cm; display: block; position: relative; margin: 0 auto; margin-bottom: 30px;">
            <a class="btn btn-info" href="javascript:window.print();" >
                Print    
                <i class="fa fa-print" aria-hidden="true"></i>            
            </a>
        </div>
    </div>

    <div class="page p-30" size="A4" id="printArea">

        <div class="a4-header" style="overflow: auto;">
            <div class="float-right p-20">
                <p id="print_date">ไม่พบข้อมูล</p>
                <p id="print_id" class="m-b-0">ไม่พบข้อมูล</p>
            </div> 
        </div>
        <div class="a4-body m-t-30">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-right">#</th>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th class="text-right">จำนวน</th>
                        <th class="text-right">ราคาต้นทุน</th>
                        <th class="text-right">ราคาขาย</th>
                        <th class="text-right">ราคาต้นทุนรวม</th>
                        <th class="text-right">ราคาขายรวม</th>
                    </tr>
                </thead>
                <tbody id="print_detail">

                </tbody>
            </table>
            <div class="row">
                <div class="col-md-12 text-right">
                    <p>
                        <strong>ต้นทุนรวม : <span id="print_cost_total"> </span> </strong><br>
                        <strong>มูลค่ารวม : <span id="print_detail_total"> </span> </strong>
                    </p>
                </div>
            </div>
            <hr />
        </div>
    </div>



@endsection
@section('page_script')
<script src="{{ asset('js/print.js') }}"></script>
@endsection
