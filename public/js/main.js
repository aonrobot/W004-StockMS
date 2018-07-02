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