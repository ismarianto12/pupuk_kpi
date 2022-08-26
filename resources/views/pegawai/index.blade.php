@extends('layouts.template')
@section('title', 'Master Data Pegawai')
@section('content')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-users"></i> Master  Data Pegawai',
        'label' => 'Tambah Data',
        'ajax_button' => Url('master/pegawai/create'),
    ])
@endsection

<div class="col-md-12">
    <div class="card">
        <div class="card-header">


            <div class="form-group row">
                <label class="control-label col-md-3">
                    Bidang </label>
                <div class="col-md-4">

                    <select class="form-control" name="bidang" id="bidang">
                        <option value="">--Semua Bidang--</option>
                        @foreach ($bidang as $bidangs)
                            <option value="{{ $bidangs->id }}">
                                {{ $bidangs->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="control-label  col-md-3">
                    Jabatan</label>

                <div class="col-md-4">
                    <select class="form-control" name="jabatan" id="jabatan">
                        <option value="">--Semua Jabatan--</option>

                        @foreach ($jabatan as $jabatans)
                            <option value="{{ $jabatans->id }}">
                                {{ $jabatans->n_jabatan }}

                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="modal fade" id="formmodal" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document" style=" min-width: 65%;">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="title">
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="form_content">
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Jabatan</th>
                            <th>Bidang</th>
                            <th>Nip</th>
                            <th>Nama</th>
                            <th>Jk</th>
                            <th>Email</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // table data
    jQuery(document).ready(function() {

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.pegawai') }}",
                method: 'GET',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.bidang = $('#bidang').val();
                    data.jabatan = $('#jabatan').val();
                },
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
                    data: 'n_jabatan',
                    name: 'n_jabatan'
                }, {
                    data: 'nama_bidang',
                    name: 'nama_bidang',
                },
                {
                    data: 'nik',
                    name: 'nik',
                },

                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'jk',
                    name: 'jk',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });


        $('select').on('change', function() {
            $('#datatable').DataTable().ajax.reload();
        })

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
            $.post("{{ route('master.jabatan.destroy', ':id') }}", {
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

    // addd
    $(function() {
        $('#add_data').on('click', function() {
            $('#formmodal').modal('show');
            addUrl =
                '{{ route('master.jabatan.create') }}';
            $('#form_content').html('<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>').load(addUrl);
        });

        // edit
        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ route('master.jabatan.edit', ': id ') }}'
                .replace(':id', id);
            $('#form_content').html('<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>').load(addUrl);

        })
    });
</script>
@endsection
