 <div class="card">
     <div class="card-header">
         <h4 class="card-title"><i class="fa fa-copy"></i>Tambah Data Master Sub Kamus</h4>
         <label class="control-label col-md-6"></label>
     </div>

     <div class="ket"></div>
     <div class="card-body">
         <form id="exampleValidation" method="POST" class="simpan needs-validation" novalidate="">
             <div class="form-group row">
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Parent Kamus KPI</label>
                     <select class="form-control" name="tmkamus_kpi_id" id="tmkamus_kpi_id">
                         @foreach ($tmkamus_kpi as $tmkamus_kpis)
                             <option value="{{ $tmkamus_kpis->id }}">{{ $tmkamus_kpis->nama_kpi }}</option>
                         @endforeach
                     </select>
                 </div>
             </div>

             <div class="form-group row">

                 <div class="col-md-6">
                     <label class="control-label col-md-6">Nama KPI</label>

                     <input name="nama_kpi_sub" class="form-control" id="nama_kpi" required />
                 </div>
             </div>
             <div class="form-group row">

                 <div class="col-md-6">
                     <label class="control-label col-md-6">Definisi KPI</label>
                     <textarea class="form-control" name="definisi" required></textarea>
                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Tujuan</label>
                     <textarea class="form-control" name="tujuan" id="editor" required></textarea>
                 </div>
             </div>
             <div class="form-group row">
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Satuan</label>

                     <select id="tmsatuan_id" class="form-control" name="tmsatuan_id" required>
                         @foreach ($satuan as $satuans)
                             <option value="{{ $satuans->id }}">
                                 {{ $satuans->nama_satuan }} </option>
                         @endforeach
                     </select>
                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Formula </label>
                     <textarea class="form-control" name="formula_penilaian" id="editor1" required></textarea>
                 </div>
             </div>
             <div class="form-group row">
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Target</label>
                     <input name="target" class="form-control" id="target" required />
                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Frekuensi</label>
                     <select class="form-control" id="tmfrekuensi_id" name="tmfrekuensi_id" required>

                         @foreach ($frekuensi as $frekuensi)
                             <option value="{{ $frekuensi->id }}">
                                 {{ $frekuensi->nama_frekuensi }} </option>
                         @endforeach
                     </select>
                 </div>
             </div>
             <div class="form-group row">
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Polaritas</label>
                     <select id="tmpolaritas_id" class="form-control" name="tmpolaritas_id">
                         @foreach ($polaritas as $polaritass)
                             <option value="{{ $polaritass->id }}">
                                 {{ $polaritass->nama_polaritas }} </option>
                         @endforeach
                     </select>

                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Unit Pemilik KPI</label>
                     <input name="unit_pemilik_kpi" class="form-control" id="unit_pemilik_kpi" required />
                 </div>
             </div>
             <div class="form-group row">
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Unit Pengelola</label>

                     <select id="unit_pengelola" class="form-control" name="unit_pengelola_kpi">
                         @foreach ($unit_pengelola as $unit_pengelolas)
                             <option value="{{ $unit_pengelolas->id }}">
                                 {{ $unit_pengelolas->nama }} </option>
                         @endforeach
                     </select>
                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Sumber data</label>
                     <input name="sumber_data" class="form-control" id="sumber_data" required />
                 </div>
             </div>
             <div class="form-group row">

                 <div class="col-md-6">
                     <label class="control-label col-md-6"><b>Jenis Pengukuran :</b></label>
                     <hr />
                     <div style="margin-left:30px">
                         @foreach ($jenis_pengukuran as $jenis_pengukurans)
                             <div class="checkbox-inline">
                                 <label class="checkbox checkbox-success">
                                     <input type="checkbox" name="jenis_pengukuran[]"
                                         value="{{ $jenis_pengukurans->id }}" />
                                     <span></span>{{ $jenis_pengukurans->jenis_pengukuran }}</label>
                             </div>
                         @endforeach
                     </div>
                 </div>
                 <div class="col-md-6">
                     <label class="control-label col-md-6">Tahun KPI</label>
                     <select id="tahun_id" class="form-control" name="tahun_id">
                         @foreach ($tahun as $tahuns)
                             <option value="{{ $tahuns->id }}">
                                 {{ $tahuns->tahun }}</option>
                         @endforeach
                     </select>
                 </div>
             </div>

             <div class="form-group row">

                 <div class="col-md-6">
                     <label class="control-label col-md-6"><b>Catatan Kamus :</b></label>
                     <textarea class="form-control" name="catatan_kamus" id="editor2"> </textarea>

                 </div>
             </div>


             <div class="card-footer">
                 <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-save"></i>Simpan data</button>
                 <button type="reset" class="btn btn-danger">Cancel</button>
             </div>

         </form>
     </div>
 </div>
 <script type="text/javascript">
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
     $(function() {
         CKEDITOR.replace('editor');
         CKEDITOR.replace('editor1');
         CKEDITOR.replace('editor2');
         $('#tmkamus_kpi_id').select2({
             placeholder: "Pilih Parent Kamus"
         });
         $('#unit_pengelola').select2({
             placeholder: "Select a state"
         });
         $('#tahun_id').select2({
             placeholder: "Select a state"
         });

         $('#tmpolaritas_id').select2({
             placeholder: "Select a state"
         });
         $('#tmfrekuensi_id').select2({
             placeholder: "Select a state"
         });
         $('#tmsatuan_id').select2({
             placeholder: "Select a state"
         });

         $('.simpan').on('submit', function(e) {
             e.preventDefault();
             if ($(this)[0].checkValidity() === false) {

                 e.stopPropagation();
                 toastr.error('silahkan cek kembali field yang kosong');

             } else {
                 $('.card-body').css({
                     'opacity': '0.2'
                 });
                 for (instance in CKEDITOR.instances) {
                     CKEDITOR.instances.editor.updateElement();
                     CKEDITOR.instances.editor1.updateElement();
                     CKEDITOR.instances.editor2.updateElement();
                 };
                 $.ajax({
                     url: "{{ route('master.kamus_sub.store') }}",
                     method: "POST",
                     data: $(this).serialize(),
                     chace: false,
                     async: false,
                     success: function(data) {
                         $('#formmodal').modal('hide');
                         $('#datatable').DataTable().ajax.reload();
                         toastr.success('data kamus berhasil di simpan');
                     },
                     error: function(data) {
                         var div = $('.container');
                         setInterval(function() {
                             var pos = div.scrollTop();
                             div.scrollTop(pos + 2);
                         }, 10)
                         err = '';
                         respon = data.responseJSON;
                         $.each(respon.errors, function(index, value) {
                             err += "<li>" + value + "</li>";
                         });

                         toastr.error(err);
                         $('.ket').html(
                             "<div class='container'><div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong></strong> " +
                             respon.message + "<ol class='pl-3 m-0'>" + err +
                             "</ol></div></div>");
                     }
                 })
                 $('.card-body').css({
                     'opacity': '1'
                 });
             }
         });
         $('#jenis_kamus').on('change', function() {
             var jenis = $(this).val();
             option = '';
             $.get("{{ Url('kamus_api/status_kamus') }}/" + jenis, function(data) {
                 $.each(data, function(index, value) {
                     console.log('test' + value['nama_kpi']);
                     option +=
                         `<option value="${value['id']}">${value['nama_kpi']}</option>`;
                 });
             });
             console.log(option);
             $('#parent_child_id').append(option);
         })
     });
 </script>
