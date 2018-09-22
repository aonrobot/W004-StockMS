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

    @media print{
        #printReport {
            display: none;
        }
        .reporHeader {
            font-size: 38px;
        }
    }
</style>

<div class="container main" >

    <div class="col-md-12 mb-3 text-right">
        <button class="btn btn-success btn-lg" id="printReport"> <i class="fa fa-print"></i> Print</button>
    </div>
    
    <div class="col-md-12"> 
        <div class="card"> 
            <div class="card-body">
            <h3 class="reporHeader"> สรุปยอดคงเหลือ ณ <small>วันที่</small> <small id="today"></small></h3>
            <table class="table table-striped" id="reportTable">
                <thead>
                    <tr>
                        <th scope="col">รหัสสินค้า</th>
                        <th scope="col">ชื่อสินค้า</th>
                        <th scope="col">จำนวนสินค้าคงเหลือ</th>
                        <th scope="col" class="text-primary">ราคาต้นทุน</th>
                        <th scope="col" class="text-primary">ราคาต้นทุนรวม</th>
                        <th scope="col" class="text-info">ราคาต้นขาย</th>
                        <th scope="col" class="text-info">ราคาต้นขายรวม</th>
                    </tr>
                </thead>
                </table>      
            </div>
        </div>
    </div>

    <div class="col-md-12 text-right">
        <div class="card">
            <div class="col-md-12">
                <h4><u>สินค้าคงเหลือ</u>ทั้งหมด <span class="badge badge-warning total odometer" id="totalQuantity">0</span> ชิ้น</h4>
            </div>
            <div class="col-md-12">
                <h4>คิดเป็น<b class="text-primary">ราคาต้นทุน</b>รวมทั้งหมด <span class="badge badge-primary total odometer" id="totalCost">0</span> บาท</h4>
            </div>
            <div class="col-md-12">
                <h4>คิดเป็น<b class="text-info">ราคาขาย</b>รวมทั้งหมด <span class="badge badge-info total odometer" id="totalSale">0</span> บาท</h4>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function(){

        $('body').busyLoad("show", busyBoxOptions);

        var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');
        
        var d = new Date();
	    var months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

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
                url: url,
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
        
        ajaxSetValue('api/inventory/quantity/sum', '#totalQuantity')
        ajaxSetValue('api/inventory/totalprice', '', function(data){
            setValue('#totalSale', data.total.sale)
        })
        ajaxSetValue('api/inventory/totalprice', '', function(data){
            setValue('#totalCost', data.total.cost)
        })

        ajaxSetValue('api/report/all', '', function(data){
            var c = $('<tbody />');
            _.forEach(data, function(ele){
                c.append(`
                    <tr>
                        <td scope="row">${ele.product_code}</td>
                        <th>${ele.name}</th>
                        <td><u>${ele.quantity}</u></td>
                        <td>${ele.costPrice}</td>
                        <th>${ele.costTotal}</th>
                        <td>${ele.salePrice}</td>
                        <th>${ele.saleTotal}</th>
                    </tr>
                `)
            });
            $('#reportTable').append(c)

            $('body').busyLoad("hide", busyBoxOptions);

        })

        $('#printReport').click(function(){
            window.print();
        });

        $("#today").html(
            d.getDate() + ' ' +
            months[d.getMonth()] + ' ' +
            d.getFullYear() 
        );
        
    })
    
</script>

@endsection

