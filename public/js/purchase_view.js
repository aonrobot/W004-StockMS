var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

var invoice_view_table = $('#invoice_view_table').DataTable({
    "columnDefs": [{
        "orderable": false,
        "targets": 4
    }],
    columns: [
        {
            "render": function ( data, type, full, meta ) {
                return  meta.row + 1;
            },
            "width": "5%"
        },
        {
            "data": "invID",
            "render": function (data, type , full , meta) { 

                return `<a id ="${ full.id }" href="javascript:" onclick="viewDetail(${ full.id })"> ${data} </a>`;
            },
        },  
        {
            "data": "invDate",
            "width": "20%"
        },
        {
            "data": "prodSalePrice",
            "className": "text-right",
            "width": "15%"
        },
        {   
            "ordering": false,
            render: function (data, type, full, meta) {
                return `<div>   
                                <a href="#" onclick="editProd(${full.id})" class="edit-btn" >
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

    var DOC_TYPE = "po"
    $.ajax({
        type: 'GET',
        url: "api/document?type=" + DOC_TYPE,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response){

        var data = response;
        console.log(data);
        for (var i = 0; i < data.length; i++) {
            $('#invoice_view_table').DataTable().row.add({
                "invID": data[i].number,
                "invDate": data[i].date,
                "prodSalePrice": data[i].total,
                "id": data[i].id
                  
            }).draw();
        }
    })
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



function viewDetail(id) {

    $.ajax({
        method: 'GET',
        url: "api/document?id=" + id,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response) {
        console.log(response);
        $("#detail_modal").modal('show');
    });
}   