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
                <button class="btn btn-primary pull-right m-b-20" type="button" data-toggle="modal" data-target="#addProd">
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
                                <tr>
                                    <td>1</td>
                                    <td>กระเบื้องดำ</td>
                                    <td>23</td>
                                    <td>2</td>
                                    <td>N/A</td>
                                    <td>กล่อง</td>
                                    <td class="table-btn">
                                        <div>
                                            <a href="#">
                                                แก้ไข
                                            </a> |
                                            <a href="#">
                                                ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>ผ้าปู</td>
                                    <td>23</td>
                                    <td>1</td>
                                    <td>N/A</td>
                                    <td>ชิ้น</td>
                                    <td class="table-btn">
                                        <div>
                                            <a href="#">
                                                แก้ไข
                                            </a> |
                                            <a href="#">
                                                ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
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
                                    <select class="form-control" id="prod_cat" name="product_catagories" value="null">
                                        <option>ไม่มีหมวดหมู่</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-link" type="button"> 
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
                                <button class="btn btn-link" type="button"> 
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
                        <button type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
               
                
            </div>
        </div>
    </div>
</div>


<div class=" container alert alert-info" role="alert">
    API Token : <input value="{{$token}}"></input>
</div>
@endsection

