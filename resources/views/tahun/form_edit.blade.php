<div class="card-title">{{ _('Edit data master tahun') }}</div>
<div class="ket"></div>
<form id="exampleValidation" method="POST" class="simpan">

    <label for="name">Kode<span class="required-label">*</span></label>
    <input type="text" class="form-control" id="kode" name="kode" value="{{ $kode }}">
    <label for="name">Tahun <span class="required-label">*</span></label>
    <input type="text" class="form-control" id="tahun" name="tahun" value="{{ $tahun }}">
    <label for="name">Status Active <span class="required-label">*</span></label>

    <select class="form-control" name="active">
        @foreach (['1' => 'Active', '2' => 'Non active'] as $act => $ac)
            @php
                $selected = $act == $active ? 'selected' : '';
            @endphp <option value="{{ $act }}" {{ $selected }}>{{ $ac }}</option>
        @endforeach
    </select>


    <br />
    <div class="row">
        <div class="col-md-12">
            <input class="btn btn-success" type="submit" value="Simpan">
            <button id="reset" class="btn btn-danger" type="reset">Batal</button>
        </div>
    </div>
</form>



<script type="text/javascript">
    $(function() {
        $('.simpan').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('master.tahun.update', $id) }}",
                method: "PUT",
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
