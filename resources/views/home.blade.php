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

<div class="container fixed-bottom main">
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
                        <table id="prod_table" class="table-style">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อ</th>
                                    <th>จำนวน</th>
                                    <th>ราคาต่อหน่วย</th>
                                    <th>คำอธิบายเพิ่มเติม</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    
                                    <td>กระเบื้องดำ</td>
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        23
                                    </td>
                                    <td>
                                        N/A
                                    </td>
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
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        23
                                    </td>
                                    <td>
                                        N/A
                                    </td>
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
                <div class="modal-body">
                    <form id="form_series">
                        <div class="row m-t-20">
                            <div class="col-md-4">
                                <h4>ชื่อสินค้า</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group p-10">
                                    <input type="text" class="form-control" id="" placeholder="กรอกชื่อสินค้าที่ต้องการ">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>จำนวน</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group p-10">
                                    <input type="text" class="form-control" id="" placeholder="กรอกจำนวน" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>ราคาต่อหน่วย</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group p-10">
                                    <input type="text" class="form-control" id="" placeholder="กรอกราคาต่อหน่วย">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <h4 style="margin-bottom: 0;">คำอธิบายเพิ่มเติม</h4>
                                <span style="color: #ccc; font-size: 16px;">(ตัวเลือก)</span>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group p-10">
                                    <textarea type="text" class="form-control" id="" placeholder="กรอกคำอธิบาย"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-md-4">
                                <div id='name' style="display: none;">
                                    <h4 style="margin-bottom: 0;">รายละเอียดเพิ่มเติม</h4>
                                </div>
                            </div>
                            <div class="col-md-8" >
                                <table class="table form-group">
                                    <tbody id="optional">
                                            
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <button class="btn btn-link pull-right" onclick="addMoreDetail()" type="button"> 
                            เพิ่มรายละเอียด <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" onclick="submit()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class=" container alert alert-info" role="alert">
    API Token : <input value="{{$token}}"></input>
</div>
@endsection

