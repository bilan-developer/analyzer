$( document ).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function sendForm() {
    $.ajax({
        type: "POST",
        url: '/analysis',
        data: $("#form").serialize(),
        success: function (response) {
            $("#report").empty().append(response);
        },
        error: function(xhr) {
            var response = $.parseJSON(xhr.responseText);
            $.each(response.errors, function (key, val) {
                $("#" + key + "_error").text(val[0]);
                $("#" + key).addClass('is-invalid');
            });
        }
    })
}
function clearReport() {
    $("#report").empty();
}