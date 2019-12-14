
var baseUrl = window.location.origin+'/api/';
$('#createPropertyForm').parsley();
$(document).ready(function (){

    Dropzone.options.myDropzone = {
        url: `${baseUrl}administrator/properties`,
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 1,
        maxFileSize:2048,
        uploadMultiple: false,
        acceptedFiles: "image/*",

        init: function () {

            var submitButton = document.querySelector("#submit-all");
            var wrapperThis = this;

            submitButton.addEventListener("click", function (e) {
                e.preventDefault();
                wrapperThis.processQueue();
            });

            this.on("addedfile", function (file) {

                var removeButton = Dropzone.createElement("<button class='btn btn-block btn-danger'><i class='fa-times fa'></button>");

                removeButton.addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    wrapperThis.removeFile(file);
                });

                file.previewElement.appendChild(removeButton);
            });

            this.on('sending', function (data, xhr, formData) {
                formData.append("country", $("#country").val());
                formData.append("county", $("#county").val());
                formData.append("town", $("#town").val());
                formData.append("postcode", $("#postcode").val());
                formData.append("description", $("#description").val());
                formData.append("address", $("#address").val());
                formData.append("bathrooms", $("#bathrooms").val());
                formData.append("price", $("#price").val());
                formData.append("bedrooms", $("#bedrooms").val());
                formData.append("propertyTypeId", $("#propertyType").val());
                formData.append("type", $(".type").val());
            });

            this.on('success', function (files, response) {

                    toastr.success(response.message);

                setTimeout(function () {
                    location.reload();
                }, 1000);

            });

            this.on('error', function (file, error, xhr) {

                file.status = 'queued';
                if (xhr.status === 422){
                    toastr.error('An error occurred, please confirm that you have filled all inputs and try again');
                }else{
                    toastr.error('An error occurred');
                }

            });

            this.on("maxfilesexceeded", function(file) {
                wrapperThis.removeFile(file);
            });

        }
    };


    var roomsHtml = '<option value="">choose no.</option>';
    for (i = 1; i < 1001; i++){
        roomsHtml += '<option value="'+i+'">'+i+'</option>';
    }

    $('#bedrooms').html(roomsHtml);
    $('#bathrooms').html(roomsHtml);
});


var deleteProperty = function (ele) {

    swal({
        title: "Delete property?",
        buttons:["cancel", "proceed"]
    }).then(function (proceed) {
            if (proceed){

                $('#propertyBlock').preloader();

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
                        }, 1000);
                    },
                    error: function(jqXHR, textStatus, errorThrown){

                        toastr.error(jqXHR.responseJSON.errors);

                    }
                });

                $('#propertyBlock').preloader('remove');
            }
    });


};

var viewImage = function (ele) {

    $('#modalImage').preloader();
    var url = $(ele).attr('data-id');

    var modalImageHtml = '<img src="'+url+'" alt="Modal Image">';

    $('#modalImage').html(modalImageHtml);

} ;

