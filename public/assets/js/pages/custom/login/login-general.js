


toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

jQuery('#loginacti').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        method: 'post',
        data: $(this).serialize(),
        chace: false,
        asynch: false,
        beforeSend: function () {
            Swal.showLoading();
        },
        success: function (data, JqXHR) {
            toastr.success('login success sedang mengalihkan ...');
            window.location.href = HOST_URL;
            console.log(JqXHR);
        },
        error: function (data, JqXHR, err) {
            err = '';
            respon = data.responseJSON;
            toastr.error('login gagal username dan passwor salah');
            $.each(respon.errors, function (index, value) {
                err += "<li>" + value + "</li>";
            });
            Swal.fire({
                title: 'Opp ada kesalahan',
                html: err,
                icon: 'error',
                confirmButtonText: 'Cool'
            })
        }
    });
});