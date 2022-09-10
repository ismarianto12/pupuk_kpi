<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ _('Edit Data User Data Hak Akses') }}</h4>
    </div>
    <div class="ket"></div>

    <form id="exampleValidation" method="POST" class="simpan" novalidate="">

        <div class="card-body">
            <div class="form-group row">
                <label for="name" class="col-md-2 text-left">Username<span class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="username" name="username"
                        value="{{ $username }}" required>
                </div>

                <label for="name" class="col-md-2 text-left">Unit / Bidang<span
                        class="required-label"></span></label>
                <div class="col-md-4">
                    <select name="tmunit_id" class="form-control">
                        @foreach ($unit as $units)
                            @php
                                $selected = $units->id == $tmunit_id ? 'selected' : '';
                            @endphp

                            <option value="{{ $units->id }}" {{ $selected }}>{{ $units->nama }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div class="form-group row">

                <label for="name" class="col-md-2 text-left">Password <span class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <label for="name" class="col-md-2 text-left">Email<span class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="email" class="form-control" id="email" name="email" value="{{ $email }}"
                        required>
                </div>

            </div>
            <div class="form-group row">
                <label for="name" class="col-md-2 text-left">Konfirmasi Password <span
                        class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="password" class="form-control" id="password_k" name="password_k" required>
                </div>
                <label for="name" class="col-md-2 text-left">Level Akses <span
                        class="required-label">*</span></label>
                <div class="col-md-4">

                    <select name="tmlevel_id" class="form-control">
                        @foreach ($level as $lev)
                            @php
                                $selected = $lev->id == $tmlevel_id ? 'selected' : '';
                            @endphp
                            <option value="{{ $lev->id }}" {{ $selected }}>{{ $lev->level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <div class="card-action">
            <div class="row">
                <div class="col-md-12">
                    <input class="btn btn-success" type="submit" value="Simpan">
                    <button class="btn btn-danger" type="reset">Batal</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    (function() {
        'use strict';
        var forms = document.getElementsByClassName('simpan');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

    })();
    // sellect 2
    $(document).ready(function() {
        $('.js-example-basic-single').select2({
            width: '100%'
        });
    });
    $(function() {


        $('.simpan').on('submit', function(e) {
            e.preventDefault();
            if ($('input[name="password_k"]').val() != $('input[name="password"]').val()) {
                toastr.error(
                    'Password tidak sama',
                );
            } else if ($('input[name="password_k"]').val() == '') {
                toastr.error(
                    'Password wajib di isi',
                );
            } else {
                $.ajax({
                    url: "{{ route('master.user.update', $id) }}",
                    method: "PUT",
                    data: $(this).serialize(),
                    cache: false,
                    asynch: false,
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(data) {
                        $('#datatable').DataTable().ajax.reload();
                        $('#formmodal').modal('hide');

                        swal.fire('info', 'Data berhasil di simpan', 'success');

                    },
                    error: function(data) {
                        var div = $('.container');
                        setInterval(function() {
                            var pos = div.scrollTop();
                            div.scrollTop(pos + 2);
                        }, 10)
                        err = '';
                        respon = data.responseJSON;
                        $.each(respon.errors, function(index, value) {
                            err += "<li>" + value + "</li>";
                        });
                        $('.ket').html(
                            "<div class='container'><div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong></strong> " +
                            respon.message + "<ol class='pl-3 m-0'>" + err +
                            "</ol></div></div>");

                        swal.fire('info', err, 'info');

                    }
                })
            }
        });
    });
</script>
