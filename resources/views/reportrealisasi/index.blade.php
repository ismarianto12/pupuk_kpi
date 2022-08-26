@extends('layouts.template')
@section('title', 'Laporan Realiasi KPI')
@section('content')

@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('kamus/create'),
        'par' => '<i class="fa fa-users"></i> Report Realisasi KPI',
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
            <h4><i class="fa fa-copy"></i> Unit : {{ ucfirst(Auth::user()->name) }}</h4>
        </div>
        <br /><br />

        <div class="table-responsive bordered">


            <div class="form-group row">
                <label class="control-label col-md-3">
                    Tahun KPI </label>
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




            <div class="__danger_button"></div>

            <table id="datatable" class="table table-striped">
                <thead>
                    <tr>
                        <th rowspan="2"><strong>No</strong></th>
                        <th rowspan="2"><strong>KPI</strong></th>
                        <th rowspan="2"><strong>Satuan</strong></th>
                        <th rowspan="2"><strong>Baseline<br />
                                2021</strong></th>
                        <th rowspan="2"><strong>Target<br />
                                2022</strong></th>
                        <th rowspan="2"><strong>Polaritas</strong></th>
                        <th colspan="4" class="text-center">
                            <b>Bobot</b>
                        </th>
                    </tr>
                    <tr>
                        <th>Sub</th>
                        <th>KPI</th>
                        <th>Total</th>
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
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.reportrealisasi') }}",
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
                }, {
                    data: 'tahun',
                    name: 'tahun',
                    width: '150px'
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
                    data: 'sub',
                    name: 'sub',
                },
                {
                    data: 'kpi',
                    name: 'kpi',
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'action',
                    name: 'action'  
                }
            ],
            rowGroup: {
                startRender: null,
                endRender: function(rows, group, data) {
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    console.log(group);
                    var uitkering = '';

                    var sub = rows
                        .data()
                        .pluck('sub')
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    var t_skor_kpi = rows
                        .data()
                        .pluck('skor_kpi')
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    var total = rows
                        .data()
                        .pluck('total')
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    var color = 'style="color:red"';
                    var alltot = intVal(sub) + intVal(t_skor_kpi) + intVal(total);
                    return $('<tr ' + color + '/>')
                        .append('<td colspan="3" style="text-align: right">Total KPI</td>')
                        .append('<td>' + '</td>')
                        .append('<td/>')
                        .append('<td></td>')
                        .append('<td style: "text-align: right;">' + sub + '</td>')
                        .append('<td style: "text-align: right;">' + t_skor_kpi + '</td>')
                        .append('<td style: "text-align: right;">' + total + '</td>')
                        .append('<td style: "text-align: right;">' + alltot + '</td>');

                },
                dataSrc: 'rubrieken.box'
            },
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
    jQuery(document).ready(function() {
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
            console.log(e);
            $('.modal-dialog').css({
                'min-width': '60%'
            });
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ route('master.kamus.show', ':id') }}'.replace(':id', id);
            $('#form_content').html(
                '<center><h3><i class="fa fa-refresh fa-10x"></i></h3></center>').load(
                addUrl);

        })

        let kpi = '';
        let sub = '';
        let total = '';


        $('#datatable').on('change', 'input[name="kpi"]', function() {
            console.log($(this).val());
            kpi = $(this).val();
        })
        $('#datatable').on('change', 'input[name="sub"]', function() {
            console.log($(this).val());
            sub = $(this).val();
        })
        $('#datatable').on('change', 'input[name="total"]', function() {
            console.log($(this).val());
            total = $(this).val();
        })

        $('#datatable').on('click', '#simpan_xx', function(e) {
            e.preventDefault();
            let tmkamus_kpi_id = $(this).data('id');
            $.ajax({
                url: '{{ url('korporat/simpan_data_kpi') }}',
                method: 'POST',
                data: 'kpi=' + kpi + '&sub=' + sub +
                    '&total=' + total + '&tmkamus_kpi_id=' + tmkamus_kpi_id,
                dataType: 'JSON',
                cache: false,
                asynch: false,
                success: function(jd) {
                    toastr.success('data berhasil di simpan');
                },
                error: function(data) {
                    toastr.error('data gagal berhasil di simpan');
                }
            });


        });
        //save data event button save onclick
        let tahun_id = $('select #tahun_id').find('option:selected').val();
        console.log(tahun_id);

        $('.__danger_button').html(` 
   <div class="button-group">
                <a href='{{ Url('report/report_realisasi/excel') }}' target="_blank" class="btn btn-primary btn-round ml-auto btn-sm" id="add_data">
                    <i class="fa fa-plus"></i>
                    Print as Excel
                </a>
                <a href='{{ Url('report/report_realisasi/excel') }}' target="_blank" class="btn btn-danger btn-round btn-sm" id="add_data" onclick="javascript:confirm_del()">
                    <i class="fa fa-minus"></i>
                    Print as PDF
                </a>
            </div>
            <br /> <br /> <br /> <br />
        `);
    });
</script>
@endsection
