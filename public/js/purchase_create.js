var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');
var ROW_INDEX = 1;


$(document).ready(function () {

    $("#purchase_date").datepicker("setDate", new Date());
    $("#purchase_date").datepicker({
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        endDate: '+1d',
        orientation: "right",
    });

    get.purchase_id().done(function (res) {
        $("#purchase_id").val(res);
    });
});

// Initial // Bind Auto Complete
var columnCode = $("#row_1 .td__prodCode input[type=search]");
var columnName = $("#row_1 .td__prodName input[type=search]");

    columnCode.map(function (obj, elem) {
        $(elem).attr( {
            'onkeyup': `search(this, 0, 1)`
        });
    })
    columnName.map(function (obj, elem) {
        $(elem).attr( {
            'onkeyup': `search(this, 1, 1)`
        });
    })

function addRow() {
    ROW_INDEX += 1;

    var $table_body = $("#table_body");
    $table_body.append(`
            <tr id="row_${ ROW_INDEX}">
                <td class="td__btn-add">
                    <button id="btn_${ ROW_INDEX}" class="btn-default btn-circle" 
                            data-toggle="modal" data-target=".bd-example-modal-lg">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                </td>
                <td class="text-right td__index">
                    ${ ROW_INDEX}
                </td>
                <td class="td__prodCode">
                    <input type="search" class="form-control"/>
                </td>
                <td class="td__prodName">
                    <input type="search" class="form-control"/>
                </td>
                <td class="td__unitValue">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text pointer" 
                                onclick="row_value.minus(${ ROW_INDEX})">
                                -
                            </span>
                        </div>

                        <input type="number" id="unitValue_${ ROW_INDEX}" value="0" 
                                class="text-center form-control form__number"
                                onchange="row_value.total(${ ROW_INDEX })"  />

                        <div class="input-group-append">
                            <span class="input-group-text pointer"
                                onclick="row_value.plus(${ ROW_INDEX})">
                                +
                            </span>
                        </div>
                    </div>
                    <small class="float-right"></small>
                </td>
                <td class="td__amount">
                    <input type="number" class="form-control text-right" onchange="row_value.total(${ ROW_INDEX})" />
                </td>
                <td class="text-right td__unit">
                    
                </td>
                <td class="text-right td__total">
                    0.00
                </td>
                <td class="td__btn-remove"> 
                    <i class="fa fa-minus-circle pointer btn-remove-row" aria-hidden="true" onclick="removeRow(${ ROW_INDEX})"></i>
                </td>
            </tr>
        `);
    // Bind Auto Complete
    var columnCode = $("#row_" + ROW_INDEX + " .td__prodCode input[type=search]");
    var columnName = $("#row_" + ROW_INDEX + " .td__prodName input[type=search]");
    
    columnCode.map(function (obj, elem) {
        $(elem).attr( {
            'onkeyup': `search(this, 0, ${ ROW_INDEX })`
        });
    })
    columnName.map(function (obj, elem) {
        $(elem).attr( {
            'onkeyup': `search(this, 1, ${ ROW_INDEX })`
        });
    })
}

function search (elem , searchType ,idx) {
    // TODO: Combined 2 searchType
    var TEXT_SEARCH = $(elem).val();
    var autocompleteOptions = {
        minLength: 1,
        source: function (request, response) {
            $.ajax({
                type: "GET",
                url: "/api/product/service/autoComplete?searchType=" + searchType + "&q=" + TEXT_SEARCH,
                headers: {
                    "Accept": "application/json",
                    "Authorization": Authorization
                },
                success: function (data) {

                    var arr = data.map(function (obj) {

                        if (searchType){
                            return { 
                                data: obj, 
                                label: obj.name 
                            };
                        }
                        else {
                            return { 
                                data: obj, 
                                label: obj.code 
                            };
                        } 
                    });
                    response(arr);
                }
            });
        },
        select: function(e, ui) {
            e.preventDefault();

            var $row_elem = $("#row_" + idx);
                $($row_elem).find(".td__prodCode input").val(ui.item.data.code);
                $($row_elem).find(".td__prodName input").val(ui.item.data.name);
                $($row_elem).find(".td__amount input").val(ui.item.data.costPrice);
                $($row_elem).find(".td__unitValue small").html(
                    `<span>จำนวนคงเหลือ <strong>${ ui.item.data.sumQuantity }</strong></span>`
                );
                $($row_elem).find(".td__unit").html(`
                        <span class="badge badge-light">${ ui.item.data.unitName }</span>
                    `);
            // Count Total 
            row_value.total(idx);
        }
    };

    $(elem).focus().autocomplete(autocompleteOptions);
}

function removeRow(idx) {

    if (ROW_INDEX === 0) return;

    var $delete_row = $("#row_" + idx);
    $delete_row.remove();

    $("#table_body tr").filter(function (obj, elem) {

        var index = elem.id.split('_')[1];
        var reduce_index = index - 1;
        if (index > idx) {

            elem.id = 'row_' + (reduce_index);

            // Re-render First Column a.k.a. = 0
            $("#" + elem.id + " td:eq(0)").html(`
                <button id="btn_${ reduce_index}" class="btn-default btn-circle" 
                data-toggle="modal" data-target=".bd-example-modal-lg">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>`);

            // Re-render Second Column a.k.a. = 1
            $("#" + elem.id + " td:eq(1)").html(reduce_index);

            // Re-render Last Column a.k.a. = 8
            $("#" + elem.id + " td:eq(8)").html(`
                <i class="fa fa-minus-circle pointer btn-remove-row" 
                    aria-hidden="true" 
                    onclick="removeRow(${ reduce_index})">
                </i>`);

            // Re-render Plus & Minus Button
            var oldValue_elem = $("#" + elem.id + " td.td__unitValue input");
            var oldValue = oldValue_elem.val();
            var oldAmount = $("#" + elem.id + " td.td__unitValue small").html();

            $("#" + elem.id + " td.td__unitValue").html(`
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text pointer" 
                            onclick="row_value.minus(${ reduce_index})">
                            -
                        </span>
                    </div>

                    <input type="number" id="unitValue_${ reduce_index}" value="${oldValue}" 
                                class="text-center form-control form__number" 
                                onchange="row_value.total(${ reduce_index})" />

                    <div class="input-group-append">
                        <span class="input-group-text pointer"
                            onclick="row_value.plus(${ reduce_index})">
                            +
                        </span>
                    </div>
                </div>
                <small class="float-right">${oldAmount}</small>
            `);

            // Re-render UnitValue 
            var oldAmount_elem = $("#" + elem.id + " td.td__amount");
            var oldAmount = $(oldAmount_elem).find("input").val();
            $(oldAmount_elem).html(`
                <input type="number" class="form-control text-right" onchange="row_value.total(${ reduce_index})" value="${oldAmount}" />`);

            // Bind Auto Complete
            var columnCode = $("#row_" + reduce_index + " .td__prodCode input[type=search]");
            var columnName = $("#row_" + reduce_index + " .td__prodName input[type=search]");
                columnCode.map(function (obj, elem) {
                    $(elem).attr( {
                        'onkeyup': `search(this, 0, ${ reduce_index })`
                    });
                })
                columnName.map(function (obj, elem) {
                    $(elem).attr( {
                        'onkeyup': `search(this, 1, ${ reduce_index })`
                    });
                })
            
        }
    });
    ROW_INDEX -= 1;
    // Sum purchase Total
    sumTotal();
}

$('#product_modal').on('shown.bs.modal', function (e) {
    // This is ID of Modal Button
    var btn_id = e.relatedTarget.id;
    initialDataTable(btn_id);
});


var modal_table = $('#modal_prod_table').DataTable({
    "columnDefs": [{
        "orderable": false,
        "targets": 5
    }],
    columns: [
        {
            "data": "prodID",
            "render": function (data) {
                return `<span id ="${data}">${data}</span>`;
            },
        },
        {
            "data": "prodName",
        },
        {
            "data": "prodSalePrice",
            "className": "text-right",
        },
        {
            "data": "prodAmount",
            "className": "text-right",
        },
        {
            "data": "prodUnit",
            "className": "text-right",
        },
        {
            "ordering": false,
            render: function (data, type, full, meta) {

                return `<div>   
                            <button onclick="addProdInRow(${meta.row} ,${full.btn.target} )" 
                                    class="btn btn-primary edit-btn pointer" >
                                เลือก
                            </button>
                        </div>`;
            },
            className: "table-btn"
        }
    ],
    "scrollX": true
});


function initialDataTable(btn_id) {

    modal_table
        .clear()

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
            $('#modal_prod_table').DataTable().row.add({
                "prodID": data[i].code,
                "prodName": data[i].name,
                "prodSalePrice": data[i].inventory.costPrice,
                "prodAmount": data[i].inventory.quantity,
                "prodUnit": data[i].unitName,
                "btn": {
                    "id": data[i].product_id,
                    "target": btn_id
                }
            }).draw();
        }
        $('body').busyLoad("hide", busyBoxOptions);
        // createChangeQty_event();
    });
}

function addProdInRow(rowIdx, target) {

        var row_data = modal_table.row( rowIdx ).data();  /// json object
        var targetID = target.id.split('_')[1]; /// number ex.1,2,3,  
        var $row_elem = $("#row_" + targetID);

        $($row_elem).find(".td__prodCode input").val(row_data.prodID);
        $($row_elem).find(".td__prodName input").val(row_data.prodName);
        $($row_elem).find(".td__amount input").val(row_data.prodSalePrice);
        $($row_elem).find(".td__unitValue small").html(
            `<span>จำนวนคงเหลือ <strong>${ row_data.prodAmount }</strong></span>`
        );

        $($row_elem).find(".td__unit").html(`
                <span class="badge badge-light">${ row_data.prodUnit}</span>
            `);

        // Count Total 
        row_value.total(targetID);

        // Close Modal
        $('#product_modal').modal('hide');
    }

var row_value = {

    plus: function (idx) {

        var $unitValue = $("#unitValue_" + idx);
        var $unitValue_val = parseInt($unitValue.val());

        if (isNaN($unitValue_val)) {
            $unitValue_val = 0;
        }
        $unitValue.val($unitValue_val += 1);
        row_value.total(idx);
    },

    minus: function (idx) {

        var $unitValue = $("#unitValue_" + idx);
        var $unitValue_val = parseInt($unitValue.val());

        if (isNaN($unitValue_val)) {
            $unitValue_val = 0;
        }
        $unitValue.val($unitValue_val -= 1);
        row_value.total(idx);
    },
    total: function (idx) {

        var $row_elem = $("#row_" + idx);
        var $unitValue_val = parseInt($("#unitValue_" + idx).val());
        var $amount = $($row_elem).find(".td__amount input").val();
        var $total = $($row_elem).find(".td__total");

        if (isNaN($amount) ||
            isNaN($unitValue_val) ||
            !$amount.length) return;

        $($total).html((Number($amount) * Number($unitValue_val)).toFixed(2));

        // Sum purchase Total
        sumTotal();
    }
}



function sumTotal() {

    var sum = 0;

    $(".td__total").map(function (obj, elem) {

        sum += Number($(elem).html());
    });

    $(".PURCHASE_TOTAL").map(function (obj, elem) {

        $(elem).html(sum.toFixed(2));
    });
}


function createPurchase() {

    var id = $("#purchase_id").val();
    var date = $("#purchase_date").val();
    // var reference = $("#purchase_ref").val();
    var warehouse_id = Number($("#warehouse_select").val()) ? Number($("#warehouse_select").val()) : null;
    var table_body = $("#table_body");
    var checkNullValue = true;
    var arr = [];

    if (id === '' ||
        date === '') {

        errorDialog(1);
        return;
    }

    $(table_body).find('tr').map(function (obj, elem) {

        var prodCode = $(elem).find('td:eq(2) input').val();
        var prodName = $(elem).find('td:eq(3) input').val();
        var prodUnitValue = $(elem).find('td:eq(4) input').val();
        var prodAmount = $(elem).find('td:eq(5) input').val();
        var obj = {};

        if(prodCode === '' ||
           prodName === '' || 
           prodAmount === '' ) {
            errorDialog(1);
            checkNullValue = false;
        }

        obj = {
            "product_code": prodCode,
            "amount": prodUnitValue,
            "price": prodAmount,
            "discount": 0
        }

        arr.push(obj);
    });
    // Return if input value is null
    if (!checkNullValue) return;

    var json_data = {
        "detail": {
            "number": id,
            "customer_id": null,
            "ref_id": null,
            "source_wh_id": warehouse_id,
            "target_wh_id": null,
            "type": "po",
            "tax_type": "without_tax",
            "comment": "",
            "status": "create",
            "date": date
        },
        "lineitems": arr
    }
    console.log(json_data);
    $.ajax({
        type: 'POST',
        url: "api/document",
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        },
        data: json_data
    }).done(function(res) {
        console.log(res);
        if (res.created) {
            window.location = '/purchase_view';
        }else {
            errorDialog(2)
        }
    });
}

var get = {
    purchase_id: function () {
        return (
            $.ajax({
                type: 'GET',
                url: "api/document/service/gennumber/po",
                headers: {
                    "Accept": "application/json",
                    "Authorization": Authorization
                }
            })
        );
    }
}

function errorDialog( err ) {
 
    var text;

    switch ( err ) {
        // Case 1 กรอกข้อมูลไม่ครบ; 
        case 1 : 
            text = `กรุณากรอกข้อมูลให้ครบถ้วน`;
            break;
        // Case 2 ขายสินค้าจำนวนมากกว่าที่มีอยู่;
        case 2 :
            text = `ขายสินค้ามากกว่าจำนวนคงเหลือ`;
            break;
        default :
            text = `มีบางอย่างขัดข้องโปรดลงใหม่อีกครั้ง`;
            break;

    }
    $("#warning_text").html(text);
    $('#warning_modal').modal('show');
}



var inventory = {
    get: function () {
        return $.ajax({
            method: 'GET',
            url: "api/warehouse",
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            }
        })
    },
    set: function () {

        var warehouse  = $("#warehouse_select");
        var option = '';
        inventory.get().done(function (res) {
            
            for(var i in res) { 
                option += `
                    <option value="${res[i].warehouse_id}">${res[i].name} </option>
                `
            }
            $(warehouse).html(option);
        }); 
    }
}

inventory.set()