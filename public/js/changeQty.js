function ajaxChangeQty(url, id, amout){
    $.ajax({
        type: 'POST',
        url: "http://" + url,
        headers: {
            "Accept":"application/json",
            "Authorization":Authorization
        },
        data: {
            "product_id": id,
            "amount": amout
        }
    }).done(function(){
        $('body').busyLoad("hide", busyBoxOptions);
    });
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
            ajaxChangeQty('localhost/api/inventory/quantity/add', id, amount);
            var newData = parseInt(cell.data()) + amount;
            break;
        case 'decrease' :
        ajaxChangeQty('localhost/api/inventory/quantity/remove', id, amount);
            var newData = parseInt(cell.data()) - amount;
            break;
    }
    intQty.val('0');
    cell.data(newData).draw()
}

$(document).ready(function(){
    $('.qtyAmountInput').click(function(){
        $(this).select();
    })
    $('.qtyAmountInput').change(function(){
        var value = parseInt($(this).val());
        if(value < 0) $(this).val('0')
    })
    $('.increaseOneQtyBtn').click(function(){
        var id = $(this).data('id');
        changeQty(this, 'increase', id);
    })
    $('.decreaseOneQtyBtn').click(function(){
        var id = $(this).data('id');
        changeQty(this, 'decrease', id);
    })
})