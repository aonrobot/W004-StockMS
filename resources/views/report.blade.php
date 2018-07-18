@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container fixed-bottom main" >

    <div class="col-md-12" style="margin-bottom: 30px;">
        <div class="card">
                <h3>ร้านคงเหลือสินค้าทั้งหมด : <span id="quantity_sum"></span></h3>
                <h3>สินค้าทั้งหมดคิดเป็นราคา : <span id="total"></span></h3>
        </div>
    </div>
    
    <div class="col-md-12">
        
        <div class="card"> 
            <div class="card-body">
            <h3> Report </h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">รหัสสินค้า</th>
                        <th scope="col">ชื่อสินค้า</th>
                        <th scope="col">จำนวนสินค้าคงเหลือทั้งหมด</th>
                        <th scope="col">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
                </table>      
            </div>
        </div>
    </div>
</div>

<script src="./js/report.js"></script>

@endsection

