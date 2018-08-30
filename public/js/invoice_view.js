var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

var invoice_view_table = $('#invoice_view_table').DataTable({
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

function initialDataTable() {

    invoice_view_table
        .clear()

    // $('body').busyLoad("show", busyBoxOptions);
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
            $('#invoice_view_table').DataTable().row.add({
                "prodID": data[i].code,
                "prodName": data[i].name,
                "prodSalePrice": data[i].inventory.salePrice,
                "prodAmount": data[i].inventory.quantity,
                "prodUnit": data[i].unitName,
                "btn": data[i].product_id
                  
            }).draw();
        }
        // $('body').busyLoad("hide", busyBoxOptions);
        // createChangeQty_event();
    });
}



$('#invoice_view_table tbody').on('click', '.delete-btn', function (mm) {

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

var DOC_TYPE = "inv"
$.ajax({
    type: 'GET',
    url: "api/document?type=" + DOC_TYPE,
    headers: {
        "Accept": "application/json",
        "Authorization": Authorization
    }
}).done(function (res){
    console.log(res);
})