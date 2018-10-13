var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

var purchase_view_table = $('#purchase_view_table').DataTable({
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
            "data": "poID",
            "render": function (data, type , full , meta) { 

                return `<a id ="${ full.id }" href="javascript:" onclick="viewDetail(${ full.id })"> ${data} </a>`;
            },
        },  
        {
            "data": "poDate",
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
                            <a href="#" class="edit-btn" >
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
    // This for re render index number when delete row
    drawCallback: function(settings){
        var api = new $.fn.dataTable.Api( settings );
        api.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1;
            api.cell(cell).invalidate('dom');             
        } );            
    },
    "scrollX": true
});

initialDataTable();

function initialDataTable() {

    purchase_view_table
        .clear()

    $('body').busyLoad("show", busyBoxOptions);

    var DOC_TYPE = "po"
    $.ajax({
        type: 'GET',
        url: "api/document?type=" + DOC_TYPE,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (response){
        console.log(response);
        var data = response;

        for (var i = 0; i < data.length; i++) {
            $('#purchase_view_table').DataTable().row.add({
                "poID": data[i].number,
                "poDate": data[i].date,
                "prodSalePrice": data[i].total,
                "id": data[i].id
                  
            }).draw();
        }

        $('body').busyLoad("hide", busyBoxOptions);
    })
}



$('#purchase_view_table tbody').on('click', '.delete-btn', function (mm) {

    if (confirm("คุณยืนยันที่จะลบข้อมูล?")) {
        
        var id = purchase_view_table.row($(this).parents('tr')).data()
        $.ajax({
            method: 'POST',
            url: "api/DEL/document/" + id.poID,
            headers: {
                "Accept": "application/json",
                "Authorization": Authorization
            }
        }).done(function (response) {
            if(!response.updated) {
                console.log('Error');
                return false;
            }
        });
        
        purchase_view_table
            .row($(this).parents('tr'))
            .remove()
            .draw(true);

            
    } else {
        return;
    }
});

$('#purchase_view_table tbody').on('click', '.edit-btn', function (mm) {
    
    var id = purchase_view_table.row($(this).parents('tr')).data()
    window.location = "/purchase_edit/" + id.poID;
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
        console.log(doc_list);
        if ( Object.keys(doc_list).length ) {

            $("#doc_id").html(doc_list.number);
            $("#doc_date").html(doc_list.date);
            // $("#doc_refer").html(doc_list.ref_id ? doc_list.ref_id : '-');
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