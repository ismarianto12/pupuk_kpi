 <div class="card-title align-items-start flex-column">
     <h3 class="card-label font-weight-bolder text-dark">Tambah data prospektif</h3>
     <span class="text-muted font-weight-bold font-size-sm mt-1">Craeate form</span>
 </div>

 <div class="ket"></div>
 <form id="exampleValidation" method="POST" class="simpan needs-validation" novalidate="">


     <label class="control-label"> Nama Prospektif</label>
     *) required
     <input class="form-control" name='nama_prospektif' value="" required />
     <label class="control-label"> Kode </label>
     *) jika ada
     <input class="form-control" name='kode' value="" required />
     <label class="control-label"> Assing ke Unit : </label>
     *) required
     <label class="control-label col-md-6"><b>Assign To :</b></label>
     <hr />

     @foreach (Properti_app::getUnitkerja() as $units)
         <div class="checkbox-inline">
             <label class="checkbox checkbox-success">
                 <input type="checkbox" name="units_kerja[]" value="{{ $units->id }}" required/>
                 <span></span>{{ $units->level }}</label>
         </div>
     @endforeach

     <label class="control-label"> Tahun KPI</label>
     *) require one
     <select class="form-control" name="tmtahun_id" class="form-control" required>
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
                 url: "{{ route('master.prospektif.store') }}",
                 method: "POST",
                 data: $(this).serialize(),
                 chace: false,
                 async: false,
                 success: function(data) {
                     $('#datatable').DataTable().ajax.reload();
                     $('#formmodal').modal('hide');
                     $('#panel_tambah').removeClass('offcanvas-on');
                     $('#overlay').removeClass('offcanvas-overlay');
                     $('#formmodal').modal('hide');

                     toastr.success('Data prospektif berhasil di simpan');


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
                         "<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Perahtian donk!</strong> " +
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
