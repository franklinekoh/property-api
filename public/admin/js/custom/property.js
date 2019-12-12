var baseUrl = window.location.origin+'/api/';
$(document).ready(function (){

});


var deleteProperty = function (ele) {
    var properyId = $(ele).attr('data-id');

    var body = {
        'uuid': properyId
    };


    $.ajax({
        url: baseUrl + 'administrator/properties',
        headers: {
            'Accept': 'application/json'
        },
        type: 'delete',
        data: body,
        success: function(data, textStatus, jqXHR){

            if (data.status){
                toastr.success(data.message)
            }

            if (!data.status) {
                toastr.error(data.message);
            }
            setTimeout(function () {
                location.reload();
            }, 3000);
        },
        error: function(jqXHR, textStatus, errorThrown){

            toastr.error(jqXHR.responseJSON.errors);


        }
    });
};

var viewImage = function (ele) {
    
    var url = $(ele).attr('data-id');

    var modalImageHtml = '<img src="'+url+'" alt="Modal Image">';

    $('#modalImage').html(modalImageHtml);

} ;