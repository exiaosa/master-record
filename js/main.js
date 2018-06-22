$(document).ready(function () {
    $(".delete-action").click(function (e) {
        e.preventDefault();

        $email = $(".record-container").attr('data-email');


        $.ajax({
            type: "POST",
            url: window.location.pathname+'/access/deleteRecord',
            data: {
                Email: $email
            },
            success: function (data) {
                if (data.Status == 1) {
                    $(".record-container").html("<p class='success-message'>"+data.Message+"</p>");
                }
            }
        });

    });


    $(".file-action").click(function (e) {
        //e.preventDefault();

        $email = $(".record-container").attr('data-email');

        $(this).removeClass('file-action').html('It is processing...').addClass('download');

        $.ajax({
            type: "POST",
            url: window.location.pathname+'/access/fileRecord',
            data: {
                Email: $email
            },
            success: function (data) {
                if (data.Status == 1) {
                    $('.download').html('<a target="_blank" href="assets/UserData/'+data.FilePath+'">DOWNLOAD</a>');
                }
            }
        });

    });

    $(".download").click(function (e) {
        //e.preventDefault();

        $email = $(".record-container").attr('data-email');

        //$(this).removeClass('file-action').html('It is processing...').addClass('download');

        $.ajax({
            type: "POST",
            url: window.location.pathname+'/access/fileClean',
            data: {
                Email: $email
            },
            success: function (data) {
                if (data.Status == 1) {
                    $('.download').html('Thanks for downloading!');
                }
            }
        });

    });


});