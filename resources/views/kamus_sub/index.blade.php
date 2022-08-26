@extends('layouts.template')
@section('title', 'Master Data Kamus')
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
        'url' => Url('kamus/create'),
        'par' => '<i class="fa fa-users"></i> Master  Data Kamus',
        'label' => '',
        'ajax_button' => null,
    ])
@endsection



<div class="card card-custom gutter-b">

    <div class="card-body">
        <div class="d-flex align-items-right">
            <div class="button-group">
                <button class="btn btn-primary btn-round ml-auto btn-sm" id="add_data">
                    Add Row
                </button>
                <button class="btn btn-danger btn-round btn-sm" id="add_data" onclick="javascript:confirm_del()">
                    <i class="fa fa-minus"></i>
                    Delete selected
                </button>
            </div>
        </div>
        <div class="card-header">
            <div class="form-group row">
                <label class="control-label col-md-3">
                    Bidang Unit Akses </label>
                <br />
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
        </div>

        <div class="table-responsive" style="overflow:auto">
            <table id="datatable" class="display table table-striped table-hover" style="width: 150%; font-size: 12px;">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sub Kamus KPI</th>
                        <th>Target</th>
                        <th>Unit Pemilik</th>
                        <th>Unit Pengelola</th>
                        <th>Sumber data</th>
                        <th>Tahun</th>
                        <th>Created at</th>
                        <th>Updated at</th>
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

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.kamus_sub') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.unit_pengelola_id = $('#bidang').val();
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
                    name: 'nama_kpi',
                    width: '30%'

                }, {
                    data: 'target',
                    name: 'target',
                    width: '30%'
                },
                {
                    data: 'unit_pemilik_kpi',
                    name: 'unit_pemilik_kpi',
                    width: '30%'

                },

                {
                    data: 'nama_unit',
                    name: 'nama_unit'
                },
                {
                    data: 'sumber_data',
                    name: 'sumber_data',
                    width: '30%'


                },
                {
                    data: 'tmtahun_id',
                    name: 'tmtahun_id',

                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('DD-MM-YYYY HH:mm:ss');
                    }
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('DD-MM-YYYY HH:mm:ss');
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
        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            $('.modal-dialog').css({
                'min-width': '95%'
            });
            $('#formmodal').modal('show');

            id = $(this).data('id');
            addUrl =
                '{{ route('master.kamus.edit', ':id') }}'.replace(':id', id);
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
