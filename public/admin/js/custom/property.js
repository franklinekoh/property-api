var baseUrl = window.location.origin+'/api/';
var windowsUrl = window.location.origin;

$(document).ready(function (){

    // Parsley for form validation
    $('#createPropertyForm').parsley();
    $('#editPropertyForm').parsley();

    Dropzone.options.createDropzone = {
        url: `${baseUrl}administrator/properties`,
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 1,
        maxFileSize:2048,
        uploadMultiple: false,
        acceptedFiles: "image/*",

        init: function () {

            var submitButton = document.querySelector("#submitCreateForm");
            var wrapperThis = this;

            submitButton.addEventListener("click", function (e) {
                e.preventDefault();

                if (wrapperThis.files.length) {
                    wrapperThis.processQueue();
                } else {
                    wrapperThis.submit();
                }

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


    Dropzone.options.editDropzone = {
        url: `${baseUrl}administrator/properties/update`,
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 1,
        maxFileSize:2048,
        uploadMultiple: false,
        acceptedFiles: "image/*",

        init: function () {

            var submitButton = document.querySelector("#submitEditForm");
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
                formData.append("country", $("#countryEdit").val());
                formData.append("county", $("#countyEdit").val());
                formData.append("town", $("#townEdit").val());
                formData.append("postcode", $("#postcodeEdit").val());
                formData.append("description", $("#descriptionEdit").val());
                formData.append("address", $("#addressEdit").val());
                formData.append("bathrooms", $("#bathroomsEdit").val());
                formData.append("price", $("#priceEdit").val());
                formData.append("bedrooms", $("#bedroomsEdit").val());
                formData.append("propertyTypeId", $("#propertyTypeEdit").val());
                formData.append("type", $(".typeEdit").val());
                formData.append("uuid", $("#uuid").val());
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

    var modalImageHtml = '<img src="'+url+'" alt="Property Image">';

    $('#modalImage').html(modalImageHtml);

} ;

var getPropertyById = function (ele) {

    var uuid = $(ele).attr('data-id');

    $.ajax({
        url: baseUrl + `administrator/properties/${uuid}`,
        headers: {
            'Accept': 'application/json'
        },
        type: 'get',
        success: function(data, textStatus, jqXHR){
            var property = data.data.property;
            var propertyTypes = data.data.propertyTypes;

            $('#countryEdit').val(property.country)
            $('#countyEdit').val(property.county);
            $('#townEdit').val(property.town);
            $('#postcode').val(property.postcode);
            $('#descriptionEdit').val(property.description);
            $('#addressEdit').val(property.address);
            $('#priceEdit').val(property.price);
            $('#uuid').val(property.uuid);


            var bedNoRoomHtml = '<option value="">choose no.</option>';
            var selectedBedRoom = '';

            for (i = 1; i < 1001; i++){
                selectedBedRoom = (property.num_bedrooms ===  i)? 'selected': '';
                bedNoRoomHtml += '<option value="'+i+'" '+selectedBedRoom+'>'+i+'</option>';
            }


            var bathNoRoomHtml = '<option value="">choose no.</option>';
            var selectedBathroom = '';

            for (i = 1; i < 1001; i++){
                selectedBathroom = (property.num_bathrooms ===  i)? 'selected': '';
                bathNoRoomHtml += '<option value="'+i+'" '+selectedBathroom+'>'+i+'</option>';
            }

            var propertyTypeHtml = '<option value="">Choose type</option>';
            var selectedProperty = '';

            $.each(propertyTypes, function (index, value) {
                selectedProperty = (property.property_type_id === value.id)? 'selected': '';
                propertyTypeHtml += '<option value="'+value.id+'" '+selectedProperty+'>'+value.title+'</option>';
            });


            $('#bathroomsEdit').html(bathNoRoomHtml);
            $('#bedroomsEdit').html(bedNoRoomHtml);
            $('#propertyTypeEdit').html(propertyTypeHtml);
            
            if (property.type === 'rent'){
                $('#rentEdit').attr('checked', 'checked');
            }

            if (property.type === 'sale'){
                $('#saleEdit').attr('checked', 'checked');
            }

            console.log(windowsUrl);
            var currentImageHtml = '';
            var localImageUrl = windowsUrl+'/'+property.img_url;
            
            if (property.latitude === null && property.longitude === null){
                currentImageHtml = '<img src="'+localImageUrl+'" alt="Current property Image">';
            } else{
                currentImageHtml = '<img src="'+property.img_url+'" alt="Current property Image">';
            }

            $('#currentImageBlock').html(currentImageHtml);
                

        },
        error: function(jqXHR, textStatus, errorThrown){

            toastr.error(jqXHR.responseJSON.errors);

        }
    });
};

