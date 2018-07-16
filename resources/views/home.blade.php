@extends('layouts.app')

@section('content')
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div> -->
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container fixed-bottom main" >
        <!-- This is for Sub Menu -->
        <div class="row">
            <div class="col-md-6 m-t-50">
                <h4 class="title">สินค้าทั้งหมด </h4>
            </div>
            <div class="col-md-6 m-t-50">
                <button id="addProduct" class="btn btn-primary pull-right m-b-20" type="button" data-toggle="modal" data-target="#addProd">
                    เพิ่มสินค้า
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </button>
            </div>

            <div class="col-md-12">
                <div class="panel panel-default card">
                    <div class="panel-body">
                        <table id="prod_table" class="table-style" style="WIDTH:100%;">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อ</th>
                                    <th>ราคาซื้อ</th>
                                    <th>ราคาขาย</th>
                                    <th>จำนวนคงเหลือ</th>
                                    <th>หน่วย</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="cm-footer">
            <span class="pull-left">This is Footer</span>
        </footer>
    </div>

    <!-- Modal -->
    <div id="addProd" class="modal fade in" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title ">เพิ่มสินค้า</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                
                <form id="form_prod">
                    <div class="modal-body">
                        <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>รหัสสินค้า</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="prod_code" name="product_code" value="" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <h4>ชื่อสินค้า <sup class="text-danger">*</sup></h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="prod_name" name="product_name" 
                                    value="" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>หมวดหมู่</h4>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group ">

                                    <select class="form-control" 
                                            id="prod_cat" 
                                            name="product_catagories" 
                                            value="">
                                        <option selected="true" value="">ไม่มีหมวดหมู่</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-link" id="add_cat" type="button" data-toggle="modal" data-target="#prod_cat_modal"> 
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    เพิ่มหมวดหมู่
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>ราคาซื้อ / ราคาขาย</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <input type="number" min="0" name="product_price_buy" class="form-control" id="prod_price_buy" placeholder="ราคาซื้อ" value=""/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <input type="number"  min="0" name="product_price_sale" class="form-control" id="prod_price_sale" placeholder="ราคาขาย" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>หน่วย</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="prod_unit" placeholder="เช่น : ชิ้น, กล่อง, ชุด" value="" name="product_unit"/>
                                </div>
                            </div>
                        </div>
                        
                        <hr />


                        <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">จำนวน</h4>
                                <span style="color: #ccc; font-size: 16px;">(จำนวนสต๊อกปัจจุบัน)</span>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="number" class="form-control" id="prod_amount"  min="0" name="product_amount" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">สินค้าเข้าที่  </h4>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group ">
                                    <select class="form-control" id="prod_branch" name="branch" value="main_branch">
                                        <option>คลังสินค้าหลัก</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button id="add_branch" class="btn btn-link" type="button" data-toggle="modal" data-target="#prod_branch_modal"> 
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    เพิ่มคลังสินค้า
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">คำอธิบายเพิ่มเติม</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <textarea type="text" class="form-control" name="product_detail" id="prod_detail" value="" placeholder="กรอกคำอธิบาย"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="submitBtn" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sub Modal Add Catagory -->
    <div id="prod_cat_modal" class="modal fade in" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content" style="-webkit-box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);
            box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);">
                <div class="modal-header">
                    <h3 class="modal-title ">เพิ่มหมวดหมู่</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="form_cat">
                    <div class="modal-body">
                    <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>หมวดหมู่</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="cat_code"name="product_code" value="" required />

                                    <span id="text_warning" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="addProdCat" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    
    <!-- Sub Modal Add branch -->
    <div id="prod_branch_modal" class="modal fade in" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content" style="-webkit-box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);
            box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);">
                <div class="modal-header">
                    <h3 class="modal-title ">เพิ่มคลังสินค้า</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="form_cat">
                    <div class="modal-body">
                    <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>คลังสินค้า</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="branch_code" name="branch_name" value="" required />

                                    <span id="text_warning_branch" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="addBranch" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div id="edit_modal" class="modal fade in" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title ">แก้ไขสินค้า</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                
                <form id="form_prod">
                    <div class="modal-body">
                        <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>รหัสสินค้า</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="edit_prod_code" name="edit_product_code" value=""  disabled />
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <h4>ชื่อสินค้า <sup class="text-danger">*</sup></h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="edit_prod_name" name="edit_product_name" 
                                    value="" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>หมวดหมู่</h4>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group ">

                                    <select class="form-control" 
                                            id="edit_prod_cat" 
                                            name="edit_product_catagories" 
                                            value="">
                                        <option selected="true" value="">ไม่มีหมวดหมู่</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-link" id="edit_add_cat" type="button" data-toggle="modal" data-target="#edit_prod_cat_modal"> 
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    เพิ่มหมวดหมู่
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>ราคาซื้อ / ราคาขาย</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <input type="number" min="0" name="edit_product_price_buy" class="form-control" id="edit_prod_price_buy" placeholder="ราคาซื้อ" value=""/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <input type="number"  min="0" name="edit_product_price_sale" class="form-control" id="edit_prod_price_sale" placeholder="ราคาขาย" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>หน่วย</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="edit_prod_unit" placeholder="เช่น : ชิ้น, กล่อง, ชุด" value="" name="edit_product_unit"/>
                                </div>
                            </div>
                        </div>
                        
                        <hr />


                        <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">จำนวน</h4>
                                <span style="color: #ccc; font-size: 16px;">(จำนวนสต๊อกปัจจุบัน)</span>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="number" class="form-control" id="edit_prod_amount"  min="0" name="edit_product_amount" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">สินค้าเข้าที่  </h4>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group ">
                                    <select class="form-control" id="edit_prod_branch" name="branch" value="main_branch">
                                        <option>คลังสินค้าหลัก</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-link" type="button" data-toggle="modal" data-target="#edit_prod_branch_modal"> 
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    เพิ่มคลังสินค้า
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">คำอธิบายเพิ่มเติม</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <textarea type="text" class="form-control" name="product_detail" id="edit_prod_detail" value="" placeholder="กรอกคำอธิบาย"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="editBtn" type="submit" class="btn btn-primary" >Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- EDIT CATAGORY MODAL -->
    <div id="edit_prod_cat_modal" class="modal fade in" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content" style="-webkit-box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);
            box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);">
                <div class="modal-header">
                    <h3 class="modal-title ">เพิ่มหมวดหมู่</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="form_cat">
                    <div class="modal-body">
                    <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>หมวดหมู่</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="edit_cat_code"name="product_code" value="" required />

                                    <span id="edit_text_warning" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="edit_addProdCat" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- EDIT BRANCH Modal -->
    <div id="edit_prod_branch_modal" class="modal fade in" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content" style="-webkit-box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);
            box-shadow : -1px 7px 55px -9px rgba(0,0,0,0.75);">
                <div class="modal-header">
                    <h3 class="modal-title ">เพิ่มคลังสินค้า</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="form_cat">
                    <div class="modal-body">
                    <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>คลังสินค้า</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group ">
                                    <input type="text" class="form-control" id="edit_branch_code" name="branch_name" value="" required />

                                    <span id="edit_text_warning_branch" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="edit_addBranch" type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    //Event
    $(document).ready(function(){
        console.log("Loading");

        let proID = ''
        let Authorization = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjUwZDYwYzRkNzU1YTMxNzIyN2FjYzM4NTEwMWNhN2U5NmM3NzQ4M2E5NzE0NDhkM2ZmOTFkZGVmMzAwZTc1YTIzOGYxZGQxNjg4ZWYwODQzIn0.eyJhdWQiOiIxIiwianRpIjoiNTBkNjBjNGQ3NTVhMzE3MjI3YWNjMzg1MTAxY2E3ZTk2Yzc3NDgzYTk3MTQ0OGQzZmY5MWRkZWYzMDBlNzVhMjM4ZjFkZDE2ODhlZjA4NDMiLCJpYXQiOjE1MzA4MTAzMTcsIm5iZiI6MTUzMDgxMDMxNywiZXhwIjoxNTYyMzQ2MzE3LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.pOFmjIzsbTumMP8X1aBIFQACsEJBh5AcepEdyQgmUWrqn4-bVcbzKuXMtToQbHsDz6WRHRTTGJbTWSmwoh0ND8bHZAahy8Qn-O5FytcyVCvad0kzSxl_aBdwp6Mcr1P-rUU1Su_paR9Lf_0kFvTmw6V0AR4O6ZYnfEHW-Kr34rO555raZjp4DakhSuF7pmLsYb-fe8eXykzpvR7-liTdZvzLcwRbv8EkeoUXgyk6Bn6kcy-Tl00CHs2JRnwHUwFB_ogpz65QrRlwdKoP9HNVWmU7I7KCMm3dresBpOtkWeig-MNRDfsltkr_eTHm06mn0JID2zlnNFcSTh_MEQtC34v4iels2w3yjMYR3HaCk52xrq4Vwr-IOCPAk3Byb2xXIfyKTEtMbtAy6_jXNBRlmdXEjmFHRFdyOFooNZ4-rNPO3EP9OOLszaI_iEV0GkoOZ3YFmUi0lDERIH73Wz6YivZ1hAGpQor-Ul5RnRlvad6h78ms6WSkYLrUAkmwfmbeayoeRDTGyrzVRG7-QXMEVTe_VANPgRMpczfEdsh1aPl7fQch9PRn-QutZz6fXRhnUDFuEeKFIkZR28H5ZiYa9ONqo8csclIa6Dnk853zw0ylShaPQro0JJAn1-76PZqeoMFwHRDv9uzxBom06ex80AhYSyHuL972Yz4m9Gw8Q00'

        $('#addProduct').click(function(){
            $.ajax({
                method: 'GET',
                url: "http://localhost/api/service/product/getcode",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                success: function(data) {
                    // $('#prod_code').val(data.code);
                    proID = data.code;
                }
            });

            $('#prod_cat').empty();
            $('#prod_branch').empty();

            $.ajax({
                method: 'GET',
                url: "http://localhost/api/category",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                success: function(data) {
                    var select = $("<select>");
                    $.each(data, function(key,value) {
                        select.append(
                            $('<option></option>').val(value.id).html(value.name)
                        );
                    });
                    $("#prod_cat").append(select.html());
                }
            });

            $.ajax({
                method: 'GET',
                url: "http://localhost/api/warehouse",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                success: function(data) {
                    var select = $("<select>");
                    $.each(data, function(key,value) {
                        select.append(
                            $('<option></option>').val(value.id).html(value.name)
                        );
                    });
                    $("#prod_branch").append(select.html());
                }
            });

        });

        $('#addProdCat').click(function(){
            $.ajax({
                type: 'POST',
                url: "http://localhost/api/category",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                data: {
                    "category":{
                        "name": $("#cat_code").val(),
                        "description":""
                    }
                }
            });
        });

        $('#addBranch').click(function(){
            $.ajax({
                type: 'POST',
                url: "http://localhost/api/warehouse",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                data: {
                    "warehouse": {  
                        "name": $("#branch_code").val(),
                        "address":""
                    }
                }
            });
        }); 

        $('#submitBtn').click(function(){
            $.ajax({
                type: 'POST',
                url: "http://localhost/api/product",
                headers: {
                    "Accept":"application/json",
                    "Authorization":Authorization
                },
                data: {
                    // "product_id": proID,
                    "category_id": $("#prod_cat").val(),
                    "code": $("#prod_code").val(),
                    "name": $("#prod_name").val(),
                    "unitName": $("#prod_unit").val(),
                    "description": $("#prod_detail").val(),
                    "detail": {  
                        "warehouse_id": $("#prod_branch").val(),
                        "quantity": $("#prod_amount").val(),
                        "costPrice": $("#prod_price_buy").val(),
                        "salePrice": $("#prod_price_sale").val()
                    }
                },
                success: function(data) {
                    console.log(data)
                }
            }); 
        });
    });

</script>

@endsection

