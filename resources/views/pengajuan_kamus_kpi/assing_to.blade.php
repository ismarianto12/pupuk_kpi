 <div class="card">
     <div class="card-header">
         <h4><i class="fa fa-forward"></i>Forward Assing Pengajuan KPI </h4>
         <br />
     </div>
     <div class="card-body">
         <form class="simpan form-horizontal" method="POST" novalidate="">
             <input type="hidden" name="id" value="{{ $id }}" />
             <div class="form-group">
                 <div class="form-group row">
                     <label class="control-label col-md-4">
                         Bidang </label>

                     <div class="col-md-6">
                         <select class="form-control" name="tmunit_id" id="tmunit_id" required>
                             <option value="">--Semua Bidang--</option>
                             @foreach ($bidang as $bidangs)
                                 <option value="{{ $bidangs->id }}">
                                     {{ $bidangs->nama }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                 </div>
             </div>
             <div class="card-action">
                 <br />
                 <div class="row">
                     <div class="col-md-12">
                         <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>Assign To</button>
                         <button class="btn btn-warning" type="reset"><i
                                 class="fa fa-share fa-spin"></i>Batal</button>
                     </div>
                 </div>
             </div>

         </form>
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
     $(function() {
         $('.simpan').on('submit', function(e) {
             e.preventDefault();
             if ($(this)[0].checkValidity() === false) {

                 e.stopPropagation();
                 toastr.error('silahkan cek kembali field yang kosong');

             } else {
                 var datastring = $(this).serialize();
                 $.ajax({
                     url: "{{ route('korporat.assignto_save') }}",
                     method: "POST",
                     data: datastring,
                     cache: false,
                     beforeSend: function() {
                         toastr.info('Sedang proses penyimpanan data ...');
                     },
                     success: function(data) {
                         $('#datatable').DataTable().ajax.reload();
                         $('#formmodal').modal('hide');
                         $('#pengajuan').modal('hide');
                         toastr.success('data berhasil di simpan');

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
                         $('.ket').html(
                             "<div class='container'><div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong></strong> " +
                             respon.message + "<ol class='pl-3 m-0'>" + err +
                             "</ol></div></div>");

                         toastr.error('validasi form tidak cocok');

                     }
                 });
             }
         });

     });
 </script>
