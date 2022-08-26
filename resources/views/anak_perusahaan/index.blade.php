@extends('layouts.template')
@section('title', 'Master data anak perusahaan')


@section('content')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-list"></i> Master data anak perusahaan',
        'label' => 'Kembali',
        'ajax_button' => '',
    ])
@endsection
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-right">
                <div class="button-group">
                    <h4>Read Only </h4>
                </div>
            </div>
        </div>
        <div class="card-body">

            <table id="datatable" class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nama cabang</th>
                        <th>Kode</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>

<div id="panel_tambah" class="offcanvas offcanvas-right pt-5 pb-10">

    <div class="offcanvas-content px-10">
        <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_panel_close">
            <i class="ki ki-close icon-xs text-muted"></i>
        </a>
        <div id="form_content"></div>
    </div>
</div>
<div id="overlay"></div>

<script>
    jQuery(document).ready(function() {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.cabang_perushaan') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    align: 'center',
                    className: 'text-center'
                }, {
                    data: 'nama_cabang',
                    name: 'nama_cabang'
                }, {
                    data: 'kode',
                    name: 'kode',
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    });

    @include('layouts.tablechecked');

    function del() {
        var c = new Array();
        $("input:checked").each(function() {
            c.push($(this).val());
        });
        if (c.length == 0) {
            $.alert("Silahkan memilih data yang akan dihapus.");
        } else {
            $.post("{{ route('master.anak_perusahaan.destroy', ':id') }}", {
                '_method': 'DELETE',
                'id': c
            }, function(data) {
                $('#datatable').DataTable().ajax.reload();
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Data berhasil di hapus',
                    showConfirmButton: false,
                    timer: 1500
                });
            }, "JSON").fail(function(data) {
                $('#datatable').DataTable().ajax.reload();

                err = '';
                respon = data.responseJSON;
                $.each(respon.errors, function(index, value) {
                    err += "<li>" + value + "</li>";
                });

                $.notify({
                    icon: 'flaticon-alarm-1',
                    title: 'Akses tidak bisa',
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
            });
        }
    }
    jQuery(document).ready(function() {
        $('#kt_quick_panel_close').on('click', function() {
            $('#panel_tambah').removeClass('offcanvas-on');
            $('#overlay').removeClass('offcanvas-overlay');
            $('#formmodal').modal('hide');

        });
        $(document).on("click", function(event) {
            $('#panel_tambah').addClass('offcanvas-off');
            $('#overlay').addClass('');
        })
        $('#tambah').on('click', function() {
            console.log('sdada');
            $('#panel_tambah').addClass('offcanvas-on');
            $('#overlay').addClass('offcanvas-overlay');

            addUrl = '{{ route('master.tahun.create') }}';
            $('#form_content').html(
                '<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>'
            ).load(addUrl);
        });

        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            console.log('sdada');
            $('#panel_tambah').addClass('offcanvas-on');
            $('#overlay').addClass('offcanvas-overlay');

            var id = $(this).data('id');
            addUrl =
                '{{ route('master.tahun.edit', ':id') }}'.replace(':id', id);
            $('#form_content').html(
                '<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>'
            ).load(addUrl);
        });

    });
</script>
@endsection
