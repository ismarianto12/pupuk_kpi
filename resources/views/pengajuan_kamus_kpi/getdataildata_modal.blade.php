 <div class="card">
     <div class="card-header">
         <div class="detai_kpi_data_content"></div>
     </div>
     <div class="card-body">


         <form class="simpan form-horizontal" method="POST" novalidate="">
             <input type="hidden" name="id" value="{{ $id }}" />
             <input type="hidden" name="backurl" value="" />

             <div class="form-group">
                 <div class="form-group row">
                     <label class="control-label col-md-4">
                         Status Approval </label>
                     <div class="col-md-6">
                         <select class="form-control" name="status" id="status" required>
                             <option value="">--Status Approval--</option>
                             @foreach ($status as $val => $value)
                                 <option value="{{ $val }}">
                                     {{ $value }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                 </div>
                 <div class="form-group row">
                     <label class="control-label col-md-4">
                         Catatan Approval </label>
                     <div class="col-md-6">
                         <textarea class="form-control" name="catatan"></textarea>
                     </div>
                 </div>
             </div>
             <div class="card-action">
                 <br />
                 <div class="row">
                     <div class="col-md-12">
                         <a href="#" class="detaildata_get btn btn-pirmary" data-id="{{ $id }}"
                             class="btn btn-success">Detail
                             data Kpi</a>
                         <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>Simpan</button>
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

     jQuery(document).ready(function() {
         $('.detai_kpi_data_content').load("{{ Url('korporat/pengajuan_kamus_kpi/' . $id) }}");
         $('.simpan').on('submit', function(e) {
             e.preventDefault();
             if ($(this)[0].checkValidity() === false) {

                 e.stopPropagation();
                 toastr.error('silahkan cek kembali field yang kosong');

             } else {
                 var datastring = $(this).serialize();
                 $.ajax({
                     url: "{{ route('master.save_getdataildata_modal') }}",
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
                         $('#modal_notifikasi').modal('hide');
                         
                         toastr.success('data berhasil di simpan');

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
