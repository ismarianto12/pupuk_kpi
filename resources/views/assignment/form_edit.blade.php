 <div class="card">
     <div class="card-header">
         <h4 class="card-title"><i class="fa fa-plus"></i> {{ $nama_prospektif }}
         </h4>
         <label class="control-label col-md-6"></label>
     </div>
     <div class="ket"></div>



     <div class="card-body">
         <form id="exampleValidation" method="POST" class="simpan needs-validation" novalidate="">
             <input type="hidden" name="tmprospektif_id" value="{{ $tmprospektif_id }}" />
             <input type="hidden" name="tmprospektif_sub_id" value="{{ $tmprospektif_sub_id }}" />

             <div class="form-group row">
                 <label class="form-label"><b>Pilih Kamus Kpi</b></label>
                 <select class="form-control" name="tmkamus_kpi_id" id="tmkamus_kpi_id">
                     @foreach ($kamus as $kamuss)
                         <option value={{ $kamuss->id }}>{{ $kamuss->nama_kpi }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="form-group row">

                 <div class="col-md-3">
                     <label class="form-label">Sub</label>
                     <input type="text" name="sub" class="form-control" value="{{ $sub }}" />
                 </div>

                 <div class="col-md-3">
                     <label class="form-label">KPI</label>
                     <input type="text" name="kpi" class="form-control" value="{{ $kpi }}" />
                 </div>
                 <div class="col-md-3">
                     <label class="form-label">Total</label> 
                     <input type="text" name="total" class="form-control" value="{{ $total }}" />
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
             placeholder: "Pilih Kamus KPI"
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
                 $.ajax({
                     url: "{{ route('kamus.assingment.update', $id) }}",
                     method: "PUT",
                     data: $(this).serialize(),
                     chace: false,
                     async: false,
                     beforeSend: function() {
                         Swal.showLoading();
                     },
                     success: function(data) {
                         Swal.fire({
                             position: 'top-end',
                             icon: 'success',
                             title: 'Data berhasil di tambah',
                             showConfirmButton: false,
                             timer: 1500
                         });
                         $('#datatable').DataTable().ajax.reload();

                         toastr.success('data kamus berhasil di simpan');
                         $('#_xformmodal').modal('hide');

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
     });
 </script>
