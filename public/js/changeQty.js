function ajaxChangeQty(url, amout, type){
    var result;
    $.ajax({
        type: 'PUT',
        url: url,
        async:false,
        headers: {
            "Accept":"application/json",
            "Authorization": Authorization
        },
        data: {
            "amount": amout,
            "type": type
        }
    }).done(function(data){
        $('body').busyLoad("hide", busyBoxOptions);
        result = data
        !(data.updated) ? swal(data.message, 'error', 'error') : ''
    });

    return result

}

function changeQty(that, action, id){
    $('body').busyLoad("show", busyBoxOptions);
    var intQty = $(`.qtyAmountInput[data-id=${id}]`);
    var r = $(that).data('row');
    var c = $(that).data('col');
    var cell = table.cell(r, c);
    var amount = parseInt(intQty.val());
    switch(action){
        case 'increase' :
            var result = ajaxChangeQty('api/inventory/quantity/' + id, amount, 'increase');
            var newData =  result.total
            break;
        case 'decrease' :
            var result = ajaxChangeQty('api/inventory/quantity/' + id, amount, 'decrease');
            var newData =  result.total
            break;
    }
    intQty.val('0');
    if(newData != undefined) cell.data(newData).draw()
}

function createQtyEvent(){
    $('.qtyAmountInput').unbind( "click" ).click(function(){
        $(this).select();
    })
    $('.qtyAmountInput').unbind( "change" ).change(function(){
        var value = parseInt($(this).val());
        if(value < 0) $(this).val('0')
    })
    $('.increaseOneQtyBtn').unbind( "click" ).click(function(){
        var id = $(this).data('id');
        changeQty(this, 'increase', id);
    })
    $('.decreaseOneQtyBtn').unbind( "click" ).click(function(){
        var id = $(this).data('id');
        changeQty(this, 'decrease', id);
    })
}
function createChangeQty_event(){
    createQtyEvent()
    table.on( 'draw', function () {
        createQtyEvent()
    } );
}