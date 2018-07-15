var table = $('#prod_table').DataTable({
    "columnDefs": [{
        "orderable": false,
        "targets": 6
    }],
    columns: [{
            "data": "prodID",
            "width": "10%"
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
                            <a href="#">
                                Edit
                            </a> |
                            <a href="#" onclick="deleteRow(${ meta.row });" >
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

$("#form_prod").submit(function (e) {

    event.preventDefault();
    var data = $('#form_prod').serializeArray();
    console.log(data);
    data = {
        prodID: data[0].value,
        prodName: data[1].value,
        prodCat: data[2].value,
        prodBuyPrice: data[3].value,
        prodSalePrice: data[4].value,
        prodUnit: data[5].value,
        prodAmount: data[6].value,
        branch: data[7].value,
        prodDetail: data[8].value
    }

    table.row.add({
        "prodID": data.prodID,
        "prodName": data.prodName,
        "prodBuyPrice": data.prodBuyPrice,
        "prodSalePrice": data.prodSalePrice,
        "prodAmount": data.prodAmount,
        "prodUnit": data.prodUnit,
        "btn": ""
    }).draw();

    $('#addProd').modal('hide');

});

function deleteRow(idx) {
    table.row(idx).remove().draw(false);
}

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


$('#addProdCat').click(function(e){ 
    e.preventDefault();


    var text_warning = $('#text_warning');
    var catagory_name = $('#cat_code');
    var catagory_list = $('#prod_cat');
    
    var catagory_name_value = catagory_name.val();
    // clear
    text_warning.empty();

    // if user not type any data
    if ( catagory_name.val().length === 0 ){

        catagory_name.addClass('is-invalid');
        text_warning.append('กรุณาใส่หมวดหมู่');

    } else {
        
        catagory_list.append(`<option value="${ catagory_name_value }" selected="true"> ${ catagory_name_value } </option>`);

        // Close Modal
        $('#prod_cat_modal').modal('hide')

    }       
});

$('#addBranch').click(function(e){ 
    e.preventDefault();

    var warning = $('#text_warning_branch');
    var branch_name = $('#branch_code');
    var branch_list = $('#prod_branch');
    
    var branch_name_value = branch_name.val();
    // clear
    warning.empty();

    // if user not type any data
    if ( branch_name.val().length === 0 ){

        branch_name.addClass('is-invalid');
        warning.append('กรุณาใส่คลังสินค้า');

    } else {
        
        branch_list.append(`<option value="${ branch_name_value }" selected="true"> ${ branch_name_value } </option>`);

        // Close Modal
        $('#prod_branch_modal').modal('hide')

    }       
});

$('#addProd').on('show.bs.modal', function (event) {
    
    $('#addProd').find('form')[0].reset();
})
