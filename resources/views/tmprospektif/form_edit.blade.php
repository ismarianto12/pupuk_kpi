 <div class="card-title align-items-start flex-column">
     <h3 class="card-label font-weight-bolder text-dark">Edit data prospektif</h3>
     <span class="text-muted font-weight-bold font-size-sm mt-1">Edit form</span>
 </div>

 <div class="ket"></div>
 <form id="exampleValidation" method="POST" class="simpan needs-validation" novalidate="">


     <label class="control-label"> Nama Prospektif</label>
     *) required

     <input class="form-control" name='nama_prospektif' value="{{ $nama_prospektif }}" />


     <br />
     <label class="control-label"> Kode </label>
     <input class="form-control" name='kode' value="{{ $kode }}" />

     <br />
     <label class="control-label"> Assing ke Unit : </label>
     @foreach (Properti_app::getUnitkerja() as $units)
         @php
             $checked = strpos($tmlevel_id, $units->id) !== false ? 'checked' : '';
         @endphp
         <div class="checkbox-inline">
             <label class="checkbox checkbox-success">
                 <input type="checkbox" name="tmlevel_id[]" value="{{ $units->id }}" {{ $checked }} />
                 <span></span>{{ $units->level }}</label>
         </div>
     @endforeach
     <br />

     <label class="control-label"> Tahun KPI</label>
     *) required

     <select class="form-control" name="tmtahun_id" class="form-control">
         @foreach (Properti_app::getActiveYear() as $tahuns)
             <option value={{ $tahuns->id }}>{{ $tahuns->tahun }}</option>
         @endforeach
     </select>


     <br /> <br />
     <div class="row">
         <div class="col-md-12">
             <input class="btn btn-success" type="submit" value="Simpan">
             <button id="reset" class="btn btn-danger" type="reset">Batal</button>
         </div>
     </div>
 </form>



 <script type="text/javascript">
     (function() {
         'use strict';
         var forms = document.getElementsByClassName('needs-validation');
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
         $('.simpan').on('submit', function(e) {
             e.preventDefault();
             $.ajax({
                 url: "{{ route('master.prospektif.update',$id) }}",
                 method: "PUT",
                 data: $(this).serialize(),
                 chace: false,
                 async: false,
                 success: function(data) {
                     $('#datatable').DataTable().ajax.reload();
                     $('#formmodal').modal('hide');
                     $('#panel_tambah').removeClass('offcanvas-on');
                     $('#overlay').removeClass('offcanvas-overlay');
                     $('#formmodal').modal('hide');

                     toastr.success('data pegawai berhasil di simpan');


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
                     toastr.error('Silahkan cek inputan berikut :');

                     $('.ket').html(
                         "<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>??</span></button><strong>mohon perbaiki kesalahan berikut : </strong> " +
                         respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");

                 }
             })

         });
         $('#reset').on('click', function(e) {
             $('#panel_tambah').removeClass('offcanvas-on');
             $('#overlay').removeClass('offcanvas-overlay');
             $('#formmodal').modal('hide');

         });
     });
 </script>
