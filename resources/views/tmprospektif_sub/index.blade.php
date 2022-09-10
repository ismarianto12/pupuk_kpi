@extends('layouts.template')
@section('title', '::: master Prospektif :::')


@section('content')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-list"></i> Sub Prospektif',
        'label' => 'Kembali',
        'ajax_button' => '',
    ])
@endsection
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-right">
                <div class="button-group">
                    <button class="btn btn-info btn-round btn-sm" id="tambah">
                        <i class="fa fa-plus"></i>
                        Add Data
                    </button>

                    <button class="btn btn-danger btn-round btn-sm" id="add_data" onclick="javascript:confirm_del()">
                        <i class="fa fa-minus"></i>
                        Delete selected
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="form-group row">
                <label class="form-label col-md-3"> Tahun </label>
                <div class="col-md-4">
                    <select class="form-control" name="tmtahun_id" id="tmtahun_id">
                        <option value="">all data</option>
                        @foreach ($tahun as $tahuns)
                            <option value="{{ $tahuns->id }}"> {{ $tahuns->tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-3">Parent Prospective </label>
                <div class="col-md-4">
                    <select class="form-control" name="tmprospektif_id" id="tmprospektif_id">
                        <option value="">all data</option>

                        @foreach ($prospektif as $prospektifs)
                            <option value="{{ $prospektifs->id }}"> {{ $prospektifs->nama_prospektif }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-3">Unit Kerja </label>
                <div class="col-md-4">
                    <select class="form-control" name="tmunit_kerja" id="unit_kerja">
                        <option value="">all data</option>

                        @foreach ($tmunit_kerja as $tmunit_kerjas)
                            <option value="{{ $tmunit_kerjas->id }}"> {{ $tmunit_kerjas->nama }}</option>
                        @endforeach
                    </select>

                </div>
            </div>


            <table id="datatable" class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Kode</th>
                        <th>Tahun</th>
                        <th>Nama Prospektif</th>
                        <th>Created By</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>

<div id="panel_tambah" class="offcanvas offcanvas-right pt-5 pb-5" style="100%">

    <div class="offcanvas-content px-10" style="overflow:auto">
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
                url: "{{ route('api.prospektif_sub') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tmtahun_id = $('#tmtahun_id').val();
                    data.tmunit_kerja = $('#tmunit_kerja').val();

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
                    data: 'kode_sub',
                    name: 'kode_sub'
                }, {
                    data: 'tahun',
                    name: 'tahun',
                }, {
                    data: 'nama_prospektif_sub',
                    name: 'nama_prospektif_sub'
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('DD-MM-YYYY HH:mm:ss');
                    }
                },
                {
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

            ],
            rowGroup: {
                emptyDataGroup: '',
                startRender: function(rows, group) {
                    return $('<tr/>')
                        .append(
                            '<td colspan="10" style="background: #45d942; text-align: center; height: 14px; color: #fff;"><i class="fa fa-list"></i> ' +
                            group + '</td>')

                },
                endRender: null,
                dataSrc: ['nama_prospektif']
            }
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
            $.post("{{ route('master.prospektif_sub.destroy', ':id') }}", {
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

            addUrl = '{{ route('master.prospektif_sub.create') }}';
            $('#form_content').load(addUrl);
        });

        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            console.log('sdada');
            $('#panel_tambah').addClass('offcanvas-on');
            $('#overlay').addClass('offcanvas-overlay');

            var id = $(this).data('id');
            addUrl =
                '{{ route('master.prospektif_sub.edit', ':id') }}'.replace(':id', id);
            $('#form_content').load(addUrl);
        });

    });
</script>
@endsection
