@extends('layouts.template')
@section('title', 'Level Akses')
@section('content')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-list"></i> Maste Data Level Akses',
        'label' => 'Kembali',
        'ajax_button' => '',
    ])
@endsection
<div class="col-md-12">
    <div class="card">
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
                            <th>Kode</th>
                            <th>Level Akses</th>
                            <th>Di Buat Oleh</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets') }}/js/plugin/datatables/datatables.min.js"></script>
<script>
    // table 
    jQuery(document).ready(function() {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [1, 'asc'],
            pageLength: 10,
            ajax: {
                url: "{{ route('api.tmlevel') }}",
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
                    data: 'level_kode',
                    name: 'level_kode'
                }, {
                    data: 'level',
                    name: 'level',
                }, {
                    data: 'nama',
                    name: 'nama'
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
            $.post("{{ route('master.tmlevel.destroy', ':id') }}", {
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


            });
        }
    }

    // addd
    $(function() {
        $('#add_data').on('click', function() {
            $('#formmodal').modal('show');
            addUrl =
                '{{ route('master.tmlevel.create') }}';
            $('#form_content').html('<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>').load(addUrl);
        });

        // edit
        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl =
                '{{ route('master.tmlevel.edit', ': id ') }}'
                .replace(':id', id);
            $('#form_content').html('<center><h3><img src="{{ asset('assets/img/loading.gif') }}" class="img-responsive" style="width:350px;height:250px"></h3></center>').load(addUrl);

        })
    });
</script>
@endsection
