@extends('layouts.template')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h3>{{ _('Ganti Password') }}</h3>
                </div>
            </div>
            <div class="ket"></div>

            <form id="exampleValidation" method="POST" class="simpan" enctype="multipart/form-data">

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-md-2 text-left">Username<span
                                class="required-label">*</span></label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $username }}" disabled>
                            <small>Username tidak bisa di ganti *</small>
                        </div>

                        <label for="name" class="col-md-2 text-left">Nama<span class="required-label">*</span></label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $name }}">
                        </div>
                    </div>

                    <div class="form-group row">

                        <label for="name" class="col-md-2 text-left">Password <span
                                class="required-label">*</span></label>
                        <div class="col-md-4">
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <label for="name" class="col-md-2 text-left">Email<span class="required-label">*</span></label>
                        <div class="col-md-4">
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $email }}">
                        </div>


                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-md-2 text-left">Konfirmasi <br /> Password <span
                                class="required-label">*</span></label>
                        <div class="col-md-4">
                            <input type="password" class="form-control" id="password" name="password_k">
                        </div>
                    </div>
                </div>
                <div class="card-action text-center">
                    <div class="row">
                        <div class="col-md-12">
                            <input class="btn btn-success" type="submit" value="Simpan">
                            <button class="btn btn-danger" type="reset">Batal</button>
                        </div>

                    </div>
                </div>
                <br />
                <br />
                <br />

            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#fc_upload_preview').attr('src', e.target.result);
                        $('#image_upload_preview').attr('src', e.target.result);

                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#foto").change(function() {
                var ext = $('#foto').val().split('.').pop().toLowerCase();
                //Allowed file types
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    swal('File Error', 'tidak bisa upload', 'warning');
                    $('#foto').val('');
                } else {
                    readURL(this);
                }
            });


            $('.simpan').on('submit', function(e) {
                e.preventDefault();
                if ($('input[name="password_k"]').val() != $('input[name="password"]').val()) {
                    $.notify({
                        icon: 'flaticon-alarm-1',
                        title: 'Kesalahan',
                        message: 'Password tidak sama',
                    }, {
                        type: 'secondary',
                        placement: {
                            from: "top",
                            align: "right"
                        },
                        time: 3000,
                        z_index: 2000
                    });
                } else if ($('input[name="password_k"]').val() == '') {
                    $.notify({
                        icon: 'flaticon-alarm-1',
                        title: 'Kesalahan',
                        message: 'Konfirmasi Passowrd Wajib di isi.',
                    }, {
                        type: 'secondary',
                        placement: {
                            from: "top",
                            align: "right"
                        },
                        time: 3000,
                        z_index: 2000
                    });
                } else {
                    var datastring = $(this).serialize();
                    $.ajax({
                        url: "{{ route('user.profilesave') }}",
                        method: "POST",
                        data: datastring,
                        cache: false,
                        asynch: false,
                        beforeSend: function() {
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire('info', 'Data berhasil di simpan', 'success');
                        },
                        error: function(data) {
                            var div = $('#container');
                            setInterval(function() {
                                var pos = div.scrollTop();
                                div.scrollTop(pos + 2);
                            }, 10)
                            err = '';
                            respon = data.responseJSON;
                            $.each(respon.errors, function(index, value) {
                                err += "<li>" + value + "</li>";
                            });
                            $.notify({
                                icon: 'flaticon-alarm-1',
                                title: 'Opp Seperti nya lupa inputan berikut :',
                                message: err,
                            }, {
                                type: 'secondary',
                                placement: {
                                    from: "top",
                                    align: "right"
                                },
                                time: 3000,
                                z_index: 2000
                            });

                        }
                    })
                }
            });
        });
    </script>
@endsection
