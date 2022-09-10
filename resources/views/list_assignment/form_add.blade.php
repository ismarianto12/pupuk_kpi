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
        <h4><i class="fa fa-users"></i>Tambah data Assigment Kamus </h4>
        <div class="table-responsive">
            <form action="" method="GET" id="formdata">
                <table class="display table table-bordered table-hover" style="width: 100%; font-size: 12px;">
                    <thead style="
    background: #55b16d;
    color: #fff;
    text-align: center;
">
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

                    @php
                        $j = 1;
                    @endphp
                    @foreach ($render as $as => $renders)
                        @php
                            $tmprospektif_id = $renders['tmprospektif_id']['val'] ?? $renders['tmprospektif_id']['val'];
                            $tmprospektif_sub_id = $renders['tmprospektif_sub_id']['val'] ?? $renders['tmprospektif_sub_id']['val'];
                        @endphp

                        <tbody id="table_body{{ $j.'_'.$tmprospektif_id.'_'.$tmprospektif_sub_id }}">
                            <tr>
                                @if ($renders['parent']['val'])
                                    <td colspan="7">
                                        @php echo $renders['nama_kpi']['val'] @endphp
                                    </td>

                                    <td>

                                        <a href="" class="btn btn-sm btn-clean btn-icon"
                                            title="Assignemn data as unit kerja"
                                            onclick="javascript:append_table({{ $j }},{{ $tmprospektif_id }},{{ $tmprospektif_sub_id }})"><i
                                                class="fa fa-plus"></i></a>

                                    </td>
                                @else
                                    <td>
                                        {{ $j }} .&nbsp;&nbsp;.&nbsp;&nbsp;.&nbsp;&nbsp;
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
                                            title="Assignemn data as unit kerja"
                                            onclick="javascript:append_table({{ $j }},{{ $tmprospektif_id }},{{ $tmprospektif_sub_id }})"><i
                                                class="fa fa-plus"></i></a>
                                    </td>
                                @endif
                            </tr>

                        </tbody>
                        @php
                            $j++;
                        @endphp
                    @endforeach


                </table>

                <div class="form-group row">
                    <label class="control-label col-md-3">
                        Unit Kerja </label>
                    <br />
                    <div class="col-md-4">
                        <select class="form-control" name="tmunit_id" id="unit_id" required>
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
                        <select class="form-control" name="tmtahun_id" id="tahun_id" required>
                            <option value="">--Semua Data--</option>
                            @foreach ($tahun as $tahuns)
                                <option value="{{ $tahuns->id }}">
                                    {{ $tahuns->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br /><br /><br /><br />

                <div class="button-group">
                    <button type="submit" class="btn btn-info btn-md">
                        <i class="fa fa-save"></i>
                        Save Data
                    </button>
                    <button class="btn btn-danger btn-round btn-md" id="add_data" onclick="javascript:confirm_del()">
                        <i class="fa fa-reload"></i>
                        Reset Form
                    </button>
                </div>

            </form>
            <br /><br /><br /><br />
        </div>
    </div>

    <script>
        (function() {
            'use strict';
            var forms = document.getElementsByClassName('simpan');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

        })();
        jQuery(document).ready(function() {

            $('#tahun_id').select2({
                placeholder: "Pilih data tahun"
            });

            $('#formdata').on('submit', function() {
                event.preventDefault();
                Swal.fire({
                    title: 'confirm',
                    text: 'Semua inputan sudah benar anda yakin ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {

                        if ($(this)[0].checkValidity() === false) {

                            e.stopPropagation();
                            toastr.error(
                                'silahkan cek kembali field yang kosong'
                            );

                        } else {
                            let data = $('input, select').serialize();
                            $.ajax({
                                url: "{{ route('assignmen.list_assigment.store') }}",
                                method: "POST",
                                data: $(this).serialize(),
                                chace: false,
                                async: false,
                                beforeSend: function() {
                                    Swal.showLoading();
                                },
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == 1) {
                                        $('#formmodal').modal(
                                            'hide');
                                        $('#panel_tambah')
                                            .removeClass(
                                                'offcanvas-on');
                                        $('#overlay')
                                            .removeClass(
                                                'offcanvas-overlay'
                                            );
                                        $('#formmodal').modal(
                                            'hide');
                                        toastr.success(data
                                            .msg);
                                        window.location.href =
                                            "{{ Url('assignmen/list_assigment') }}";
                                    } else if (data.status ==
                                        2) {
                                        $('#formmodal').modal(
                                            'hide');
                                        $('#panel_tambah')
                                            .removeClass(
                                                'offcanvas-on');
                                        $('#overlay')
                                            .removeClass(
                                                'offcanvas-overlay'
                                            );
                                        $('#formmodal').modal(
                                            'hide');
                                        swal.fire('info', data
                                            .msg, 'info');
                                        toastr.error(data.msg);
                                    }

                                },
                                error: function(data) {
                                    var div = $('.container');
                                    setInterval(function() {
                                        var pos = div
                                            .scrollTop();
                                        div.scrollTop(
                                            pos + 2);
                                    }, 10)
                                    err = '';
                                    respon = data.responseJSON;
                                    $.each(respon.errors,
                                        function(index,
                                            value) {
                                            err += "<li>" +
                                                value +
                                                "</li>";
                                        });
                                    toastr.error(
                                        'Silahkan cek inputan berikut :' +
                                        err);

                                    $('.ket').html(
                                        "<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>mohon perbaiki kesalahan berikut : </strong> " +
                                        respon.message +
                                        "<ol class='pl-3 m-0'>" +
                                        err +
                                        "</ol></div>");

                                }
                            });

                            return false;
                        }
                    }
                });

                $('table').on('click', '#delete', function(e) {
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
                            }, "JSON").fail(function(data) {});
                        }

                    });
                });
            });

        });



        function append_table(increment, tmprospektif_id, tmprospektif_sub_id) {
            event.preventDefault();
            //jika sub prospektif kosong maka akan tampil
            jQuery('#table_body' + increment+'_'+tmprospektif_id+'_'+tmprospektif_sub_id).append(`     
                <tr id="tr_child_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}"> 
                <td>
                <input type="hidden" name="tmprospektif_id[]" value="${tmprospektif_id}" />
                <input type="hidden" name="tmprospektif_sub_id[]" value="${tmprospektif_sub_id}" /> 
                <select id="kamus_parent_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" class="data_kpi_list form-control" name="tmkamus_kpi_id[]" onChange="javascript:cari_kamus($(this).val(),${tmprospektif_id},${tmprospektif_sub_id},${increment})" required> 
                  <option value="">Pilih Data Nama Kpi</option>   
                    @foreach ($tmkamus_kpi as $tmkamus_kpis)
                        <option value="{{ $tmkamus_kpis->id }}">{{ $tmkamus_kpis->nama_kpi }}</option>
                    @endforeach 
                </select> 
                </td>
                <td><input class="form-control" id="tmsatuanid_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="tmsatuan_id[]" /></td>
                <td><input class="form-control" id="target_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="target[]" /></td>
                <td><input class="form-control" id="polaritas_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="polaritas[]" /></td>
                <td><input class="form-control" id="sub_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="sub[]" /></td>
                <td><input class="form-control" id="kpi_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="kpi[]" /></td>
                <td><input class="form-control" id="total_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}" name="total[]" /></td>
                <td>
                <a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" onclick="javascript:remove_table(${increment}, ${tmprospektif_id}, ${tmprospektif_sub_id})"><i class="fa fa-minus"></i></a>
                </td>
                </tr> 
            `);
    
            return false;
        }

        function remove_table(increment, tmprospektif_id, tmprospektif_sub_id) {
            event.preventDefault();
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
                    jQuery(`#tr_child_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}`).remove();
                    jQuery(`#tr_subchild_${increment}_${tmprospektif_id}_${tmprospektif_sub_id}`).remove();
                }
            });
        }

         
         function cari_kamus(kamus_id, tmprospektif_id, tmprospektif_sub_id,urutan) { 
 
            //$(`#tr_subchild_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).remove();  
             $.ajax({
                url: "{{ route('kamus.kamus_request_ajax') }}",
                method: "GET",
                data: {
                    id: kamus_id
                },
                chace: false,
                async: false,
                dataType: 'json',

                success: function(data) {  
                    $(`#kamus_parent_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).attr('disabled',true);
                    $(`#tmsatuanid_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).attr('disabled', true).val(data
                        .nama_satuan);
                    $(`#target_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).attr('disabled', true).val(data
                        .target);
                    $(`#polaritas_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).attr('disabled', true).val(data
                        .nama_polaritas);
                    $(`#sub_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).val(0);
                    $(`#kpi_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).val(0);
                    $(`#total_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).val(0);

                    if (data.tmkamus_sub_id == 0 || data.tmkamus_sub_id == null) {
                        
                    // $(`#tr_subchild_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).remove(); 


                    } else { 
                        $.ajax({
                            url: "{{ route('kamus.kamus_request_ajax_sub') }}",
                            method: "GET",
                            data: {
                                tmkamus_sub_id: data.tmkamus_sub_id
                            },
                            chace: false,
                            async: false,
                            dataType: 'json',
                            success: function(data) {
                                console.log('passed data from child element');
                                dataappend = ''; 
                                tmkamus_sub_id = data.tmkamus_sub_id; 
                                $.each(data, function(index, value) {  
                                    dataappend += `<tr id="tr_subchild_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" style="background:#3699FF"> 
                                        <td> 
                                        <span class="btn btn-icon btn-light btn-hover-primary btn-sm">${index+1}</span>
                                        <input type="hidden" name="tmprospektif_id[]" value="0" />
                                        <input type="hidden" name="tmprospektif_sub_id[]" value="${tmkamus_sub_id}" /> 
                                         ${value.nama_kpi_sub}
                                       </td>

                                            <td><input class="form-control" id="tmsatuanid_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="tmsatuan_id[]" value="${value.nama_satuan}" readonly/></td>
                                            <td><input class="form-control" id="target_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="target[]" value="${value.target}" readonly/></td>
                                            <td><input class="form-control" id="polaritas_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="polaritas[]" value="${value.nama_polaritas}" readonly/></td>
                                            <td><input class="form-control" id="sub_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="sub[]" value="0"/></td>
                                            <td><input class="form-control" id="kpi_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="kpi[]" value="0"/></td>
                                            <td><input class="form-control" id="total_${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}" name="total[]" value="0"/></td>
                                        <td></td>
                                    </tr>`  
                                }); 
                               $(`#table_body${urutan}_${tmprospektif_id}_${tmprospektif_sub_id}`).append(dataappend); 

                            
                            },
                            error: function(data) {
                                toastr.error(
                                    'child get error parsing data if response can\'t retrive'
                                );
                            }
                        });
                        //end call function  
                    }
                },
                error: function(data) {
                    toastr.error(
                        'parent get error parsing data if response can\'t retrive');
                },
            }); 
        }
    </script>
@endsection
