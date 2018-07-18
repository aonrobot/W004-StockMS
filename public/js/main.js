var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

    var table = $('#prod_table').DataTable({
        "columnDefs": [{
            "orderable": false,
            "targets": 6
        }],
        columns: [{
            "data": "prodID",
            "width": "10%",
            "render": function (data) {
                return `<span id ="${data}">${data}</span>`;
            }
        },
        {
            "data": "prodName"
        },
        {
            "data": "prodBuyPrice",
            "className": "text-right",
            "width": "12%"
        },
        {
            "data": "prodSalePrice",
            "className": "text-right",
            "width": "12%"
        },
        {
            "data": "prodAmount",
            "className": "text-right",
            "width": "12%"
        },
        {
            "ordering": false,
            render: function (data, type, full, meta) {
                return `
                    <input class="qtyAmountInput" type="number" style="width:40px" value="0" data-id="1"/>
                    <button class="btn btn-outline-primary btn-xs increaseOneQtyBtn" data-id="1" data-row="${meta.row}" data-col="${meta.col - 1}"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-outline-danger btn-xs decreaseOneQtyBtn" data-id="1" data-row="${meta.row}" data-col="${meta.col - 1}"><i class="fa fa-minus"></i></button>
                `;
            },
        },
        {
            "data": "prodUnit",
            "className": "text-right",
            "width": "12%"
        },
        {
            render: function (data, type, full, meta) {
                return `<div>   
                            <a href="#" onclick="editProd(${full.btn})" class="edit-btn">
                                Edit
                            </a> |
                            <a href="#" class="delete-btn" onclick="deleteProd(${full.btn})" >
                                Delete
                            </a>
                        </div>`;
            },
            className: "table-btn",
            width: "20%"
        }
        ],
        "scrollX": true
    });
    initialDataTable();

function initialDataTable () {
    table
    .clear()
    

    $.ajax({
        type: 'GET',
        url: "http://localhost/api/product",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response) {
        console.log(response);
        var data = response ; 

        for (var i = 0 ; i < data.length ; i++ ) {
            $('#prod_table').DataTable().row.add({
                "prodID": data[i].code,
                "prodName": data[i].name,
                "prodBuyPrice": data[i].inventory.costPrice ,
                "prodSalePrice": data[i].inventory.salePrice,
                "prodAmount": data[i].inventory.quantity,
                "prodUnit": data[i].unitName,
                "btn": data[i].product_id
            }).draw();
        }
        
    });
}

$("#form_prod").submit(function (e) {

    event.preventDefault();
    // 
    var unit = '';
    var prod_code;

    if ($("#prod_unit").val().length === 0) { unit = 'N/A' }
    else { unit = $("#prod_unit").val() }

    if ($("#prod_code").val().length === 0) { prod_code = prodID }
    else { prod_code = $("#prod_code").val() }

    var param = {
        prodCat: $("#prod_cat").val(),
        prodCode: prod_code,
        prodName : $("#prod_name").val(),
        prodDetail : $("#prod_detail").val() ,
        prodBranch : $("#prod_branch").val(),
        quantity : $("#prod_amount").val(),
        costPrice : $("#prod_price_buy").val(),
        salePrice : $("#prod_price_sale").val(),
        unit: unit
    }
   

    $.ajax({
        type: 'POST',
        url: "http://localhost/api/product",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        },
        data: {
            "product": {
                "category_id": param.prodCat,
                "code": param.prodCode,
                "name": param.prodName,
                "unitName": param.unit,
                "description": param.prodDetail,
                "detail": {
                    "warehouse_id": param.prodBranch,
                    "quantity": param.quantity,
                    "costPrice": param.costPrice,
                    "salePrice": param.salePrice
                }
            }
        }
    }).done(function (response) {
        console.log(response);
        if (response.created) {

            table.row.add({
                "prodID": param.prodCode,
                "prodName": param.prodName,
                "prodBuyPrice": param.costPrice,
                "prodSalePrice": param.salePrice,
                "prodAmount": param.quantity,
                "prodUnit": param.unit,
                "btn": response.product_id
            }).draw();
        
            $('#addProd').modal('hide');
        
        } else {
            $('#prod_code').addClass('is-invalid');
            $('#prod_code_warning').html(response.message);
        }
    });
});

$('#prod_table tbody').on('click', '.delete-btn', function () {
    var txt;
    if (confirm("คุณยืนยันที่จะลบข้อมูล?")) {

        table
            .row($(this).parents('tr'))
            .remove()
            .draw();
    } else {

        return;
    }


});


$('#prod_cat_modal').on('show.bs.modal', function (event) {

    var text_warning = $('#text_warning');
    var catagory_name = $('#cat_code');
    var catagory_list = $('#prod_cat');

    text_warning.empty();
    catagory_name.val('');
    if (catagory_name.hasClass('is-invalid')) {

        catagory_name.removeClass('is-invalid');
    }
})

$('#prod_branch_modal').on('show.bs.modal', function (event) {

    var warning = $('#text_warning_branch');
    var branch_name = $('#branch_code');
    var branch_list = $('#prod_branch');

    warning.empty();
    branch_name.val('');

    if (branch_name.hasClass('is-invalid')) {

        branch_name.removeClass('is-invalid');
    }
})


$('#addProdCat').click(function (e) {
    e.preventDefault();


    var text_warning = $('#text_warning');
    var catagory_name = $('#cat_code');
    var catagory_list = $('#prod_cat');

    var catagory_name_value = catagory_name.val();
    // clear
    text_warning.empty();

    // if user not type any data
    if (catagory_name.val().length === 0) {

        catagory_name.addClass('is-invalid');
        text_warning.append('กรุณาใส่หมวดหมู่');

    } else {

        catagory_list.append(`<option selected="true"> ${catagory_name_value} </option>`);

        // Close Modal
        $('#prod_cat_modal').modal('hide')

    }
});

$('#addBranch').click(function (e) {
    e.preventDefault();

    var warning = $('#text_warning_branch');
    var branch_name = $('#branch_code');
    var branch_list = $('#prod_branch');

    var branch_name_value = branch_name.val();
    // clear
    warning.empty();

    // if user not type any data
    if (branch_name.val().length === 0) {

        branch_name.addClass('is-invalid');
        warning.append('กรุณาใส่คลังสินค้า');

    } else {

        branch_list.append(`<option selected="true"> ${branch_name_value} </option>`);
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
        }).done( function () {
            displayBranch();
        });

        $('#prod_branch_modal').modal('hide')
    }
});

$('#addProd').on('show.bs.modal', function (event) {

    $('#addProd').find('form')[0].reset();
})


// EDIT  Catagory
$('#edit_prod_cat_modal').on('show.bs.modal', function (event) {

    var text_warning = $('#edit_text_warning');
    var catagory_name = $('#edit_cat_code');
    var catagory_list = $('#edit_prod_cat');

    text_warning.empty();
    catagory_name.val('');
    if (catagory_name.hasClass('is-invalid')) {

        catagory_name.removeClass('is-invalid');
    }
})

$('#edit_addProdCat').click(function (e) {
    e.preventDefault();


    var text_warning = $('#edit_text_warning');
    var catagory_name = $('#edit_cat_code');
    var catagory_list = $('#edit_prod_cat');

    var catagory_name_value = catagory_name.val();
    // clear
    text_warning.empty();

    // if user not type any data
    if (catagory_name.val().length === 0) {

        catagory_name.addClass('is-invalid');
        text_warning.append('กรุณาใส่หมวดหมู่');

    } else {

        catagory_list.append(`<option value="${catagory_name_value}" selected="true"> ${catagory_name_value} </option>`);

        // Close Modal
        $('#edit_prod_cat_modal').modal('hide')

    }
});

// EDIT BRANCH

$('#edit_prod_branch_modal').on('show.bs.modal', function (event) {

    var warning = $('#edit_text_warning_branch');
    var branch_name = $('#edit_branch_code');
    var branch_list = $('#edit_prod_branch');

    warning.empty();
    branch_name.val('');

    if (branch_name.hasClass('is-invalid')) {

        branch_name.removeClass('is-invalid');
    }
})

$('#edit_addBranch').click(function (e) {
    e.preventDefault();

    var warning = $('#edit_text_warning_branch');
    var branch_name = $('#edit_branch_code');
    var branch_list = $('#edit_prod_branch');

    var branch_name_value = branch_name.val();
    // clear
    warning.empty();

    // if user not type any data
    if (branch_name.val().length === 0) {

        branch_name.addClass('is-invalid');
        warning.append('กรุณาใส่คลังสินค้า');

    } else {

        branch_list.append(`<option value="${branch_name_value}" selected="true"> ${branch_name_value} </option>`);

        // Close Modal
        $('#edit_prod_branch_modal').modal('hide')

    }
});

// EDIT
function editProd (data) {
    
    getBranch();
    getCat();

    $('#edit_id').val(data);
    $.ajax({
        method: 'GET',
        url: "http://localhost/api/product/" + data,
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        }
    }).done(function (response) {
        var data = response;
        console.log(response);
        $('#edit_prod_code').val(data.code);
        $('#edit_prod_name').val(data.name);
        $('#edit_prod_cat').val(data.category_id);
        $('#edit_prod_price_buy').val(Number(data.inventory[0].costPrice));
        $('#edit_prod_price_sale').val(Number(data.inventory[0].salePrice));
        $('#edit_prod_unit').val(data.unitName);
        $('#edit_prod_amount').val(Number(data.inventory[0].quantity));
        $('#edit_prod_branch').val(data.inventory[0].warehouse_id);
        $('#edit_prod_detail').val(data.description);

        $("#edit_modal").modal()
    });
}

// SUBMIT EDIT
$("#edit_form_prod").submit(function (e) {

    event.preventDefault();
    // 
    var unit = '';

    if ($("#edit_prod_unit").val().length === 0) { unit = 'N/A' }
    else { unit = $("#edit_prod_unit").val() }

    var param = {
        prodCat: $("#edit_prod_cat").val(),
        prodCode: $("#edit_prod_code").val(),
        prodName : $("#edit_prod_name").val(),
        prodDetail : $("#edit_prod_detail").val() ,
        prodBranch : $("#edit_prod_branch").val(),
        quantity : $("#edit_prod_amount").val(),
        costPrice : $("#edit_prod_price_buy").val(),
        salePrice : $("#edit_prod_price_sale").val(),
        unit: unit
    }
    var edit_id = $('#edit_id').val();

    $.ajax({
        type: 'PUT',
        url: "http://localhost/api/product" + "/" + edit_id,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        },
        data: {
            "product": {
                "category_id": param.prodCat,
                "name": param.prodName,
                "unitName": param.unit,
                "description": param.prodDetail,
                "detail": {
                    "warehouse_id": param.prodBranch,
                    "quantity": param.quantity,
                    "costPrice": param.costPrice,
                    "salePrice": param.salePrice
                }
            }
        }
    }).done(function (response) {

        if (response.updated) {

            initialDataTable();
            $('#edit_modal').modal('hide');
        }
    });
});

function getCat() {

    $.ajax({
        method: 'GET',
        url: "http://localhost/api/category",
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        }
    }).done(function (response) {

        addSelectCat(response);

    });

}
function getBranch() {
    $.ajax({
        method: 'GET',
        url: "http://localhost/api/warehouse",
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        }
    }).done(function (response) {

        addSelectBranch(response);

    });
}
function addSelectCat(data) {
    
    var select = $('#edit_prod_cat');

    for(var i = 0 ; i < data.length ; i++) {
        select.append(
            `<option value="${data[i].id}">${ data[i].name }</option> `
        ); 
    }
}

function addSelectBranch(data) {
    
    var select = $('#edit_prod_branch');

    for(var i = 0 ; i < data.length ; i++) {
        select.append(
            `<option value="${data[i].warehouse_id}">${ data[i].name }</option> `
        ); 
    }
}

function deleteProd (id) {
    console.log(id);
    $.ajax({
        method: 'DELETE',
        url: "http://localhost/api/product/" + id,
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        }
    }).done(function (response) {

        console.log(response);

    });
}   
