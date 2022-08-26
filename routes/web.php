<?php
use App\Http\Controllers\Assignmentcontroller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RealisasiKpiController;
use App\Http\Controllers\ReportRealisasikpi;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\TmcabangpupukController;
use App\Http\Controllers\TmfrekuensiController;
use App\Http\Controllers\TmistilahKpiController;
use App\Http\Controllers\TmjabatanController;
use App\Http\Controllers\TmkamusKpiController;
use App\Http\Controllers\TmkamusKpiSubController;
use App\Http\Controllers\TmlevelController;
use App\Http\Controllers\TmpegawaiController;
use App\Http\Controllers\TmpengajuanKamusKpiController;
use App\Http\Controllers\TmpolaritasController;
use App\Http\Controllers\TmprospektifController;
use App\Http\Controllers\TmprospektifSubController;
use App\Http\Controllers\TmsatuanController;
use App\Http\Controllers\Tmsurat_masterController;
use App\Http\Controllers\TmtahunController;
use App\Http\Controllers\TmunitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PHPJasper\PHPJasper;

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Route::group(['middleware' => ['auth', 'delbastam', 'api']], function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/index.html', [App\Http\Controllers\HomeController::class, 'index'])->name('index.html');
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('profile', [UserController::class, 'profile'])->name('profile');
        Route::post('profilesave', [UserController::class, 'profile'])->name('profilesave');

    });
    Route::prefix('setting')->name('setting.')->group(function () {
        Route::resource('menu', MenuController::class);
        Route::post('menu_save_position', [MenuController::class, 'save_postion'])->name('menu_save_position');
    });
    Route::prefix('kamus')->name('kamus.')->group(function () {
        Route::get('assingment', [Assignmentcontroller::class, 'index'])->name('assingment');
        Route::get('load_unit/{id}', [Assignmentcontroller::class, 'load_unit'])->name('load_unit');
        Route::post('save_assingment', [Assignmentcontroller::class, 'save_assingment'])->name('save_assingment');

    });
    Route::prefix('master')->name('master.')->group(function () {
        route::get('jasper', function () {
            $options = [
                'format' => ['pdf'],
                'db_connection' => [
                    'driver' => 'mysql',
                    'username' => 'root',
                    'password' => '',
                    'host' => 'localhost',
                    'database' => 'pi_kpi',
                    'port' => env('DB_PORT'),
                ],
            ];
            $input = public_path('rian.jrxml');
            $ouput = public_path('file/rian');
            $jasper = new PHPJasper;
            $jasper->compile($input)->execute();
            $jasper->process(
                $input,
                $ouput,
                $options,

            )->execute();
            return response()->file($ouput . '.pdf')->deleteFileAfterSend();

        });
        Route::resource('pegawai', TmpegawaiController::class);
        Route::resource('unit', TmunitController::class);
        Route::resource('jabatan', TmjabatanController::class);
        Route::resource('jenis_surat', JenisSuratController::class);
        Route::resource('tmlevel', TmlevelController::class);
        Route::resource('tahun', TmtahunController::class);
        Route::resource('user', UserController::class);
        Route::resource('kamus', TmkamusKpiController::class);
        Route::resource('kamus_sub', TmkamusKpiSubController::class);
        Route::resource('prospektif', TmprospektifController::class);
        Route::resource('prospektif_sub', TmprospektifSubController::class);
        Route::resource('tmistilah_kpi', TmistilahKpiController::class);
        Route::resource('realiasi_kpi', RealisasiKpiController::class);
        Route::resource('anak_perusahaan', TmcabangpupukController::class);
        Route::resource('reportrealiasi', ReportRealisasikpi::class);
        Route::get('notif', [TmpengajuanKamusKpiController::class, 'notifikasi_perunit'])->name('notif');
        Route::post('getdataildata_modal', [TmpengajuanKamusKpiController::class, 'getdataildata_modal'])->name('getdataildata_modal');
        Route::post('save_getdataildata_modal', [TmpengajuanKamusKpiController::class, 'save_getdataildata_modal'])->name('save_getdataildata_modal');
        Route::post('notifikasi_data', [TmpengajuanKamusKpiController::class, 'notifikasi_data'])->name('notifikasi_data');
        Route::resource('satuan', TmsatuanController::class);
        Route::resource('frekuensi', TmfrekuensiController::class);
        Route::resource('polaritas', TmpolaritasController::class);
        Route::get('log', [SuratController::class, 'log'])->name('log');
    });
    Route::prefix('korporat')->name('korporat.')->group(function () {
        Route::resource('pengajuan_kamus_kpi', TmpengajuanKamusKpiController::class);
        Route::get('assignto/{id}', [TmpengajuanKamusKpiController::class, 'assignto'])->name('assignto');
        Route::get('assignto/{id}', [TmpengajuanKamusKpiController::class, 'assignto'])->name('assignto');
        Route::post('assignto_save', [TmpengajuanKamusKpiController::class, 'assignto_save'])->name('assignto_save');
        Route::post('simpan_data_kpi', [RealisasiKpiController::class, 'simpan_data_kpi'])->name('simpan_data_kpi');
    });
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('report_realisasi/{type}', [ReportRealisasikpi::class, 'report_realisasi'])->name('report_realisasi');
    });
    Route::get('view_download', [Tmsurat_masterController::class, 'view_download'])->name('view_download');
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('pegawai', [TmpegawaiController::class, 'api'])->name('pegawai');
        Route::get('unit', [TmunitController::class, 'api'])->name('unit');
        Route::post('tmlevel', [TmlevelController::class, 'api'])->name('tmlevel');
        Route::post('tmjabatan', [TmjabatanController::class, 'api'])->name('tmjabatan');
        Route::post('tahun', [TmtahunController::class, 'api'])->name('tahun');
        Route::post('user', [UserController::class, 'api'])->name('user');
        Route::post('kamus', [TmkamusKpiController::class, 'api'])->name('kamus');
        Route::post('kamus_sub', [TmkamusKpiSubController::class, 'api'])->name('subkamus');
        Route::post('prospektif', [TmprospektifController::class, 'api'])->name('prospektif');
        Route::post('prospektif_sub', [TmprospektifSubController::class, 'api'])->name('prospektif_sub');
        Route::post('cabang_perushaan', [TmcabangpupukController::class, 'api'])->name('cabang_perushaan');
        Route::post('reportrealisasi', [ReportRealisasikpi::class, 'api'])->name('reportrealisasi');
        Route::post('assingment', [Assignmentcontroller::class, 'api'])->name('assingment');
        Route::post('satuan', [TmsatuanController::class, 'api'])->name('satuan');
        Route::post('frekuensi', [TmfrekuensiController::class, 'api'])->name('frekuensi');
        Route::post('polaritas', [TmpolaritasController::class, 'api'])->name('polaritas');
        Route::post('tmistilah_kpi', [TmistilahKpiController::class, 'api'])->name('tmistilah_kpi');
        Route::post('pengajuan_kamus_kpi', [TmpengajuanKamusKpiController::class, 'api'])->name('pengajuan_kamus_kpi');
        Route::post('realiasi_kpi', [RealisasiKpiController::class, 'api'])->name('realiasi_kpi');

    });
    Route::prefix('kamus_api')->name('kamus_api.')->group(function () {
        Route::get('status_kamus/{status}', [TmkamusKpiController::class, 'status_kamu'])->name('status_kamu');
    });

    Route::prefix('assigmen')->name('assignmen.')->group(function(){
        Route::get('prospektif_parent',[TmprospektifController::class,'prospektif_parent'])->name('prospektif_parent');
        Route::get('subprospektif',[TmprospektifController::class,'prospektif_parent'])->name('prospektif_parent');
        Route::get('kamus_sub',[TmkamusKpiSubController::class,'kamus_sub'])->name('kamus_sub');

    });
    Route::post('jenis_show/{id}', [SuratController::class, 'carijenis'])->name('jenis_show');
    Route::prefix('dashboard_api')->name('dashboard_api.')->group(function () {
        Route::get('site_jabodetabek', [HomeController::class, 'site_jabodetabek'])->name('site_jabodetabek');
        Route::get('pr_western_jabo', [HomeController::class, 'pr_western_jabo'])->name('pr_western_jabo');
        Route::get('pr_centeral_jabo', [HomeController::class, 'pr_centeral_jabo'])->name('pr_centeral_jabo');
        Route::get('pr_eastern_jabo', [HomeController::class, 'pr_eastern_jabo'])->name('pr_eastern_jabo');

        Route::get('graph_revenue', [HomeController::class, 'graph_revenue'])->name('graph_revenue');
    });
});

Auth::routes();
