<?php

namespace App\Http\Controllers;

use App\Models\tmp_surat;
use App\Models\Tmprogres_spk;
use App\Models\Tmproyek;
use App\Models\Tmrap;
use App\Models\Tmrspk;
use App\Models\Tmsurat_master;
use App\Models\Tr_surat_master;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tmspk;
use Illuminate\Support\Facades\DB;
use App\Helpers\Properti_app;
use App\Models\tmhistory_pengajuan_kamus;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $view;
    protected $request;
    protected $route;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->request = $request;
        $this->view    = '.home';
        $this->route   = 'home';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Welcome Page';
        return view($this->view . '.home');
    }
    public function pieData($par)
    {
        $data =  [
            'Closed BAK' => 'Closed BAK',
            'Closed PKS' => 'Closed PKS',
            'Closed PAID' => 'Closed PAID',
            'Closed (Catatan)' => 'Closed (Catatan)',
            'Negosiasi' => 'Negosiasi'
        ];

        $warna =  [
            'Closed BAK' => '#12391b',
            'Closed PKS' => '#176128',
            'Closed PAID' => '#378349',
            'Closed (Catatan)' => '#8bec8d',
            'Negosiasi' => '#ddd'
        ];

        if ($par == 'all') {
            foreach ($data as $datas) {
                $data = Tmsurat_master::where('status_perpanjangan', $datas)->count();

                if ($datas == 'Negosiasi') {
                    $rdata = 398;
                } else {
                    $rdata = $data;
                }
                $row[] = [
                    'name' => $datas . ': ' . $rdata . ' Site',
                    'y' => $rdata,
                    'color' => $warna[$datas]
                ];
            }
            $rs = isset($row) ? $row : [];
            return $rs;
        } else {
            $data = DB::table('ACHIEVEMENT_PERCENT')->get();
            $res =  substr($data[0]->TOTAL, 0, 5);
            return $res;
        }
    }


    public function dashboard()
    {
    }

    // requre api
    public function all_site()
    {
        $all_site = Tmsurat_master::get()->count();
        return response()->json_encode($all_site);
    }

    public function percetage_all_site($cat)
    {
        if ($cat == 1) {
            $jenis = 'EASTERN JABOTABEK';
        } else if ($cat == 2) {
            $jenis = 'CENTRAL JABOTABEK';
        } else if ($cat == 3) {
            $jenis = 'WESTERN JABOTABEK';
        }
        $data = Tmsurat_master::select(\DB::raw('count(id) as jumlahny'))->from('tmsurat_master')->where('region', $jenis)->count();
        return response()->json([
            'cat_data' => $data
        ]);
    }
    public function custom_number_format($n, $precision = 3)
    {
        if ($n < 1000000) {
            // Anything less than a million
            $n_format = number_format($n);
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision) . 'B';
        }

        return $n_format;
    }
    // show data from this page use this line
    public function table_data()
    {
        $jenis = $this->request->jenis;
        $back = Tmsurat_master::join('tr_surat_master', 'tr_surat_master.site', '=', 'tmsurat_master.site_id');
        $periode = $this->request->periode;
        if ($periode == '1') {
            $fperiode = '3';
        } else
        if ($periode == '2') {
            $fperiode = '6';
        } else
        if ($periode == '3') {
            $fperiode = '9';
        } else
        if ($periode == '4') {
            $fperiode = '12';
        } else {
            $fperiode = '12';
        }

        $y       = date('Y');
        if ($jenis == 'cos_saving') {
            if ($this->request->periode != 0) {

                $origin = Tmsurat_master::select(\DB::raw("sum(replace(harga_patokan,',','')) as fharga_patokan"))
                    ->join('tmp_surat', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id')
                    ->join('tr_surat_master', 'tr_surat_master.site_id', '=', 'tmsurat_master.site_id');

                $compare = tmp_surat::select(\DB::raw("sum(replace(harga_sewa_baru,'.','')) as tharga_sewa_baru"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id');
                if ($periode == '1') {
                    $origin->where("tmsurat_master.quartal", "Q1")->get();
                    $compare->where("tmsurat_master.quartal", "Q1")->get();
                } else
                if ($periode == '2') {
                    // origin start
                    $origin->where("tmsurat_master.quartal", "Q2")->get();
                    $compare->where("tmsurat_master.quartal", "Q2")->get();
                } else
                if ($periode == '3') {
                    $origin->where("tmsurat_master.quartal", "Q3")->get();
                    $compare->where("tmsurat_master.quartal", "Q3")->get();
                } else
                if ($periode == '4') {
                    $origin->where("tmsurat_master.quartal", "Q4")->get();
                    $compare->where("tmsurat_master.quartal", "Q4")->get();
                } else {
                    $origin->get();
                    $compare->get();
                }
                $c_saving       = ($origin->first()->fharga_patokan - $compare->first()->tharga_sewa_baru);
                $fharga_patokan = $origin->first()->fharga_patokan;
                if ($origin->first()->fharga_patokan < 0 || $origin->first()->fharga_patokan == null) {
                    $percetage      = 0;
                } else {
                    $percetage      = (($c_saving  / $fharga_patokan) *  100);
                }
                $amount         = number_format((int)$c_saving);
                $a = [
                    'percentage' => ($percetage) ? substr($percetage, 0, 5) . '%' : '0%',
                    'amount' => ($amount) ? $amount : 0,
                ];
            } else {
                $origin = Tmsurat_master::select(\DB::raw("sum(replace(harga_patokan,',','')) as fharga_patokan"))
                    ->join('tmp_surat', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id')
                    ->join('tr_surat_master', 'tr_surat_master.site_id', '=', 'tmsurat_master.site_id')->get();

                $compare = tmp_surat::select(\DB::raw("sum(replace(harga_sewa_baru,'.','')) as tharga_sewa_baru"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id')
                    ->get();

                // Show results of log
                if ($compare->first()->tharga_sewa_baru > 0) {
                    $c_saving   = ($origin->first()->fharga_patokan - $compare->first()->tharga_sewa_baru);
                    $amount     = number_format((int)$c_saving);
                    $percetage  = (($c_saving / (int)$origin->first()->fharga_patokan) * 100);
                    $setpercentage =  substr($percetage, 0, 5);
                } else {
                    $amount     = 0;
                    $setpercentage = 0;
                }


                $a = [
                    'percentage' => ($setpercentage) . '%',
                    'amount' => ($amount) ? $amount : 0,
                ];
            }
        } else if ($jenis == 'ef_eficiency') {

            $eorigin = tmp_surat::select(\DB::raw("sum(replace(pemilik_1,'.','')) as penawaran_ttl"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id');
            $ecompare = tmp_surat::select(\DB::raw("sum(replace(harga_sewa_baru,'.','')) as tharga_sewa_baru"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id');

            if ($periode == '1') {

                $eorigin->where("tmsurat_master.quartal", "Q1")->get();
                $ecompare->where("tmsurat_master.quartal", "Q1")->get();
            } else  if ($periode == '2') {

                $eorigin->where("tmsurat_master.quartal", "Q2")->get();
                $ecompare->where("tmsurat_master.quartal", "Q2")->get();
            } else if ($periode == '3') {

                $eorigin->where("tmsurat_master.quartal", "Q3")->get();
                $ecompare->where("tmsurat_master.quartal", "Q3")->get();
            } else if ($periode == '4') {
                $eorigin->where("tmsurat_master.quartal", "Q4")->get();
                $ecompare->where("tmsurat_master.quartal", "Q4")->get();
            } else {
                $eorigin->get();
                $ecompare->get();
            }

            $efficiency     = ((int)$eorigin->first()->penawaran_ttl - (int)$ecompare->first()->tharga_sewa_baru);
            $hargaperatama  = isset($eorigin->first()->penawaran_ttl) ? $eorigin->first()->penawaran_ttl : 1;
            if ($this->request->periode != 0) {
                $percetage  = (((int)$efficiency / (int)$hargaperatama) * 100);
                $amount     = number_format((int)$efficiency);
                $a          = [
                    'percentage' => ($percetage) ? substr($percetage, 0, 6) . '%' : '0%',
                    'amount' => ($amount) ? $amount : '0'
                ];
            } else {

                $eorigin = tmp_surat::select(\DB::raw("sum(replace(pemilik_1,'.','')) as penawaran_ttl"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id')
                    ->get();

                $compare = tmp_surat::select(\DB::raw("sum(replace(harga_sewa_baru,'.','')) as tharga_sewa_baru"))->join('tmsurat_master', 'tmp_surat.site_id', '=', 'tmsurat_master.site_id')
                    ->get();

                $c_saving  =  ((int)$eorigin->first()->penawaran_ttl - (int)$compare->first()->tharga_sewa_baru);
                $amount     = number_format($c_saving);
                if ($eorigin->first()->penawaran_ttl > 0) {

                    $percetage  = (($c_saving / $eorigin->first()->penawaran_ttl) * 100);
                } else {
                    $percetage  = 0;
                }
                $a = [
                    'percentage' => substr($percetage, 0, 6) . '%',
                    'amount' => ($amount) ? $amount : '0'
                ];
            }
        }

        return response()->json($a);
    }
    private function based_on_revenue()
    {
        $site_jabodetabek = Tmsurat_master::get()->count();
        return $site_jabodetabek;
    }

    function cregion($par)
    {

        $jumlah = Tr_surat_master::where('region', $par)
            ->count();

        $nil =  (intval($jumlah) / intval($this->based_on_revenue()) * 100);
        return ceil($nil) . '%';
    }
    function site_jabodetabek()
    {
        return $this->based_on_revenue();
    }
    function pr_western_jabo()
    {
        return $this->cregion('WESTERN JABOTABEK');
    }
    function pr_centeral_jabo()
    {
        return $this->cregion('CENTRAL JABOTABEK');
    }
    function pr_eastern_jabo()
    {
        return $this->cregion('EASTERN JABOTABEK');
    }
    // left grafik

    public function graph_revenue()
    {
        $ar_cat = [
            '#N/A',
            'BRONZE',
            'SILVER',
            'GOLD',
            'PLATINUM',
            'DIAMOND'
        ];

        foreach ($ar_cat as $lsdata) {
            $jumlah = Tr_surat_master::select(\DB::raw('count(id) as ll'))->where('revenue_cat', $lsdata)
                ->first();

            $nil =  (intval($jumlah->ll) / intval($this->based_on_revenue()) * 100);
            $passedata[] = ceil($nil);
        }
        $frpassed = implode(',', $passedata);
        return $frpassed;
    }
    // persentase perhitungan
    public function saving_filter()
    {
        $periode = $this->request->periode;
        if ($periode == 0) {
            $cost_saving = '';
            $cs_percentage = '';
            $cs_amount = '';


            $efiency = '';
            $ef_percentage = '';
            $ef_amount = '';
        } else {
            $data = Tmsurat_master::join('tr_surat_master', 'tr_surat_master.site_id', '=', 'tmsurat_master.site_id', 'left');

            if ($periode == 1) {
            } elseif ($periode == 2) {
            } else if ($periode == 3) {
            }



            $cost_saving = '';
            $cs_percentage = '';
            $cs_amount = '';


            $efiency = '';
            $ef_percentage = '';
            $ef_amount = '';
        }
    }

    // prooyeksi pengajuan kpi 


    public function proyeksi_pengajuan_kpi()
    { 
        $user_id = Auth::user()->id;
        $data = tmhistory_pengajuan_kamus::join('tmpengajuan_kamus_kpi','tmpengajuan_kamus_kpi.id','=','tmhistory_pengajuan_kamus.tmpengajuan_kamus_kpi_id')
                                           ->join('user.id','=','tmhistory_pengajuan_kamus.user_id') 
                                           ->where('user',$user_id)->get();
        return response()->json($data);
    }
}
