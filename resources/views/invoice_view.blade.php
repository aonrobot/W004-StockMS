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



@endsection


@section('page_script')
<script src="{{ asset('js/invoice_view.js') }}"></script>
@endsection
