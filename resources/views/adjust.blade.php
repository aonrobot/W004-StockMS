@extends('layouts.app')


@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container main" >
    <h2>Adjust Stock  <span id="date" class="text-small text-right float-right"></span></h2>
    <div class="row m-b-30">
        <div class="col-md-6">
            <p class="m-b-0 m-t-20">เลือกวันที่ต้องการแสดงสินค้า</p>

            <div class="input-group date">
                <input class="datepicker form-control" data-date-format="dd/mm/yyyy" id="datePicker"  />

                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <p class="m-b-0 m-t-20">เลือกคลังสินค้าที่ต้องการแสดง</p>
            <select disabled class="form-control" id="warehouseSelect">
                <option selected value="1">คลังสินค้าหลัก</option>
            </select>
                <!-- <button class="btn btn-primary m-t-20 col-md-8">แสดง</button> -->
        </div>
    </div>
    <hr />

    <div class="row m-t-30" id="section_list">
        <div class="col-md-12" style="padding-left: 0;">
            <button class="btn btn-link float-right text-right" data-toggle="modal" data-target="#manageStock">
                <span>จัดการสต๊อก <i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            </button>
        </div>
        <div class="col-md-12 card">
            <h5 class="">วันที่ <span id="adjustDate"></span></h5>
            <h5 id="adjustWarehouse"></h5>
            <hr />
            
            <div class="col-md-12" id="display" style="min-height: 200px;">
            
                
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div id="manageStock" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">จัดการสต๊อก</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="addInventoryForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <p class="m-b-0 form-label">เลือกสินค้าที่ต้องการเพิ่ม หรือ ลด จำนวน<sup class="text-danger">*</sup></p>
                                <select class="js-example-basic-single form-control" name="state" style="width: 100%;" id="adjustProduct" required>

                                    <option value="" selected disabled class="text-center" style="margin:0 auto;">
                                        --- เลือกสินค้า ---
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <p class="m-b-0 form-label  m-t-20">เลือกประเภท<sup class="text-danger">*</sup></p>
                                <div class="p-10">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" id="increase" name="adjustType" checked value="increase" />
                                        <label class="custom-control-label" for="increase">
                                            เพิ่มสินค้า
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" id="decrease" name="adjustType" value="decrease" />
                                        <label class="custom-control-label" for="decrease">
                                            ลดสินค้า
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" id="outoforder" name="adjustType" value="outoforder" />
                                        <label class="custom-control-label" for="outoforder">
                                            สินค้าชำรุด
                                        </label>
                                    </div>
                                </div>

                                <p class="m-b-0 m-t-20 form-label">ใส่จำนวนที่ต้องการ<sup class="text-danger">*</sup></p>
                                <input type="number" class=" form-control" placeholder="จำนวนตัวเลข" required id="adjustAmount" />
                                <!-- WARNING!!! -->
                                <span id="amount-warning" class="text-danger">
                                    ขณะนี้มีสินค้าจำนวน <strong id="amount-warning-number">12</strong> </br> 
                                    ไม่สามารถลดจำนวนสินค้าน้อยไปกว่า จำนวนสินค้าที่มีอยู่ 
                                </span>

                                <p class="m-b-0 m-t-20 form-label">NOTE:</p>
                                <textarea class=" form-control" id="adjustRemark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="submitAddInventory" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





<script>

    var d = new Date();
    var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

    $(document).ready(function(){

        $('#date').html(
            ' Today : ' + 
            days[d.getDay()] + ' ' +
            d.getDate() + ' ' +
            months[d.getMonth()] + ' ' +
            d.getFullYear() 
        );

        $("#datePicker").datepicker({
            todayHighlight: true,
            format : 'dd/mm/yyyy',
            endDate: '+1d'

        }).on('changeDate', function(ev){
            $("#adjustDate").html($("#datePicker").val());
            inventory.get().done(function (res) {
                inventory.render(res);
            });
        });

        $("#datePicker").datepicker("setDate", new Date()); 
        $("#adjustDate").html(convertDate(new Date()));
        $("#adjustWarehouse").html($("#warehouseSelect").find('option:selected').text());
        $('.js-example-basic-single').select2();

        inventory.get().done(function (res){console.log(res)});

    });


    // Modal Add Inventory Show 
    $('#manageStock').on('show.bs.modal', function (event) {

        // init  Clear
        $('#amount-warning').hide();
        
        $('#adjustProduct').html(`<option value="" selected disabled class="text-center" style="margin:0 auto;">--- เลือกสินค้า ---</option>`);
        $('#increase').prop("checked", true);
        $('#adjustAmount').val('');
        $('#adjustAmount').removeClass('is-invalid');
        $('#adjustRemark').val('');

        product.get().done(function(res){

            var selectElem = $("#adjustProduct");
            var option = [];
            
            for (var i = 0 ; i < res.length ; i++) {
                option.push({
                    id: res[i].product_id,
                    text: `
                        <span class="badge badge-info">${res[i].code}</span> 
                        ${res[i].name}
                        <span class="float-right sub-title">
                            จำนวน ${res[i].inventory.quantity }  ${res[i].unitName }   
                        </span>
                    `,
                    quantity: res[i].inventory.quantity 
                }); 
            }   
            $(selectElem).select2({
                data: option,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        });
    })

    var product = {

        get() {
            return $.ajax({
                method: 'GET',
                url: "api/product",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            })
        }
    }

    var inventory = {
        get () {
            var date = $("#datePicker").val();
            return $.ajax({
                method: 'GET',
                // Format Date = dd/mm/yyyy
                url: "api/inventoryLog/byDate/" + date,
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        },
        update(prodID , param) {
            return $.ajax({
                method: 'PUT',
                // Format Date = dd/mm/yyyy
                url:  "api/inventory/quantity/" + prodID,
                data: param,
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        },
        render (data) {
            
            var display = $("#display");
                display.empty();
            
            if(data.length === 0){ 
                display.append(`
                    <div class="text-center" id="notfound">
                        <img src="./notfound.png" style="max-width: 100%;" width="30%"/> 
                    </div>`
                );
            }else{
                
                var ul = $(`<ul class="list-group list-group-flush"></ul>`);
                var li = '';
                for(var i = 0 ; i < data.length ; i++) {
                    li += 
                        `<li class="list-group-item">
                            <div class="row"> 
                                <div class="col-md-1 flex flex-v-center">
                                    <span class="badge badge-info m-r-20">${ data[i].product_detail.code }</span>
                                </div>
                                <div class="col-md-4  flex flex-v-center">
                                    ${ data[i].product_detail.name }
                                </div>
                                <div class="col-md-2 flex flex-v-center">ไม่รู้ใส่ตัวแปรไหนว้อย</div>
                                <div class="col-md-2 flex flex-v-center">ไม่รู้ใส่ตัวแปรไหนว้อย</div>
                                <div class="col-md-2">
                                    <span class="float-right  m-r-20"> 
                                        จำนวนคงเหลือ  
                                        <b>${ data[i].quantity }</b> 
                                        ${ data[i].product_detail.unitName }
                                    </span>
                                </div>
                                <div class="col-xs-1  flex flex-v-center">
                                    <a href="#" class="edit-inventory-btn float-right" >
                                        Edit
                                    </a> 
                                </div>
                            </div>
                        </li>`;
                }
                ul.append(li);
                display.append(ul);
            }
        }
    } 

        
    $("#addInventoryForm").submit(function (e) {

        event.preventDefault();
        var product_id = $('#adjustProduct').val();
        var warehouse_id = $('#warehouseSelect').val();
        var type = document.querySelector('input[name="adjustType"]:checked').value;
        var amount = $('#adjustAmount').val();
        var date = $('#datePicker').val();
        var remark = $('#adjustRemark').val();

        var param = {
            "warehouse_id": warehouse_id,
            "type": type,         
            "amount": amount,
            "date": date,         
            "remark": remark   
        }
        var currentQuantity = $("#adjustProduct").select2('data')[0].quantity;

        if (  ( type==="decrease" || type==="outoforder" ) && ( amount > currentQuantity ) ) {

            $('#adjustAmount').addClass('is-invalid');
            $('#amount-warning-number').html(currentQuantity);
            $('#amount-warning').show();

        }else{
            inventory.update(product_id , param);
            inventory.get().done(function(res) { inventory.render(res)} );
            $('#manageStock').modal('hide');
        }
    });
    
    function convertDate(inputFormat) {
        function pad(s) { return (s < 10) ? '0' + s : s; }
            var d = new Date(inputFormat);
            return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
    }

</script>

@endsection

@section('page_script')
<!-- For add custom script of this blade -->
@endsection