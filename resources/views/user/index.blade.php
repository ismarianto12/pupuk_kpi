@extends('layouts.template')
@section('content')
@section('title', 'Manajemen user dan hak akses aplikasi')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-copy"></i> All Data',
        'label' => '#',
        'ajax_button' => '',
    ])
@endsection
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-right">
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
        <div class="card-body">
            <!-- Modal -->
            <div class="modal fade" id="formmodal" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document" style=" min-width: 80%;">
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

            <div class="form-group row">
                <label for="name" class="col-md-2 text-left">Level Akses <span
                        class="required-label">*</span></label>
                <div class="col-md-4">
                     <select name="tmlevel_id" class="form-control">
                        @foreach ($level as $levels)
                            <option value="{{ $levels->id }}">[{{ $levels->level_kode }}] - {{ $levels->level }}</option>
                        @endforeach
                    </select>
                </div>
                <label for="name" class="col-md-2 text-left">Unit <span class="required-label">*</span></label>
                <div class="col-md-4">
                    <select name="tmunit_id" class="form-control">
                        @foreach ($unit as $units)
                            <option value="{{ $units->id }}">[{{ $units->unit_kode }}] - {{ $units->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
<br /><br /><br />
            <div class="table-responsive">
                <table id="datatable" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Unit</th>
                            <th>Username</th>
                            <th>Email Unit / Bidang</th>
                            <th>Level</th>
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
                url: "{{ route('api.user') }}",
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
                data: 'nama_unit',
                name: 'nama_unit'
            }, {
                data: 'username',
                name: 'username',
            }, {
                data: 'email',
                name: 'email'
            }, {
                data: 'level',
                name: 'level'
            }, {
                data: 'action',
                name: 'action'
            }]
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
            $.post("{{ route('master.user.destroy', ':id') }}", {
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
            addUrl = '{{ route('master.user.create') }}';
            $('#form_content').load(addUrl);
        });

        // edit
        $('#datatable').on('click', '#edit', function(e) {
            e.preventDefault();
            $('#formmodal').modal('show');
            id = $(this).data('id');
            addUrl = '{{ route('master.user.edit', ':id') }}'.replace(':id', id);
            $('#form_content').load(addUrl);

        })
    });
</script>
@endsection
