
var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');

var quatity_sum = $('#quantity_sum');
var total = $('#total');

$.ajax({
    method: 'GET',
    url: "api/inventory/quantity/sum",
    headers: {
        "Accept":"application/json",
        "Authorization":Authorization
    },
    success: function(data) {
        $(quantity_sum).html(data);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log(textStatus);
    }
});


$.ajax({
    method: 'GET',
    url: "api/inventory/totalprice",
    headers: {
        "Accept":"application/json",
        "Authorization":Authorization
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log(textStatus);
    }
}).done(function (data) {
    var quantity = Number($(quantity_sum).html());
    if ( quantity !== NaN) {
        $(total).html(quantity + data.total.cost);
    }else{ 
        $(total).html('n/a');
    }
});

$.ajax({
    method: 'GET',
    url: "api/report/all",
    headers: {
        "Accept":"application/json",
        "Authorization":Authorization
    },
    success: function(data) {
        console.log(data);
    }
});

