<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ _('Tambah Data User Data Hak Akses') }}</h4>
    </div>
    <div class="ket"></div>

    <form id="exampleValidation" method="POST" class="simpan">

        <div class="card-body">

            <div class="form-group row">
                <label for="name" class="col-md-2 text-left">Username<span class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <label for="name" class="col-md-2 text-left">Unit <span class="required-label">*</span></label>
                <div class="col-md-4">
                    <select name="tmunit_id" class="form-control">
                        @foreach ($unit as $units)
                            <option value="{{ $units->id }}">[{{ $units->unit_kode }}] - {{ $units->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">

                <label for="name" class="col-md-2 text-left">Password <span class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="password" class="form-control" id="password" name="password">
                </div>



                <label for="name" class="col-md-2 text-left">Email Unit / Bidang<span
                        class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="email" class="form-control" id="email" name="email">
                </div>

            </div>
            <div class="form-group row">
                <label for="name" class="col-md-2 text-left">Konfirmasi Password <span
                        class="required-label">*</span></label>
                <div class="col-md-4">
                    <input type="password" class="form-control" id="password_k" name="password_k">
                </div>
                <label for="name" class="col-md-2 text-left">Level Akses <span
                        class="required-label">*</span></label>
                <div class="col-md-4">
                    {{-- select level was here --}}
                    <select name="tmlevel_id" class="form-control">
                        @foreach ($level as $lev)
                            <option value="{{ $lev->id }}">{{ $lev->level }}</option>
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
                toastr.error('kesalaha password tidak sama');
            } else if ($('input[name="password_k"]').val() == '') {
                toastr.error('password konfirmasi wajib di isi');
            } else {
                var datastring = $(this).serialize();
                $.ajax({
                    url: "{{ route('master.user.store') }}",
                    method: "POST",
                    data: datastring,
                    cache: false,
                    beforeSend: function() {
                        toastr.info('Sedang proses penyimpanan data ...');

                    },
                    success: function(data) {
                        $('#datatable').DataTable().ajax.reload();
                        $('#formmodal').modal('hide');

                        toastr.success('data berhasil di simpan');

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
                        $('.ket').html(
                            "<div class='container'><div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong></strong> " +
                            respon.message + "<ol class='pl-3 m-0'>" + err +
                            "</ol></div></div>");

                        toastr.error('validasi form tidak cocok');

                    }
                })
            }
        });
    });
</script>
