@extends('layouts.template')
@section('title', 'Pengajuan Data Kamus')
@section('content')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => '#',
        'par' => '<i class="fa fa-users"></i> Pengajuan  Data Kamus',
        'label' => '',
        'ajax_button' => null,
    ])
@endsection 

<div class="modal fade" id="formmodal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

<div class="card card-custom gutter-b">

    <div class="card-body">
        <div class="card-header">
            <div class="d-flex align-items-right">
                <div class="button-group">
                    <button class="btn btn-primary btn-round ml-auto btn-sm" id="add_data">
                        <i class="fa fa-plus"></i>
                        Add Row
                    </button>
                    <button class="btn btn-danger btn-round btn-sm" id="add_data" onclick="javascript:confirm_del()">
                        <i class="fa fa-minus"></i>
                        Delete selected
                    </button>
                </div>
            </div>
            <br /><br /><br />
            <div class="form-group row">
                <label class="control-label col-md-3">
                    Tahun </label>
                <div class="col-md-4">

                    <select class="form-control" name="tahun_id" id="tahun_id">
                        <option value="">--Semua Tahun--</option>
                        @foreach (Properti_app::getTahunActive() as $tahuns)
                            <option value="{{ $tahuns->id }}">
                                {{ $tahuns->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nama KPI</th>
                        <th>Target</th>
                        <th>Unit Pemilik</th>
                        <th>Unit Pengelola</th>
                        <th>Status Assign</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function() {
        $('#tahun_id').select2({
            placeholder: 'pilih tahun '
        });

    
        var table = $('#datatable').DataTable({
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: {
                leftColumns: 2
            },
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.pengajuan_kamus_kpi') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tahun_id = $('#tahun_id').val();

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
                    data: 'nama_kpi',
                    name: 'nama_kpi'
                }, {
                    data: 'target',
                    name: 'target',
                },
                {
                    data: 'unit_pemilik_kpi',
                    name: 'unit_pemilik_kpi',
                },

                {
                    data: 'nama_unit',
                    name: 'nama_unit'
                },
                {
                    data: 'assign_status',
                    name: 'assign_status'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('DD/MM/YYYY HH:mm:ss');
                    }
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
            $.post("{{ route('korporat.pengajuan_kamus_kpi.destroy', ':id') }}", {
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
            event.preventDefault();
            $('.modal-dialog').css({
                'min-width': '95%'
            });

            $('#formmodal').modal('show');
            addUrl =
                '{{ route('korporat.pengajuan_kamus_kpi.create') }}';
            $('#form_content').load(addUrl);
        });

        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            $('.modal-dialog').css({
                'min-width': '80%'
            });

            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ route('korporat.pengajuan_kamus_kpi.edit', ':id') }}'.replace(':id', id);
            $('#form_content').load(addUrl);

        })

        $('#datatable').on('click', '#assign_to', function(e) {
            e.preventDefault();
            console.log('adsad' + e);
            $('.modal-dialog').css({
                'min-width': '50%'
            });

            $('#formmodal').modal('show');

            id = $(this).data('id');
            addUrl =
                '{{ route('korporat.assignto', ':id') }}'.replace(':id', id);
            $('.modal-body').load(addUrl);

        })


        $('#datatable').on('click', '#view', function(e) {
            e.preventDefault();
            $('#formmodal').modal('show');
            $('.modal-dialog').css({
                'min-width': '70%'
            });

            id = $(this).data('id');
            addUrl =
                '{{ route('korporat.pengajuan_kamus_kpi.show', ':id') }}'.replace(':id', id);
            $('#form_content').html(
                '<center><h3><i class="fa fa-refresh fa-10x"></i></h3></center>').load(
                addUrl);

        })
    });
</script>
@endsection
