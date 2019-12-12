$(document).ready(function (){

    $('#supportForm').on('submit', function (e) {

        e.preventDefault();
        $('#supportBlock').preloader();
        $('#supportForm :input').prop("disabled", true);

        let email = $('#email').val();
        let subject = $('#subject').val();
        let description = $('#description').val();

        let file = $('#file')[0].files[0];

        let fd = new FormData();
if (file !== undefined)
        fd.append('file', file);


        fd.append('email', email);
        fd.append('subject',subject);
        fd.append('description', description);

        $.ajax({
            url: baseUrl + 'user/support',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(data, textStatus, jqXHR){
                $('#supportBlock').preloader('remove');
                toastr.success("Support created successfully, you will be contacted within 12 hours");
                $('#supportForm :input').prop("disabled", false);
            },
            error: function(jqXHR, textStatus, errorThrown){
                toastr.error('Failed. try again');
                $('#supportForm :input').prop("disabled", false);
                $('#supportBlock').preloader('remove');
            }
        });
    });
});