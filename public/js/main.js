$(document).ready(function() {

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
            "data": "prodUnit",
            "className": "text-right",
            "width": "12%"
        },
        {
            render: function (data, type, full, meta) {
                return `<div>   
                                <a href="#" id="tbEditBtn" data-toggle="modal" data-target="#edit_modal">
                                    Edit
                                </a> |
                                <a href="#" id="tbDeleteBtn" class="delete-btn">
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
});

function initialDataTable () {

    $.ajax({
        type: 'GET',
        url: "http://localhost/api/product",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response) {
        var data = response ; 
        for (var i = 0 ; i < data.length ; i++ ) {
            $('#prod_table').DataTable().row.add({
                "prodID": data[i].code,
                "prodName": data[i].name,
                "prodBuyPrice": '',
                "prodSalePrice": '',
                "prodAmount": '',
                "prodUnit": data[i].unitName,
                "btn": ""
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

    var prodCat = $("#prod_cat").val();
    var prodName = $("#prod_name").val();
    var prodDetail = $("#prod_detail").val() ;
    var prodBranch = $("#prod_branch").val();
    var quantity = $("#prod_amount").val();
    var costPrice = $("#prod_price_buy").val();
    var salePrice = $("#prod_price_sale").val();

    $.ajax({
        type: 'POST',
        url: "http://localhost/api/product",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        },
        data: {
            "product": {
                "category_id": prodCat,
                "code": prod_code,
                "name": prodName,
                "unitName": unit,
                "description": prodDetail,
                "detail": {
                    "warehouse_id": prodBranch,
                    "quantity": quantity,
                    "costPrice": costPrice,
                    "salePrice": salePrice
                }
            }
        }
    }).done(function (response) {
        if (response.created) {

            table.row.add({
                "prodID": prod_code,
                "prodName": prodName,
                "prodBuyPrice": costPrice,
                "prodSalePrice": salePrice,
                "prodAmount": quantity,
                "prodUnit": unit,
                "btn": ""
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

        // Close Modal
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