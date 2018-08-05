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
                <input class="datepicker form-control" data-date-format="dd/mm/yyyy" id="datePicker" />

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
                <option selected >คลังสินค้าหลัก</option>
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
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <p class="m-b-0 form-label">เลือกสินค้าที่ต้องการเพิ่ม หรือ ลด จำนวน</p>
                            <select class="js-example-basic-single form-control" name="state" style="width: 100%;" id="productSelect">
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                        <p class="m-b-0 form-label">เลือกประเภท</p>
                            <div class="p-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="plus" name="defaultExampleRadios" checked />
                                    <label class="custom-control-label" for="plus">
                                        เพิ่มสินค้า
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="minus" name="defaultExampleRadios" />
                                    <label class="custom-control-label" for="minus">
                                        ลดสินค้า
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="broken" name="defaultExampleRadios" />
                                    <label class="custom-control-label" for="broken">
                                        สินค้าชำรุด
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <p class="m-b-0 form-label">ใส่จำนวนที่ต้องการ</p>
                            <input type="number" class=" form-control" placeholder="จำนวนตัวเลข" />
                        </div>

                        <div class="col-md-12 form-group">
                            <p class="m-b-0 form-label">NOTE:</p>
                            <textarea class=" form-control" ></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
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
            todayHighlight: true
        });
        
        $("#datePicker").datepicker("setDate", new Date); 
        $("#adjustDate").html(convertDate(new Date()));
        $("#adjustWarehouse").html($("#warehouseSelect").val());
        $('.js-example-basic-single').select2();

    });


    $.ajax({
        method: 'GET',
        url: "api/inventoryLog/byDate/"+'2018-08-04',
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        }
    }).done(function (data) {
        console.log(data);
    });

    
    $('#manageStock').on('show.bs.modal', function (event) {
        product.get().done(function(res){
            var selectElem = $("#productSelect");
            var option = [];
            
            for (var i = 0 ; i < res.length ; i++) {
                option.push({
                    id: res[i].product_id,
                    text: `<span class="badge badge-info">${res[i].code}</span> ${res[i].name}`  
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
        
        init(){
            this.get();
        },

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