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

    var DOC_TYPE = "inv"
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

    var table_body = ``;
    $.ajax({
        method: 'GET',
        url: "api/document?id=" + id,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (doc_list) {
        
        if ( Object.keys(doc_list).length ) {

            $("#doc_id").html(doc_list.number);
            $("#doc_date").html(doc_list.date);
            $("#doc_refer").html(doc_list.ref_id ? doc_list.ref_id : '-');
            $("#doc_print").html(
                `<a class="btn btn-info m-r-10" href="./print?did=${ id }">
                    Print    
                    <i class="fa fa-print" aria-hidden="true"></i>
                </a>`
            );
            
            var idx, 
                total = 0;
            
            for (var i = 0 ; i < doc_list.lineItems.length ; i++) {
                
                idx = i + 1;
                total =  total + Number(doc_list.lineItems[i].total);

                table_body += `
                    <tr> 
                        <td></td>
                        <td class="text-right"> ${ idx } </td>
                        <td> ${ doc_list.lineItems[i].product.code } </td>
                        <td> ${ doc_list.lineItems[i].product.name } </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].amount } </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].price } </td>
                        <td class="text-right">
                            <span class="badge badge-light"> 
                                ${ doc_list.lineItems[i].product.unitName } 
                            </span>
                        </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].total } </td>
                    </tr>
                `;
            }   

            $("#doc_detail").html(table_body);
            $("#doc_detail_count").html(idx);
            $("#doc_detail_total").html(total.toFixed(2));

            $("#detail_modal").modal('show');
        }
    });
}   