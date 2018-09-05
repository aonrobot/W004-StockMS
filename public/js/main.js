var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

var table = $('#prod_table').DataTable({
    "columnDefs": [{
        "orderable": false,
        "targets": 6
    }],
    columns: [{
        "data": "prodCode",
        "width": "10%",
        "render": function (data) {
            return `<span id ="${data}">${data}</span>`;
        }
    },
    {
        "data": "prodName",
        "render": function (data, type, full, meta) {
            return `
                <span class="edit-btn" data-prodID="${full.prodID}">
                    ${data}
                </span>
            `;
        }
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
    // {
    //     "ordering": false,
    //     render: function (data, type, full, meta) {
    //         // return `
    //         //         <input class="qtyAmountInput" type="number" style="width:40px" value="0" data-id="${full.invenID}"/>
    //         //         <button class="btn btn-outline-primary btn-xs increaseOneQtyBtn" data-id="${full.invenID}" data-row="${meta.row}" data-col="${meta.col - 1}"><i class="fa fa-plus"></i></button>
    //         //         <button class="btn btn-outline-danger btn-xs decreaseOneQtyBtn" data-id="${full.invenID}" data-row="${meta.row}" data-col="${meta.col - 1}"><i class="fa fa-minus"></i></button>
    //         // `;
    //     },
    // },
    {
        "data": "prodUnit",
        "className": "text-right",
        "width": "12%"
    },
    {
        render: function (data, type, full, meta) {
            return `<span>${full.prodBuyPrice * full.prodAmount} บาท</span>`;
        },
    },
    {
        render: function (data, type, full, meta) {
            return `<div>   
                        <a href="#" onclick="editProd(${full.btn})" class="edit-btn" >
                            Edit
                        </a> |
                        <a href="#" class="delete-btn" >
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

function defaultValue (elem, decimal){

    var elem = $('#'+elem);
    var value = elem.val()

    if (elem.val().length === 0) {

        elem.val((0).toFixed(decimal));
        return (0).toFixed(decimal) ;
    }else {
        return value;
    }
}
function initialDataTable() {
    table.clear()

    $('body').busyLoad("show", busyBoxOptions);

    $.ajax({
        type: 'GET',
        url: "api/product",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response) {

        var data = response;

        for (var i = 0; i < data.length; i++) {
            $('#prod_table').DataTable().row.add({
                "prodID": data[i].product_id,
                "prodCode": data[i].code,
                "prodName": data[i].name,
                "prodBuyPrice": data[i].inventory.costPrice,
                "prodSalePrice": data[i].inventory.salePrice,
                "prodAmount": data[i].inventory.quantity,
                "prodUnit": data[i].unitName,
                "invenID": data[i].inventory.id,
                "btn": data[i].product_id
            }).draw();
        }

        $('body').busyLoad("hide", busyBoxOptions);
        createChangeQty_event();
    });
}

$("#form_prod").submit(function (e) {

    event.preventDefault();

    $('body').busyLoad("show", busyBoxOptions);

    var unit = '';
    var prod_code;

    if ($("#prod_unit").val().length === 0) { unit = 'N/A' }
    else { unit = $("#prod_unit").val() }

    if ($("#prod_code").val().length === 0) { prod_code = prodCode }
    else { prod_code = $("#prod_code").val() }

    var param = {
        prodCat: $("#prod_cat").val(),
        prodCode: prod_code,
        prodName: $("#prod_name").val(),
        prodDetail: $("#prod_detail").val(),
        prodBranch: $("#prod_branch").val(),
        quantity: defaultValue('prod_amount', 0),
        costPrice: defaultValue('prod_price_buy', 2),
        salePrice:  defaultValue('prod_price_sale', 2),
        unit: unit
    }


    $.ajax({
        type: 'POST',
        url: "api/product",
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

        if (response.created) {

            table.row.add({
                "prodCode": param.prodCode,
                "prodName": param.prodName,
                "prodBuyPrice": param.costPrice,
                "prodSalePrice": param.salePrice,
                "prodAmount": param.quantity,
                "prodUnit": param.unit,
                "invenID": response.inventory_id,
                "btn": response.product_id
            }).draw();

            $('#addProd').modal('hide');

            $('body').busyLoad("hide", busyBoxOptions);

        } else {
            $('#prod_code').addClass('is-invalid');
            $('#prod_code_warning').html(response.message);
        }
    });
});

$('#prod_table tbody').on('click', '.delete-btn', function (mm) {

    if (confirm("คุณยืนยันที่จะลบข้อมูล?")) {
        
        $('body').busyLoad("show", busyBoxOptions);

        var id = table.row($(this).parents('tr')).data()

        $.ajax({
            method: 'DELETE',
            url: "api/product/" + id.btn,
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            }
        }).done(function (response) {

            if(!response.destroyed) {

                console.log('Error');
                return false;
            }

            $('body').busyLoad("hide", busyBoxOptions);

        });
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
        // Add Catagories
        $.ajax({
            type: 'POST',
            url: "api/category",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            },
            data: {
                "category": {
                    "name": $("#cat_code").val(),
                    "description": ""
                }

            }
        }).done(function () {
            displayCat();
        });
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
            url: "api/warehouse",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            },
            data: {
                "warehouse": {
                    "name": $("#branch_code").val(),
                    "address": ""
                }
            }
        }).done(function () {
            displayBranch();
        });

        $('#prod_branch_modal').modal('hide')
    }
});

$('#addProd').on('show.bs.modal', function (event) {

    $('#addProd').find('form')[0].reset();
})


// EDIT
function editProd(data) {

    $('#edit_id').val(data);
    $.ajax({
        method: 'GET',
        url: "api/product/" + data,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response) {

        var data = response;

        branch.render('edit_prod_branch', data.inventory[0].warehouse_id);
        catagory.render('edit_prod_cat', data.category_id);

        $('#edit_prod_code').val(data.code);
        $('#edit_prod_name').val(data.name);
        // $('#edit_prod_cat').val(data.category_id);
        $('#edit_prod_price_buy').val(Number(data.inventory[0].costPrice));
        $('#edit_prod_price_sale').val(Number(data.inventory[0].salePrice));
        $('#edit_prod_unit').val(data.unitName);
        $('#edit_prod_amount').val(Number(data.inventory[0].quantity));
        // $('#edit_prod_branch').val(data.inventory[0].warehouse_id);
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
        prodName: $("#edit_prod_name").val(),
        prodDetail: $("#edit_prod_detail").val(),
        prodBranch: $("#edit_prod_branch").val(),
        quantity: $("#edit_prod_amount").val(),
        costPrice: $("#edit_prod_price_buy").val(),
        salePrice: $("#edit_prod_price_sale").val(),
        unit: unit
    }
    var edit_id = $('#edit_id').val();

    $.ajax({
        type: 'PUT',
        url: "api/product" + "/" + edit_id,
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

var catagory = {

    postAPI(value) {

        return $.ajax({
            type: 'POST',
            url: "api/category",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            },
            data: {
                "category": {
                    "name": value,
                    "description": ""
                }

            }
        });
    },
    getAPI() {
        return $.ajax({
            method: 'GET',
            url: "api/category",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            }
        })
    },
    add(e, textElem, inputElem, selectElem, modalElem) {
        e.preventDefault();

        var catagory_name = $('#' + inputElem);
        if (catagory_name.val().length === 0) {
            validate(textElem, inputElem);
        } else {
            catagory.postAPI(catagory_name.val()).done(function (response) {
                catagory.render(selectElem, 'last');
                catagory.closeModal(modalElem);
            })
        }
    },
    render(elem, defaultSelected) {

        var elem = $('#' + elem);

        catagory.getAPI().done(function (res) {

            var select = $("<select>");
            var option = "";
            // Clear option in select
            elem.empty();
            var selected = defaultSelected;

            if (selected === 'last') {

                selected = res.length;

                for (var i = 0; i < res.length; i++) {

                    if ((selected - 1) === i) {
                        select.append(
                            `<option value="${res[i].id}" selected="true"> ${res[i].name} </option>`
                        );
                    } else {
                        select.append(
                            `<option value="${res[i].id}"> ${res[i].name} </option>`
                        );
                    }
                }
            } else {
                for (var i = 0; i < res.length; i++) {

                    option += `<option value="${res[i].id}"> ${res[i].name} </option>`;
                }
            }

            select.append(option);
            $(elem).append(select.html());
            $(elem).val(selected);
            // Append to DOM


        });
    },
    closeModal(modalID) {
        $('#' + modalID).modal('hide')
    }
}
function validate(textElem, inputElem) {

    $('#' + textElem).append('กรุณาใส่หมวดหมู่');
    $('#' + inputElem).addClass('is-invalid');
}
function clear(textElem, inputElem) {

    $('#' + textElem).empty();
    $('#' + inputElem).val('');
    $('#' + inputElem).removeClass('is-invalid');
}
// Clear Edit Add Catagory Modal
$('#edit_prod_cat_modal').on('show.bs.modal', function (event) {
    clear('edit_text_warning', 'edit_cat_code');
})

var branch = {

    postAPI(value) {

        return $.ajax({
            type: 'POST',
            url: "api/warehouse",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            },
            data: {
                "warehouse": {
                    "name": value,
                    "address": ""
                }
            }
        });
    },
    getAPI() {
        return $.ajax({
            method: 'GET',
            url: "api/warehouse",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            }
        })
    },
    render(elem, defaultSelected) {

        var elem = $('#' + elem);

        branch.getAPI().done(function (res) {

            var select = $("<select>");
            var option = "";
            // Clear option in select
            elem.empty();
            var selected = defaultSelected;

            if (selected === 'last') {

                selected = res.length;

                for (var i = 0; i < res.length; i++) {

                    if ((selected - 1) === i) {
                        select.append(
                            `<option value="${res[i].warehouse_id}" selected="true"> ${res[i].name} </option>`
                        );
                    } else {
                        select.append(
                            `<option value="${res[i].warehouse_id}"> ${res[i].name} </option>`
                        );
                    }
                }
            } else {
                for (var i = 0; i < res.length; i++) {

                    option += `<option value="${res[i].warehouse_id}"> ${res[i].name} </option>`;
                }
            }
            select.append(option);
            $(elem).append(select.html());
            $(elem).val(selected);
        });
    },
    add(e, textElem, inputElem, selectElem, modalElem) {
        e.preventDefault();

        var branch_name = $('#' + inputElem);
        if (branch_name.val().length === 0) {
            validate(textElem, inputElem);
        } else {
            branch.postAPI(branch_name.val()).done(function (response) {
                branch.render(selectElem, 'last');
                branch.closeModal(modalElem);
            })
        }
    },
    closeModal(modalID) {
        $('#' + modalID).modal('hide')
    }
}

$('#edit_prod_branch_modal').on('show.bs.modal', function (event) {
    clear('edit_text_warning_branch', 'edit_branch_code');
});


/**
 * 
 * Transaction Modal
 * 
 */

