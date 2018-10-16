$( document ).ready(function() {
    var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');
    var DOC_ID;
    var table_body = ``;

    // GET DOC_ID via URL
    if((window.location.href).indexOf('?') != -1) {
        var queryString = (window.location.href).substr((window.location.href).indexOf('?') + 1); 
        
        DOC_ID = (queryString.split('='))[1];
        DOC_ID = decodeURIComponent(DOC_ID);
    }

    $.ajax({
        method: 'GET',
        url: "api/document?id=" + DOC_ID,
        headers: {
            "Accept": "application/json",
            "Authorization": Authorization
        }
    }).done(function (doc_list) {

        if ( Object.keys(doc_list).length ) {
            
            $("#print_date").html(
                `<strong>วันที่ทำรายการ :</strong> ${ doc_list.date } `
            );
            $("#print_id").html(
                `<strong>เอกสารเลขที่ :</strong> ${ doc_list.number } `
            );

            var idx, 
                total = 0;
                costTotal = 0;
            
            for (var i = 0 ; i < doc_list.lineItems.length ; i++) {
                
                idx = i + 1;
                total =  total + Number(doc_list.lineItems[i].total);
                costLine = doc_list.lineItems[i].product.inventory.costPrice * doc_list.lineItems[i].amount.toFixed(2)
                costTotal += costLine

                table_body += `
                    <tr> 
                        <td class="text-right"> ${ idx } </td>
                        <td> ${ doc_list.lineItems[i].product.code } </td>
                        <td> ${ doc_list.lineItems[i].product.name } </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].amount }</td>
                        <td class="text-right"> ${ doc_list.lineItems[i].product.inventory.costPrice } </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].price } </td>
                        <td class="text-right"> ${ costLine } </td>
                        <td class="text-right"> ${ doc_list.lineItems[i].product.inventory.salePrice * doc_list.lineItems[i].amount.toFixed(2) } </td>
                    </tr>
                `;
            }   

            $("#print_detail").html(table_body);
            $("#print_detail_total").html(total.toFixed(2));
            $("#print_cost_total").html(costTotal.toFixed(2));
        }
    });
});
