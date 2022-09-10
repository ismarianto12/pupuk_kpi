@extends('layouts.template')
@section('title', 'List Data Assigment per Unit kerja')


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
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-users"></i> List Data Assigment per Unit kerja',
        'label' => 'Kembali',
        'ajax_button' => '',
    ])
@endsection
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-right">
                <div class="button-group">

                    <a href="{{ route('assignmen.list_assigment.create') }}" class="btn btn-success btn-round btn-sm">
                        <i class="fa fa-plus"></i>
                    </a>

                    <a href="javascript:void(0)" class="btn btn-danger btn-round btn-sm" id="add_data"
                        onclick="javascript:confirm_del()">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="form-group row">
                <label class="control-label col-md-3">
                    Unit Kerja </label>
                <br />
                <div class="col-md-4">
                    <select class="form-control" name="unit_id" id="tmunit_id">
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
                    <select class="form-control" name="tahun_id" id="tmtahun_id">
                        <option value="">--Semua Data--</option>
                        @foreach ($tahun as $tahuns)
                            <option value="{{ $tahuns->id }}">
                                {{ $tahuns->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-md-3">
                    Batch </label>
                <br />
                <div class="col-md-4">
                    <select class="form-control" name="unit_id" id="tmunit_id">
                        <option value="">--Semua Data--</option>
                        @foreach (Properti_app::getBatch() as $batch)
                            <option value="{{ $batch->batch }}">
                                {{ $batch->batch }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if (Session::has('msg'))
                @php echo Session::get('msg') @endphp
            @endif
            <table id="datatable" class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nama Unit</th>
                        <th>Tahun</th>
                        <th>Batch</th>
                        <th>Created By</th>
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
        $.fn.dataTable.ext.errMode = 'none';

        $('#tmunit_id').select2({
            placeholder: "Pilih data unit kerja"
        });

        $('#tmtahun_id').select2({
            placeholder: "Pilih data tahun"
        });
        $('#batch').select2({
            placeholder: "Pilih data Batch"
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.list_assigment') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tmtahun_id = $('#tmtahun_id').val();
                    data.tmunit_id = $('#tmunit_id').val();
                    data.batch = $('#batch').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    align: 'center',
                    className: 'text-center'
                }, {
                    data: 'nama_unit',
                    name: 'nama_unit'
                }, {
                    data: 'tahun',
                    name: 'tahun',
                }, {
                    data: 'batch',
                    name: 'batch'
                },
                {
                    data: 'name',
                    name: 'name'
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
            $.post("{{ route('assignmen.list_assigment.destroy', ':id') }}", {
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
            var batch = $(this).data('batch');
            var tmunit_id = $(this).data('tmunit_id');
            var tmtahun_id = $(this).data('tmtahun_id');
            addUrl =
                '{{ Url('assignmen/list_assigment/') }}/' + tmunit_id + '/edit/?tmunit_id=' +
                tmunit_id +
                '&tmtahun_id=' + tmtahun_id + '&batch=' + batch
            window.location.href = addUrl;
        });


        $('select').on('change', function() {
            $('#datatable').DataTable().ajax.reload();
        });
        $('#datatable').on('click', '#view', function(e) {
            e.preventDefault();
            var batch = $(this).data('batch');
            var tmunit_id = $(this).data('tmunit_id');
            var tmtahun_id = $(this).data('tmtahun_id');
            $('.modal-dialog').css({
                'min-width': '90%'
            });
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ Url('assignmen/list_assigment/') }}/' + tmunit_id + '?tmunit_id=' + tmunit_id +
                '&tmtahun_id=' + tmtahun_id + '&batch=' + batch;
            $('#form_content').load(
                addUrl);

        })

    });
</script>
@endsection
