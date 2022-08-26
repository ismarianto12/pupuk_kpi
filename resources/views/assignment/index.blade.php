@extends('layouts.template')
@section('title', 'Assigment data kamus kpi per unit kerja')
@section('content')

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
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => '#',
        'par' => '<i class="fa fa-list"></i>Assigment kamus KPI',
        'label' => '',
        'ajax_button' => null,
    ])
@endsection

<div class="card card-custom gutter-b">
    <div class="card-body">
        <div class="card-header">
            <div class="form-group row">
                <label class="control-label col-md-3">
                    Unit Kerja </label>
                <br />
                <div class="col-md-4">
                    <select class="form-control" name="unit_id" id="unit_id">
                        <option value="">--Semua Bidang--</option>
                        @foreach ($unit as $units)
                            <option value="{{ $units->id }}">
                                {{ $units->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-3">
                    Tahun </label>
                <br />
                <div class="col-md-4">
                    <select class="form-control" name="tahun_id" id="tahun_id">
                        <option value="">--Semua Data--</option>
                        @foreach ($tahun as $tahuns)
                            <option value="{{ $tahuns->id }}">
                                {{ $tahuns->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="overflow:auto">
            <table id="datatable" class="display table-hover" style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th><strong>No</strong></th>
                        <th><strong>KPI</strong></th>
                        <th><strong>Satuan</strong></th>
                        <th><strong>Target</strong></th>
                        <th><strong>Polaritas</strong></th>
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
        $('#unit_id').select2({
            placeholder: "Pilih data unit kerja",
            initSelection: function(element, callback) {
                console.log(callback);

            }
        });

        $('#tahun_id').select2({
            placeholder: "Pilih data tahun",
            initSelection: function(element, callback) {

                console.log(callback);
            }

        });


        function prospektif_parent() {
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('assignmen.prospektif') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tahun_id = $('#tahun_id').val();
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    align: 'center',
                }, {
                    data: 'nama_kpi',
                    name: 'nama_kpi',
                    width: '500px'
                }, {
                    data: 'nama_satuan',
                    name: 'nama_satuan',
                },
                {
                    data: 'target',
                    name: 'target',
                    width: '150px'
                },
                {
                    data: 'nama_polaritas',
                    name: 'nama_polaritas',
                    width: '450px'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],

        }

        function prospektif_child() {

            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('assignmen.subprospektif') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tahun_id = $('#tahun_id').val();
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    align: 'center',
                }, {
                    data: 'nama_kpi',
                    name: 'nama_kpi',
                    width: '500px'
                }, {
                    data: 'nama_satuan',
                    name: 'nama_satuan',
                },
                {
                    data: 'target',
                    name: 'target',
                    width: '150px'
                },
                {
                    data: 'nama_polaritas',
                    name: 'nama_polaritas',
                    width: '450px'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],


        }


        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.assingment') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tahun_id = $('#tahun_id').val();
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    align: 'center',
                }, {
                    data: 'nama_kpi',
                    name: 'nama_kpi',
                    width: '500px'
                }, {
                    data: 'nama_satuan',
                    name: 'nama_satuan',
                },
                {
                    data: 'target',
                    name: 'target',
                    width: '150px'
                },
                {
                    data: 'nama_polaritas',
                    name: 'nama_polaritas',
                    width: '450px'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
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
            $.post("{{ route('master.kamus.destroy', ':id') }}", {
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
            $('.modal-dialog').css({
                'min-width': '90%'
            });
            addUrl =
                '{{ route('master.kamus.create') }}';
            $('#form_content').load(addUrl);
        });

        // edit
        $('#datatable').on('click', '#view_data', function(e) {
            e.preventDefault();
            $('.modal-dialog').css({
                'min-width': '65%'
            });
            $('#formmodal').modal('show');

            id = $(this).data('id');
            addUrl =
                '{{ route('kamus.load_unit', ':id') }}'.replace(':id', id);
            $('#form_content').load(addUrl);
        })
        $('#datatable').on('click', '#view', function(e) {
            e.preventDefault();
            $('.modal-dialog').css({
                'min-width': '60%'
            });
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ route('master.kamus.show', ':id') }}'.replace(':id', id);
            $('#form_content').load(
                addUrl);

        })
    });
</script>
@endsection
