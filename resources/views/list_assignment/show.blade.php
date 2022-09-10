<style>


td.description {vertical-align: top;}

</style>

<div class="card card-custom gutter-b">
    <div class="card-header">
      <h4> <i class="fa fa-users"></i> Detail data asssigment KPI </h4>
      <br />
      <h4> Unit Kerja : {{ $unit_kerja }}</h4>
      <br />
      <h4> Tahun : {{$tahun}} </h4>

   
     </div>
    <div class="card-body">
    <div class="button-group">
                    <a href="{{ Url('assignmen/list_assigment_print/'.$id) }}" target="_blank" class="btn btn-info btn-md">
                        <i class="fa fa-save"></i>
                        Print Data
                    </a>
                    <button class="btn btn-danger btn-round btn-md" id="add_data" onclick="javascript:confirm_del()">
                        <i class="fa fa-reload"></i>
                        Close
                    </button>
                </div>
                <br />
        <div class="table-responsive">
                 <table class="display table table-bordered table-hover"
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
                        </tr>

                    </thead>

                    @php
                        $j = 1;
                    @endphp
                    @foreach ($render as $as => $renders)
                        @php
                            $tmprospektif_id = $renders['tmprospektif_id']['val'] ?? $renders['tmprospektif_id']['val'];
                            $tmprospektif_sub_id = $renders['tmprospektif_sub_id']['val'] ? $renders['tmprospektif_sub_id']['val'] : null;
                        @endphp

                        <tbody id="table_body{{ $j }}">


                            @if ($renders['parent']['val'] == false)
                                <tr id="tr_child_{{ $j }}_{{ $j }}">
                                @else
                                <tr>
                            @endif

                            @if ($renders['parent']['val'])
                                <td colspan="8">
                                    @php echo $renders['nama_kpi']['val'] @endphp
                                </td>
                               
                                
                            @else
                                <td> 
                                    @php echo $renders['nama_kpi']['val'] @endphp
                                </td>
                                <td>{{ $renders['nama_satuan']['val'] }}</td>
                                <td>{{ $renders['target']['val'] }}</td>
                                <td>{{ $renders['nama_polaritas']['val'] }}</td>
                                <td>{{ $renders['sub']['val'] }}</td>
                                <td>{{ $renders['kpi']['val'] }}</td>
                                <td>{{ $renders['total']['val'] }}</td>

                               
                            @endif
                            </tr>

                        </tbody>
                        @php
                            $j++;
                        @endphp
                    @endforeach


                </table>

 
      

           
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
            $('#data_kpi_list').select2({
                placeholder: "Pilih data unit kerja"
            });
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
                            toastr.error('silahkan cek kembali field yang kosong');

                        } else {
                            let data = $('input, select').serialize();
                            $.ajax({
                                url: "{{ route('assignmen.list_assigment.update', $id) }}",
                                method: "PUT",
                                data: $(this).serialize(),
                                chace: false,
                                async: false,
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == 1) {
                                        $('#formmodal').modal('hide');
                                        $('#panel_tambah').removeClass('offcanvas-on');
                                        $('#overlay').removeClass('offcanvas-overlay');
                                        $('#formmodal').modal('hide');
                                        toastr.success(
                                            'Sesuai ketentuan data berhasil disimpan'
                                        );
                                    } else if (data.error == 2) {
                                        $('#formmodal').modal('hide');
                                        $('#panel_tambah').removeClass('offcanvas-on');
                                        $('#overlay').removeClass('offcanvas-overlay');
                                        $('#formmodal').modal('hide');
                                        toastr.error(
                                            'Tidak Sesuai ketentuan nilai persentase lebih dari 20%'
                                        );

                                    }

                                },
                                error: function(data) {
                                    var div = $('#container');
                                    setInterval(function() {
                                        var pos = div.scrollTop();
                                        div.scrollTop(pos + 2);
                                    }, 10)
                                    err = '';
                                    respon = data.responseJSON;
                                    $.each(respon.errors, function(index, value) {
                                        err += "<li>" + value + "</li>";
                                    });
                                    toastr.error('Silahkan cek inputan berikut :' +
                                        err);

                                    $('.ket').html(
                                        "<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>mohon perbaiki kesalahan berikut : </strong> " +
                                        respon.message + "<ol class='pl-3 m-0'>" +
                                        err +
                                        "</ol></div>");

                                }
                            });

                            console.log(data);
                            return false;
                        }
                    }
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


        var inc = 1;

        function append_table(n, tmprospektif_id, tmprospektif_sub_id) {
            event.preventDefault();
            console.log(inc);
            var par = inc;


            jQuery('#table_body' + n).append(`     
                <tr id="tr_child_${n}_${par}"> 
                <td>
                <input type="hidden" name="tmprospektif_id[]" value="${tmprospektif_id}" />
                <input type="hidden" name="tmprospektif_sub_id[]" value="${tmprospektif_sub_id}" /> 
                <select class="form-control" id="data_kpi_list" name="tmkamus_kpi_id[]" onChange="javascript:cari_kamus($(this).val(),${n},${par})">
                   <option value="">Pilih Data Nama Kpi</option>  
             @foreach ($tmkamus_kpi as $tmkamus_kpis)
                        <option value="{{ $tmkamus_kpis->id }} ">{{ $tmkamus_kpis->nama_kpi }}</option>
                 @endforeach 
 
                </select>
                </select></td>
                <td><input class="form-control" id="tmsatuanid_${n}_${par}" name="tmsatuan_id[]" /></td>
                <td><input class="form-control" id="target_${n}_${par}" name="target[]" /></td>
                <td><input class="form-control" id="polaritas_${n}_${par}" name="polaritas[]" /></td>
                <td><input class="form-control" id="sub_${n}_${par}" name="sub[]" /></td>
                <td><input class="form-control" id="kpi_${n}_${par}" name="kpi[]" /></td>
                <td><input class="form-control" id="total_${n}_${par}" name="total[]" /></td>
                <td>
                <a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" onclick="javascript:remove_table(${n},${par})"><i class="fa fa-minus"></i></a>
                </td>
                </tr> 
            `);
            inc++;
            return false;
        }

        function remove_table(no, nm) {
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
                    jQuery('#tr_child_' + no + '_' + nm).remove();
                }
            });
        }

        {{-- function getkamus(n) {

            $.get('{{ route('kamus.get_list') }}', function(data) {
                console.log(data);
                ls = '';
                $.each(data, function(index, value) {
                    ls += "<option value=" + value.id + "> " + value.nama_kpi + "</option>";
                });
                $('#data_kpi_list').html(data);

            });
        } --}}

        function cari_kamus(n, nm, npar) {
            let number = nm;
            console.log(nm);

            $.ajax({
                url: "{{ route('kamus.kamus_request_ajax') }}",
                method: "GET",
                data: {
                    id: n
                },
                chace: false,
                async: false,
                dataType: 'json',
                success: function(data) {
                    document.querySelector('#target_');
                    console.log(data);
                    console.log('#target_' + nm + '_' + npar);
                    $('#tmsatuanid_' + nm + '_' + npar).attr('disabled', true).val(data
                        .nama_satuan);
                    $('#target_' + nm + '_' + npar).attr('disabled', true).val(data.target);
                    $('#polaritas_' + nm + '_' + npar).attr('disabled', true).val(data
                        .nama_polaritas);
                    $('#sub_' + nm + '_' + npar).val(0);
                    $('#kpi_' + nm + '_' + npar).val(0);
                    $('#total_' + nm + '_' + npar).val(0);
                },
                error: function(data) {
                    toastr.error('Server tida dapat meload data yang di maksud' + data);
                }
            });
        }


        jQuery(document).ready(function() {
            $('#data_kpi_list').on('change', function() {
                console.log((e) => {
                    console.log(e);
                })
            });
        });
    </script>
 