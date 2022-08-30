@extends('layouts.template')
@section('title', 'Assigment data kamus kpi per unit kerja')
@section('content')

@section('toolbars')
    @include('layouts.toolbars', [
        'url' => '#',
        'par' => '<i class="fa fa-list"></i>Assigment kamus KPI',
        'label' => '',
        'ajax_button' => null,
    ])
@endsection
<div class="modal fade" id="_xformmodal" role="dialog" aria-hidden="true">
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
    <div class="card-header">
    </div>


    <div class="card-body">

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

        <div class="table-responsive" style="overflow:auto">
            <table id="datatable" class="display table table-bordered table-hover"
                style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th rowspan="2"><strong>KPI</strong></th>
                        <th rowspan="2"><strong>Satuan</strong></th>
                        <th rowspan="2"><strong>Target</strong></th>
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
                    @php
                        $j = 0;
                    @endphp
                    @foreach ($render as $as => $renders)
                        <tr>
                            @if ($renders['parent']['val'])
                                <td>
                                    @php echo $renders['nama_kpi']['val'] @endphp
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="" class="btn btn-sm btn-clean btn-icon"
                                        title="Assignemn data as unit kerja" id="create"
                                        data-id="{{ $renders['id']['val'] }}"
                                        data-status="{{ $renders['status']['val'] }}"><i class="fa fa-plus"></i></a>
                                </td>
                            @else
                                <td>
                                    {{ $j }} .&nbsp;
                                    @php echo $renders['nama_kpi']['val'] @endphp
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="" class="btn btn-sm btn-clean btn-icon"
                                        title="Assignemn data as unit kerja" id="create"
                                        data-id="{{ $renders['id']['val'] }}"><i class="fa fa-edit"></i></a>
                                    <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"
                                        title="Detail data" data-id="{{ $renders['id']['val'] }}"><i
                                            class="fa fa-trash"></i></a>
                                </td>
                            @endif
                        </tr>
                        @php
                            $j++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        jQuery(document).ready(function() {
            $('#unit_id').select2({
                placeholder: "Pilih data unit kerja"
            });

            $('#tahun_id').select2({
                placeholder: "Pilih data tahun"
            });

            var table = $('#datatable').DataTable({
                "ordering": false,
                'iDisplayLength': 100
            });



            $('#datatable').on('click', '#create', function(e) {
                e.preventDefault();
                $('#_xformmodal').modal('show');
                $('.modal-dialog').css({
                    'min-width': '70%'
                });

                id = $(this).data('id');
                status = $(this).data('status');
                addUrl = '{{ route('kamus.assingment.create') }}?tmprospektif_id=' + id + '&status=' +
                    status;
                $('#form_content').load(addUrl);
            })
            $('#datatable').on('click', '#delete', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'confirm',
                    text: 'Anda akan menghapus data ini ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {

                        id = $(this).data('id');
                        $.post("{{ route('kamus.assingment.destroy', ':id') }}", {
                            '_method': 'DELETE',
                            'id': id
                        }, function(data) {

                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Data berhasil di hapus',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            window.location.href = "";
                        }, "JSON").fail(function(data) {



                        });
                    }

                });
            });
            $('select').on('change', function() {
                $('#datatable').DataTable().ajax.reload();
            });
        });
    </script>
@endsection
