@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<style>
    span.total {
        font-size: 24px;
    }
</style>
<div class="container main" >

    <div class="col-md-12" style="margin-bottom: 30px;">
        <div class="card">
            <div class="col-md-6">
                <h3>สินค้าคงเหลือทั้งหมด <span class="badge badge-success total odometer" id="totalQuantity">0</span> ชิ้น</h3>
            </div>
            <div class="col-md-6">
                <h3>คิดเป็นราคารวมทั้งหมด <span class="badge badge-success total odometer" id="totalPrice">0</span> บาท</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-12">
        
        <div class="card"> 
            <div class="card-body">
            <h3> Report </h3>
            <table class="table table-striped" id="reportTable">
                <thead>
                    <tr>
                        <th scope="col">รหัสสินค้า</th>
                        <th scope="col">ชื่อสินค้า</th>
                        <th scope="col">จำนวนสินค้าคงเหลือทั้งหมด</th>
                        <th scope="col">ราคารวม</th>
                    </tr>
                </thead>
                </table>      
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        let Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');
        

        window.odometerOptions = {
            auto: false, // Don't automatically initialize everything with class 'odometer'
            selector: '.total', // Change the selector used to automatically find things to be animated
            format: '(,ddd).dd', // Change how digit groups are formatted, and how many digits are shown after the decimal point
            duration: 1000, // Change how long the javascript expects the CSS animation to take
            animation: 'count' // Count is a simpler animation method which just increments the value,
        };

        function setValue(select, value){ 
            $(select).html(value)
            console.log(value)
        }
        function ajaxSetValue(url, setTo, callback){
            $.ajax({
                method: 'GET',
                url: "http://" + url,
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                success: function(data){
                    if(setTo !== '') setValue(setTo, data)
                    if(callback != undefined) callback(data)
                }
            });
        }
        
        ajaxSetValue('localhost/api/inventory/quantity/sum', '#totalQuantity')
        ajaxSetValue('localhost/api/inventory/totalprice', '', function(data){
            setValue('#totalPrice', data.total.cost)
        })

        ajaxSetValue('localhost/api/report/all', '', function(data){
            var c = $('<tbody />');
            _.forEach(data, function(ele){
                c.append(`
                    <tr>
                        <th scope="row">${ele.product_code}</th>
                        <td>${ele.name}</td>
                        <td>${ele.quantity}</td>
                        <td>${ele.costTotal}</td>
                    </tr>
                `)
            });
            $('#reportTable').append(c)
        })
        
    })
    
</script>

@endsection

