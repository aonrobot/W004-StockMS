var table = $('#prod_table').DataTable({
    "columnDefs": [{
        "orderable": false,
        "targets": 4
    }],
    "scrollX": true,
    columns: [{
            data: 'order',
            width: '10%'
        },
        {
            data: 'image',
            render: function (data, type, full, meta) {
                return '<img src="' + data + '" />';
            },
            className: "table-img",
        },
        {
            data: 'name'
        },
        {
            data: 'status',
            width: '15%',
            render: function (data, type, full, meta) {
                if (data) {
                    return `<span class="badge badge-publish">Public</span>`;
                } else {
                    return `<span class="badge badge-unpublish">Unpublish</span>`;
                }
            }
        },
        {
            data: 'btn',
            render: function (data, type, full, meta) {
                var rowIndex = data - 1;
                return `<div>   
                            <a href="#">
                                Edit
                            </a> |
                            <a href="#" onclick="deleteRow(${ rowIndex });" >
                                Delete
                            </a>
                        </div>`;
            },
            className: "table-btn",
        },
    ]
});


function getListSeries() {

    $.ajax({
        type: 'GET',
        url: "https://uinames.com/api/?ext&amount=25"

    }).done(function (response) {

        createListSerie(response);
    });
}

function createListSerie(lists) {
    var dom = $('#serie_lists');
    for (var i = 0; i < lists.length; i++) {

        dom.append($('<option>', {
            value: lists[i].name,
            text: lists[i].name,
            image: lists[i].photo,
        }));
    }
    $('#serie_lists').select2({
        templateResult: formatState,
        // templateSelection: formatState
    });
}

function formatState(state) {

    if (!state.id) {
        return state.text;
    }
    var $state = $(
        `<div style="min-height: 50px;">
            <img src="${ $(state.element).attr("image") }" style="max-width: 50px;"/>
            ${ state.text }
        </div>`
    );
    return $state;
};


function submit() {

    $.ajax({
        type: 'POST',
        contentType: "application/json",
        url: "api/product/detail/add",
        data: [{'label' : 'ทดสอบ', 'key' : 'ทดสอบคีย์', 'value' : 'ทดสอบค่า'}]

    }).done(function (response) {

        // createListSerie(response);
        console.log(response);
    });
    // var selected = $("#serie_lists").val();
    // var selectedImage = $('#serie_lists option:selected').attr('image');
    // var dateShow = $("#datetimepicker12").data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss");
    // var datePublic = $("#datetimepicker13").data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss"
    // var data = `{ "title": ${ selected }, "dateShow": ${ dateShow }, "datePublic": ${ datePublic }, "image": ${ selectedImage }}`;

    // localStorage.setItem('data', JSON.stringify(data));
    // add row
    // var rowIndex = table.row().data();

    // if (rowIndex) {
        
    //     table.row.add({
    //         "order": rowIndex.order += 1,
    //         "image": selectedImage,
    //         "name": selected,
    //         "status": Math.round(Math.random()),
    //         "btn": rowIndex.order
    //     }).draw();
    // } else {
    //     table.row.add({
    //         "order": 1,
    //         "image": selectedImage,
    //         "name": selected,
    //         "status": Math.round(Math.random()),
    //         "btn": 1
    //     }).draw();
    // }

    // $('#addSeries').modal('toggle');
}

function deleteRow(idx) {

    // re-order row number
    table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
        
        if (rowIdx > idx) {
            var data = this.data();
                data.order -= 1;
                data.btn -= 1; 
            // re-render row
            table.row(rowIdx).data(data).invalidate();
        }
    });
    
    table.row(idx).remove().draw( false );
}

getListSeries();

function addMoreDetail  () {


}
