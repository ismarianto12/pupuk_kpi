<h4 class="card-title">{{ _('Tambah data master frekuensi') }}</h4>
<div class="ket"></div>

<form class="simpan form" novalidate="">
    <div class="card-body">

        <div class="form-group">
            <label>Kode Frekuensi</label>
            <div class="input-icon">
                <input type="text" name="kode" class="form-control" placeholder="Search..." required />
            </div>

        </div>
        <div class="form-group">
            <label>Nama Frekuensi</label>
            <div class="input-icon input-icon-right">
                <input type="text" name="nama_frekuensi" class="form-control" placeholder="Search..." required />
            </div>

        </div>
    </div>
    <div class="card-footer">
        <button type="sumbit" class="btn btn-primary mr-2">Simmpan</button>
        <button id="reset" type="reset" class="btn btn-secondary">Cancel</button>
    </div>
</form>
<div class="form-group">
    <div class="alert alert-custom alert-default" role="alert">
        <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
        <div class="alert-text">Use custom icon input component to place icon inside input control.</div>
    </div>
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
    $(function() {
        $('.simpan').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('master.frekuensi.store') }}",
                method: "POST",
                data: $(this).serialize(),
                chace: false,
                async: false,
                success: function(data) {
                    $('#datatable').DataTable().ajax.reload();
                    $('#formmodal').modal('hide');
                    $('#panel_tambah').removeClass('offcanvas-on');
                    $('#overlay').removeClass('offcanvas-overlay');
                    $('#formmodal').modal('hide');

                    toastr.success('data pegawai berhasil di simpan');


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
                    toastr.error('Silahkan cek inputan berikut :');

                    $('.ket').html(
                        "<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Perahtian donk!</strong> " +
                        respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");

                }
            })

        });
        $('#reset').on('click', function(e) {
            $('#panel_tambah').removeClass('offcanvas-on');
            $('#overlay').removeClass('offcanvas-overlay');
            $('#formmodal').modal('hide');

        });
    });
</script>
