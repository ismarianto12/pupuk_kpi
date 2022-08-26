 <h3> <i class="fa fa-copy"></i>{{ ucfirst($nama_kpi) }}</h3>
 <br />

 <table class="table table-bordered">

     <tr>
         <th>Nama KPI</th>
         <td>{{ $nama_kpi }}</td>
     </tr>
     <tr>
         <th>Definisi KPI</th>
         <td>{{ $definisi }}</td>
     </tr>
     <tr>
         <th>Tujuan KPI</th>
         <td>{{ $tujuan }}</td>
     </tr>
     <tr>
         <th>Satuan yang higunakan</th>
         <td>{{ $nama_satuan }}</td>
     </tr>
     <tr>
         <th>Formula Penilaian</th>
         <td>
             <div style="width:50%">
                 @php echo $formula_penilaian @endphp
             </div>
         </td>
     </tr>
     <tr>
         <th>Target</th>
         <td>{{ $target }}</td>
     </tr>
     <tr>
         <th>Frekuensi</th>
         <td>{{ $nama_frekuensi }}</td>
     </tr>
     <tr>
         <th>Polaritas</th>
         <td>{{ $nama_polaritas }}</td>
     </tr>
     <tr>
         <th>Unit Pemilik KPI</th>
         <td>{{ $unit_pemilik_kpi }}</td>
     </tr>
     <tr>
         <th>Unit Pengelola KPI</th>
         <td>Unit Pengelola KPI</td>
     </tr>
     <tr>
         <th>Sumber Data</th>
         <td>{{ $sumber_data }}</td>
     </tr>
     <tr>
         <th>Jenis Pengukuran Data</th>
         <th>{{ $pengukuran_ll }}</th>
     </tr>


 </table>
 <br />

 Notes :

 {{ $catatan }}
