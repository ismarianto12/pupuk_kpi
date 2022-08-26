 <div class="card-title">{{ _('Tambah data master tahun') }}</div>
 <div class="ket"></div>
 <form id="exampleValidation" method="POST" class="simpan needs-validation" novalidate="">

     <label for="name">Kode<span class="required-label">*</span></label>
     <input type="text" class="form-control" id="kode" name="kode" required>
     <label for="name">Tahun <span class="required-label">*</span></label>
     <input type="number" maxlength="4" class="form-control" id="tahun" name="tahun" required>
     <label for="name">Status Active <span class="required-label">*</span></label>

     <select class="form-control" name="active">
         @foreach (['1' => 'Active', '2' => 'Non active'] as $act => $ac)
             <option value="{{ $act }}">{{ $ac }}</option>
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
                 url: "{{ route('master.tahun.store') }}",
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

                     toastr.success('data pegawai berhasil di simpan');


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
