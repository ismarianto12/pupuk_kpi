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

        <div class="table-responsive">
            <table id="datatable" class="display table table-bordered table-hover" style="overflow:auto;width:120%">
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
                <tbody id="tb">
                </tbody>
            </table>
        </div>
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
            ordering: false,
            iDisplayLength: 100,
            processing: true,


            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        orthogonal: 'export'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        orthogonal: 'export'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        orthogonal: 'export'
                    }
                }
            ],

            ajax: {
                url: "{{ route('api.assingment') }}",
                method: 'POST',
                _token: "{{ csrf_token() }}",
                data: function(data) {
                    data.tahun_id = $('#tahun_id').val();
                }
            },

            columns: [{
                    data: 'nama_kpi',
                    name: 'nama_kpi',

                }, {
                    data: 'nama_satuan',
                    name: 'nama_satuan',
                },
                {
                    data: 'target',
                    name: 'target',

                },
                {
                    data: 'nama_polaritas',
                    name: 'nama_polaritas',

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
                    name: 'total',

                },
                {
                    data: 'action',
                    name: 'action',

                }
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.status === 'prospektiff') {
                    $('td:eq(0)', row).attr('colspan', 6);
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');



                } else if (data.status === 'subprospektiff') {

                    $('td:eq(0)', row).attr('colspan', 6);
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');

                }
            }
        });

        $('#datatable').on('click', '#create', function() {
            event.preventDefault();
            // var tr = $(this).closest('tr');
            // var td = $(this).closest('td');
            // var row = table.row(tr);
            // var rowData = row.data();

            // if (row.child.isShown()) {
            //     row.child.hide();
            //     tr.removeClass('shown');

            // } else {
            //     // Open this row  
            //     tr.append(
            //         `<tr><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td></tr>`
            //     );

            //     tr.addClass('shown');
            // }
            {{-- var tr = $(event.target).closest("tr");
            var prevtr = tr.prev();
            var name = prevtr.find("tr:first").text();
            $('#tb').append(
                `<tr><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td><td>Data Input : </td></tr>`
            ); 
            console.log(name); --}}
        });
    });




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
                    toastr.success('data kamus berhasil di hapus');

                    $('#datatable').DataTable().ajax.reload();
                }, "JSON").fail(function(data) {

                });
            }

        });
    });

    $('select').on('change', function() {
        $('#datatable').DataTable().ajax.reload();
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
            $.post("{{ route('kamus.assingment.destroy', ':id') }}", {
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
                $('#ket').html(`<div class="alert alert-danger">${ket}</div>`);
            });
        }
    }
</script>
@endsection
