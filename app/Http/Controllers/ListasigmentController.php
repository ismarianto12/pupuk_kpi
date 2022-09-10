<?php

namespace App\Http\Controllers;

use App\Helpers\Properti_app;
use App\Models\tmkamus_kpi;
use App\Models\tmprospektif;
use App\Models\tmprospektif_sub;
use App\Models\tmtable_assigment;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PDF;

class ListasigmentController extends Controller
{

    protected $request;
    protected $route;
    protected $view;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.list_assignment.';
        $this->route = 'assigment.list_assignment';
    }

    public function index()
    {
        $unit = tmunit::get();
        $tahun = tmtahun::get();
        return view($this->view . 'index', compact('tahun', 'unit'));

    }

    public function getdatakamus_list_assigment()
    {
        if ($this->request->ajax()) {
            $id = $this->request->id_kpi;
            // $data = tmkamus_kpi::wherep
            $data = tmkamus_kpi::select(
                'tmkamus_kpi.id as idnya',
                'tmkamus_kpi.nama_kpi',
                'tmkamus_kpi.definisi',
                'tmkamus_kpi.tujuan',
                'tmkamus_kpi.tmsatuan_id',
                'tmkamus_kpi.formula_penilaian',
                'tmkamus_kpi.target',
                'tmkamus_kpi.tmfrekuensi_id',
                'tmkamus_kpi.tmpolaritas_id',
                'tmkamus_kpi.unit_pemilik_kpi',
                'tmkamus_kpi.unit_pengelola_kpi',
                'tmkamus_kpi.sumber_data',
                'tmkamus_kpi.jenis_pengukuran',
                'tmkamus_kpi.catatan',
                'tmfrekuensi.nama_frekuensi',
                'tmfrekuensi.kode',
                'tmpolaritas.kode',
                'tmpolaritas.nama_polaritas',
                'tmsatuan.kode',
                'tmsatuan.nama_satuan',
                'tmtahun.tahun',
                'tmkamus_kpi.created_at',
                'tmkamus_kpi.updated_at',
                'tmunit.nama as nama_unit',
                \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll')
            )
                ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
                ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
                ->where('tmkamus_kpi.id', $id)->firstOrFail();
            return response()->json($data);
        }
    }

    public function api()
    {
        $tmtahun_id = $this->request->tmtahun_id;
        $tmunit_id = $this->request->tmunit_id;
        $batch = $this->request->batch;

        $data = tmtable_assigment::distinct()->select(
            'tmunit.unit_kode',
            'tmunit.nama as nama_unit',
            'tmtable_assigment.id',
            'tmtable_assigment.batch',
            'tmtable_assigment.tmunit_id',
            'tmtahun.id',
            'tmtahun.tahun',
            'tmtable_assigment.tmtahun_id',
            'users.name'
        )
            ->join('tmunit', 'tmunit.id', '=', 'tmtable_assigment.tmunit_id', 'left')
            ->join('users', 'users.id', '=', 'tmtable_assigment.user_id', 'left')
            ->join('tmtahun', 'tmtahun.id', '=', 'tmtable_assigment.tmtahun_id', 'left')
            ->groupBy('tmtable_assigment.batch');
        if ($tmtahun_id != '') {
            $data->where('tmtable_assigment.tmtahun_id', $tmtahun_id);

        }
        if ($tmunit_id != '') {
            $data->where('tmtable_assigment.tmunit_id', $tmunit_id);

        }
        if ($batch != '') {
            $data->where('tmtable_assigment.batch', $batch);

        }

        $sql = $data->get();

        return DataTables::of($sql)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->tmtahun_id . "' />";
            })
            ->editColumn('action', function ($p) {

                $batch = $p->batch;
                $tmunit_id = $p->tmunit_id;
                $tmtahun_id = $p->tmtahun_id;

                return '
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" id="edit" title="edit data assigment" data-batch="' . $batch . '" data-tmunit_id="' . $tmunit_id . '" data-tmtahun_id="' . $tmtahun_id . '" class="btn btn-icon btn-warning btn-sm me-1"><i class="fa fa-edit"></i></a>
                <a href="#" id="view" title="view data assigment" data-batch="' . $batch . '" data-tmunit_id="' . $tmunit_id . '" data-tmtahun_id="' . $tmtahun_id . '" class="btn btn-icon btn-success btn-sm me-1"><i class="fa fa-eye"></i></a>
                    </div>
                ';
            }, true)
            ->editColumn('created_by', function ($p) {
                return $p->name;
            }, true)
            ->addIndexColumn()
            ->rawColumns(['created_by', 'action', 'id'])
            ->toJson();
    }
    public function create()
    {

        $prospektif = tmprospektif::get();
        $idx = 0;
        foreach ($prospektif as $prospektiff) {
            $dataset[$idx]['id']['val'] = $prospektiff->id;
            $dataset[$idx]['status']['val'] = 'prospektiff';
            $dataset[$idx]['parent']['val'] = true;
            $dataset[$idx]['nama_kpi']['val'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
            $dataset[$idx]['tmprospektif_id']['val'] = $prospektiff->id;
            $dataset[$idx]['tmprospektif_sub_id']['val'] = 0;
            // $dataset[$idx]['input']['val'] = '<input type="hidden" name="tmprospektif_id[]" value="' . $prospektiff->id . '" />';
            $dataset[$idx]['nama_satuan']['val'] = '';
            $dataset[$idx]['target']['val'] = '';
            $dataset[$idx]['nama_polaritas']['val'] = '';
            $dataset[$idx]['sub']['val'] = '';
            $dataset[$idx]['kpi']['val'] = '';
            $dataset[$idx]['total']['val'] = '';
            $c = 1;
            $idx++;
            $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
            foreach ($tmprospektif_sub as $tmprospektif_subs) {
                $dataset[$idx]['id']['val'] = $tmprospektif_subs->id;
                $dataset[$idx]['status']['val'] = 'subprospektiff';
                $dataset[$idx]['parent']['val'] = true;
                $dataset[$idx]['nama_kpi']['val'] = '<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                // $dataset[$idx]['input']['val'] = '<input type="hidden" name="tmprospektif_sub_id[]" value="' . $tmprospektif_subs->id . '" />';
                $dataset[$idx]['tmprospektif_id']['val'] = 0;
                $dataset[$idx]['tmprospektif_sub_id']['val'] = $tmprospektif_subs->id;
                $dataset[$idx]['nama_satuan']['val'] = '';
                $dataset[$idx]['target']['val'] = '';
                $dataset[$idx]['nama_polaritas']['val'] = '';
                $dataset[$idx]['sub']['val'] = '';
                $dataset[$idx]['kpi']['val'] = '';
                $dataset[$idx]['total ']['val'] = '';
                $c++;
                $idx++;
            }

            $idx++;
        }
        return view($this->view . 'form_add', [
            'render' => $dataset,
            'unit' => tmunit::get(),
            'tahun' => tmtahun::get(),
            'tmkamus_kpi' => tmkamus_kpi::get(),
        ]);

    }

    function print($id) {

        $data = tmtable_assigment::where('tmunit_id', $id)
            ->join('tmtahun', 'tmtahun.id', '=', 'tmtable_assigment.tmtahun_id', 'left')
            ->join('tmunit', 'tmunit.id', '=', 'tmtable_assigment.tmunit_id', 'left')->first();

        $unit_kerja = $data->nama;
        $tahun = $data->tahun;
        $prospektif = tmprospektif::get();
        $idx = 0;
        foreach ($prospektif as $prospektiff) {
            $dataset[$idx]['id']['val'] = $prospektiff->id;
            $dataset[$idx]['status']['val'] = 'prospektiff';
            $dataset[$idx]['parent']['val'] = true;
            $dataset[$idx]['nama_kpi']['val'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
            $dataset[$idx]['tmprospektif_id']['val'] = $prospektiff->id;
            $dataset[$idx]['tmprospektif_sub_id']['val'] = 0;
            $dataset[$idx]['nama_satuan']['val'] = '';
            $dataset[$idx]['target']['val'] = '';
            $dataset[$idx]['nama_polaritas']['val'] = '';
            $dataset[$idx]['sub']['val'] = '';
            $dataset[$idx]['kpi']['val'] = '';
            $dataset[$idx]['total']['val'] = '';
            $c = 1;
            $idx++;

            $fkamus = tmtable_assigment::select(

                'tmkamus_kpi.id',
                'tmkamus_kpi.nama_kpi',
                'tmkamus_kpi.definisi',
                'tmkamus_kpi.tujuan',
                'tmkamus_kpi.tmsatuan_id',
                'tmkamus_kpi.formula_penilaian',
                'tmkamus_kpi.target',
                'tmkamus_kpi.tmfrekuensi_id',
                'tmkamus_kpi.tmpolaritas_id',
                'tmkamus_kpi.unit_pemilik_kpi',
                'tmkamus_kpi.unit_pengelola_kpi',
                'tmkamus_kpi.sumber_data',
                'tmkamus_kpi.jenis_pengukuran',
                'tmkamus_kpi.catatan',
                'tmfrekuensi.nama_frekuensi',
                'tmfrekuensi.kode',
                'tmpolaritas.kode',
                'tmpolaritas.nama_polaritas',
                'tmsatuan.kode',
                'tmsatuan.nama_satuan',
                'tmtahun.tahun',
                'tmkamus_kpi.created_at',
                'tmkamus_kpi.updated_at',
                'tmunit.nama as nama_unit',
                \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                'tmtable_assigment.sub',
                'tmtable_assigment.kpi',
                'tmtable_assigment.total'
            )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')

                ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id')->where('tmtable_assigment.tmprospektif_id', $prospektiff->id)->get();
            $idx++;

            $a = 1;
            foreach ($fkamus as $klamuss) {
                $dataset[$idx]['id'] = $klamuss->id;
                $dataset[$idx]['status'] = '';
                $dataset[$idx]['tmprospektif_id']['val'] = 0;
                $dataset[$idx]['tmprospektif_sub_id']['val'] = '';
                $dataset[$idx]['parent']['val'] = false;
                $dataset[$idx]['nama_kpi']['val'] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $klamuss->nama_kpi;
                $dataset[$idx]['nama_satuan']['val'] = $klamuss->nama_satuan;
                $dataset[$idx]['target']['val'] = $klamuss->target;
                $dataset[$idx]['nama_polaritas']['val'] = $klamuss->nama_polaritas;
                $dataset[$idx]['sub']['val'] = $klamuss->sub;
                $dataset[$idx]['kpi']['val'] = $klamuss->kpi;
                $dataset[$idx]['total']['val'] = $klamuss->total;
                $dataset[$idx]['action']['val'] = '';

                $idx++;
                $a++;
            }

            $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
            foreach ($tmprospektif_sub as $tmprospektif_subs) {
                $dataset[$idx]['id']['val'] = $tmprospektif_subs->id;
                $dataset[$idx]['status']['val'] = 'subprospektiff';
                $dataset[$idx]['parent']['val'] = true;
                $dataset[$idx]['nama_kpi']['val'] = '<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                $dataset[$idx]['tmprospektif_id']['val'] = 0;
                $dataset[$idx]['tmprospektif_sub_id']['val'] = $tmprospektif_subs->id;
                $dataset[$idx]['nama_satuan']['val'] = '';
                $dataset[$idx]['target']['val'] = '';
                $dataset[$idx]['nama_polaritas']['val'] = '';
                $dataset[$idx]['sub']['val'] = '';
                $dataset[$idx]['kpi']['val'] = '';
                $dataset[$idx]['total ']['val'] = '';
                $c++;
                $idx++;
                $kamus = tmtable_assigment::select(
                    'tmkamus_kpi.id',
                    'tmkamus_kpi.nama_kpi',
                    'tmkamus_kpi.definisi',
                    'tmkamus_kpi.tujuan',
                    'tmkamus_kpi.tmsatuan_id',
                    'tmkamus_kpi.formula_penilaian',
                    'tmkamus_kpi.target',
                    'tmkamus_kpi.tmfrekuensi_id',
                    'tmkamus_kpi.tmpolaritas_id',
                    'tmkamus_kpi.unit_pemilik_kpi',
                    'tmkamus_kpi.unit_pengelola_kpi',
                    'tmkamus_kpi.sumber_data',
                    'tmkamus_kpi.jenis_pengukuran',
                    'tmkamus_kpi.catatan',
                    'tmfrekuensi.nama_frekuensi',
                    'tmfrekuensi.kode',
                    'tmpolaritas.kode',
                    'tmpolaritas.nama_polaritas',
                    'tmsatuan.kode',
                    'tmsatuan.nama_satuan',
                    'tmtahun.tahun',
                    'tmkamus_kpi.created_at',
                    'tmkamus_kpi.updated_at',
                    'tmunit.nama as nama_unit',
                    \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                    'tmtable_assigment.sub',
                    'tmtable_assigment.kpi',
                    'tmtable_assigment.total'
                )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')
                    ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                    ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                    ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                    ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                    ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
                    ->where('tmtable_assigment.tmprospektif_sub_id', $tmprospektif_subs->id)->get();
                $d = 1;
                foreach ($kamus as $kamuss) {
                    $dataset[$idx]['id']['val'] = $kamuss->id;
                    $dataset[$idx]['status']['val'] = '';
                    $dataset[$idx]['parent']['val'] = false;
                    $dataset[$idx]['nama_kpi']['val'] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $kamuss->nama_kpi;
                    $dataset[$idx]['nama_satuan']['val'] = $kamuss->nama_satuan;
                    $dataset[$idx]['target']['val'] = $kamuss->target;
                    $dataset[$idx]['nama_polaritas']['val'] = $kamuss->nama_polaritas;
                    $dataset[$idx]['sub']['val'] = $kamuss->sub;
                    $dataset[$idx]['kpi']['val'] = $kamuss->kpi;
                    $dataset[$idx]['total']['val'] = $kamuss->total;
                    $dataset[$idx]['tmprospektif_id']['val'] = 0;
                    $dataset[$idx]['tmprospektif_sub_id']['val'] = '';

                    $dataset[$idx]['action'] = '';

                    $idx++;
                    $d = 1;
                }

            }
            $idx++;
        }

        $customPaper = array(0, 0, 567.00, 283.80);
        $pdf = PDF::loadView(
            $this->view . 'pdf_show', [
                'render' => $dataset,
                'unit_kerja' => $unit_kerja,
                'tahun' => $tahun,
                'id' => $id,
                'tmkamus_kpi' => tmkamus_kpi::get(),
            ]
        )->setPaper('A4', 'landscape');
        return $pdf->stream('Detail_assignment_kamus_kpi.pdf');
    }
    private function AddToArray($post_information)
    {
        $return = array();

        foreach ($post_information as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }
    public function store()
    {
        $jdata = count($this->request->tmkamus_kpi_id);
        $tmtahun_id = $this->request->tmtahun_id;
        $tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
        $tmprospektif_id = $this->request->tmprospektif_id;
        $tmprospektif_sub_id = $this->request->tmprospektif_sub_id;
        $tmunit_id = $this->request->tmunit_id;
        $catatan = $this->request->catatan;
        $user_id = $this->request->user_id;
        $status = $this->request->status;
        $sub = $this->request->sub;
        $kpi = $this->request->kpi;
        $total = $this->request->total;
        $batch = $this->request->batch;
        $min_sub = min($this->AddToArray($sub));
        $max_sub = max($this->AddToArray($sub));
        $t_total = array_sum($total);
        if ($t_total == 0) {
            return response()->json([
                'status' => 2,
                'msg' => 'Gagal target tidak boleh sama denga 0',
            ]);
        } else {

            $target = ($max_sub - $min_sub) / (int) $t_total * 100;
            if ($target > 20) {

                return response()->json([
                    'status' => 2,
                    'msg' => 'Tidak sesuai dengan ketentuan persentase melebih  20% Result : ' . $target,
                ]);

            } else {
                for ($i = 0; $i < $jdata; $i++) {
                    $data = new tmtable_assigment();
                    $data->tmtahun_id = Properti_app::getTahunActive();
                    $data->tmkamus_kpi_id = $tmkamus_kpi_id[$i];
                    $data->tmprospektif_id = $tmprospektif_id[$i];
                    $data->tmprospektif_sub_id = $tmprospektif_sub_id[$i];
                    $data->tmunit_id = $tmunit_id;
                    $data->tmtahun_id = $tmtahun_id;
                    $data->user_id = Auth::user()->id;
                    $data->status = 1;
                    $data->sub = $sub[$i];
                    $data->kpi = $kpi[$i];
                    $data->total = $total[$i];
                    $data->batch = Properti_app::batch();
                    $data->save();

                }
            }
            return response()->json([

                'status' => 1,
                'msg' => 'Sesuai dengan ketentuan nilai',

            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($this->request->ajax()) {

            $tmtahun_id = $this->request->tmtahun_id;
            $tmunit_id = $this->request->tmunit_id;
            $batch = $this->request->batch;

            if ($tmtahun_id != '' && $tmunit_id != '' && $batch != '') {
                $data = tmtable_assigment::where('tmunit_id', $id)
                    ->join('tmtahun', 'tmtahun.id', '=', 'tmtable_assigment.tmtahun_id', 'left')
                    ->join('tmunit', 'tmunit.id', '=', 'tmtable_assigment.tmunit_id', 'left')->firstOrfail();
                $unit_kerja = $data->nama;
                $tahun = $data->tahun;
                $prospektif = tmprospektif::get();
                $idx = 0;
                foreach ($prospektif as $prospektiff) {
                    $dataset[$idx]['id']['val'] = $prospektiff->id;
                    $dataset[$idx]['status']['val'] = 'prospektiff';
                    $dataset[$idx]['parent']['val'] = true;
                    $dataset[$idx]['nama_kpi']['val'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
                    $dataset[$idx]['tmprospektif_id']['val'] = $prospektiff->id;
                    $dataset[$idx]['tmprospektif_sub_id']['val'] = 0;
                    $dataset[$idx]['nama_satuan']['val'] = '';
                    $dataset[$idx]['target']['val'] = '';
                    $dataset[$idx]['nama_polaritas']['val'] = '';
                    $dataset[$idx]['sub']['val'] = '';
                    $dataset[$idx]['kpi']['val'] = '';
                    $dataset[$idx]['total']['val'] = '';
                    $c = 1;
                    $idx++;

                    $fkamus = tmtable_assigment::select(

                        'tmkamus_kpi.id',
                        'tmkamus_kpi.nama_kpi',
                        'tmkamus_kpi.definisi',
                        'tmkamus_kpi.tujuan',
                        'tmkamus_kpi.tmsatuan_id',
                        'tmkamus_kpi.formula_penilaian',
                        'tmkamus_kpi.target',
                        'tmkamus_kpi.tmfrekuensi_id',
                        'tmkamus_kpi.tmpolaritas_id',
                        'tmkamus_kpi.unit_pemilik_kpi',
                        'tmkamus_kpi.unit_pengelola_kpi',
                        'tmkamus_kpi.sumber_data',
                        'tmkamus_kpi.jenis_pengukuran',
                        'tmkamus_kpi.catatan',
                        'tmfrekuensi.nama_frekuensi',
                        'tmfrekuensi.kode',
                        'tmpolaritas.kode',
                        'tmpolaritas.nama_polaritas',
                        'tmsatuan.kode',
                        'tmsatuan.nama_satuan',
                        'tmtahun.tahun',
                        'tmkamus_kpi.created_at',
                        'tmkamus_kpi.updated_at',
                        'tmunit.nama as nama_unit',
                        \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                        'tmtable_assigment.sub',
                        'tmtable_assigment.kpi',
                        'tmtable_assigment.total'
                    )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')

                        ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                        ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                        ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                        ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                        ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id')->where('tmtable_assigment.tmprospektif_id', $prospektiff->id)->get();
                    $idx++;

                    $a = 1;
                    foreach ($fkamus as $klamuss) {
                        $dataset[$idx]['id'] = $klamuss->id;
                        $dataset[$idx]['status'] = '';
                        $dataset[$idx]['tmprospektif_id']['val'] = 0;
                        $dataset[$idx]['tmprospektif_sub_id']['val'] = '';
                        $dataset[$idx]['parent']['val'] = false;
                        $dataset[$idx]['nama_kpi']['val'] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $klamuss->nama_kpi;
                        $dataset[$idx]['nama_satuan']['val'] = $klamuss->nama_satuan;
                        $dataset[$idx]['target']['val'] = $klamuss->target;
                        $dataset[$idx]['nama_polaritas']['val'] = $klamuss->nama_polaritas;
                        $dataset[$idx]['sub']['val'] = $klamuss->sub;
                        $dataset[$idx]['kpi']['val'] = $klamuss->kpi;
                        $dataset[$idx]['total']['val'] = $klamuss->total;
                        $dataset[$idx]['action']['val'] = '';

                        $idx++;
                        $a++;
                    }

                    $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
                    foreach ($tmprospektif_sub as $tmprospektif_subs) {
                        $dataset[$idx]['id']['val'] = $tmprospektif_subs->id;
                        $dataset[$idx]['status']['val'] = 'subprospektiff';
                        $dataset[$idx]['parent']['val'] = true;
                        $dataset[$idx]['nama_kpi']['val'] = '<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                        $dataset[$idx]['tmprospektif_id']['val'] = 0;
                        $dataset[$idx]['tmprospektif_sub_id']['val'] = $tmprospektif_subs->id;
                        $dataset[$idx]['nama_satuan']['val'] = '';
                        $dataset[$idx]['target']['val'] = '';
                        $dataset[$idx]['nama_polaritas']['val'] = '';
                        $dataset[$idx]['sub']['val'] = '';
                        $dataset[$idx]['kpi']['val'] = '';
                        $dataset[$idx]['total ']['val'] = '';
                        $c++;
                        $idx++;
                        $kamus = tmtable_assigment::select(
                            'tmkamus_kpi.id',
                            'tmkamus_kpi.nama_kpi',
                            'tmkamus_kpi.definisi',
                            'tmkamus_kpi.tujuan',
                            'tmkamus_kpi.tmsatuan_id',
                            'tmkamus_kpi.formula_penilaian',
                            'tmkamus_kpi.target',
                            'tmkamus_kpi.tmfrekuensi_id',
                            'tmkamus_kpi.tmpolaritas_id',
                            'tmkamus_kpi.unit_pemilik_kpi',
                            'tmkamus_kpi.unit_pengelola_kpi',
                            'tmkamus_kpi.sumber_data',
                            'tmkamus_kpi.jenis_pengukuran',
                            'tmkamus_kpi.catatan',
                            'tmfrekuensi.nama_frekuensi',
                            'tmfrekuensi.kode',
                            'tmpolaritas.kode',
                            'tmpolaritas.nama_polaritas',
                            'tmsatuan.kode',
                            'tmsatuan.nama_satuan',
                            'tmtahun.tahun',
                            'tmkamus_kpi.created_at',
                            'tmkamus_kpi.updated_at',
                            'tmunit.nama as nama_unit',
                            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                            'tmtable_assigment.sub',
                            'tmtable_assigment.kpi',
                            'tmtable_assigment.total'
                        )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')
                            ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                            ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                            ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
                            ->where('tmtable_assigment.tmprospektif_sub_id', $tmprospektif_subs->id)->get();
                        $d = 1;
                        foreach ($kamus as $kamuss) {
                            $dataset[$idx]['id']['val'] = $kamuss->id;
                            $dataset[$idx]['status']['val'] = '';
                            $dataset[$idx]['parent']['val'] = false;
                            $dataset[$idx]['nama_kpi']['val'] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $kamuss->nama_kpi;
                            $dataset[$idx]['nama_satuan']['val'] = $kamuss->nama_satuan;
                            $dataset[$idx]['target']['val'] = $kamuss->target;
                            $dataset[$idx]['nama_polaritas']['val'] = $kamuss->nama_polaritas;
                            $dataset[$idx]['sub']['val'] = $kamuss->sub;
                            $dataset[$idx]['kpi']['val'] = $kamuss->kpi;
                            $dataset[$idx]['total']['val'] = $kamuss->total;
                            $dataset[$idx]['tmprospektif_id']['val'] = 0;
                            $dataset[$idx]['tmprospektif_sub_id']['val'] = '';

                            $dataset[$idx]['action'] = '';

                            $idx++;
                            $d = 1;
                        }

                    }
                    $idx++;
                }

                return view($this->view . 'show', [
                    'render' => $dataset,
                    'unit' => tmunit::get(),
                    'tahun' => tmtahun::get(),
                    'tmkamus_kpi' => tmkamus_kpi::get(),
                    'unit_kerja' => $unit_kerja,
                    'tahun' => $tahun,
                    'id' => $id,
                ]);
            } else {
                return '<img src="' . asset('assets/img/404.jpg') . '" /> <br /> Parameter Missing';
            }
        } else {
            return response()->json([
                'res' => 'data tidak dapat di load',
            ], 500);
        }
    }
    protected function parentIdassign($tmprospektif_id, $tmprospektif_sub_id)
    {
        return ' <input type="hidden" name="tmprospektif_id[]" value="' . $tmprospektif_id . '" />
        <input type="hidden" name="tmprospektif_sub_id[]" value="' . $tmprospektif_sub_id . '" />
       ';
    }

    protected function listProspektif($id)
    {
        $data = tmkamus_kpi::get();
        $a = '<select class="form-control" name="tmkamus_kpi_id[]" onChange="javascript:cari_kamus($(this).val(),${n},${par})">';
        foreach ($data as $datas) {
            $selected = ($id == $datas->id) ? 'selected' : '';
            $a .= '<option value="' . $datas->id . '" ' . $selected . '>' . $datas->nama_kpi . '</option>';
        }
        $a .= '</select>';
        return $a;
    }
    public function chilProspective($name, $value)
    {
        $components = '

        <input type="text" name="' . $name . '[]" value="' . $value . '" class="form-control">';
        return $components;
    }

    public function edit($id)
    {

        $tmtahun_id = $this->request->tmtahun_id;
        $tmunit_id = $this->request->tmunit_id;
        $batch = $this->request->batch;
        if ($tmtahun_id != '' && $tmunit_id != '' && $batch != '') {

            $prospektif = tmprospektif::get();
            $idx = 0;
            foreach ($prospektif as $prospektiff) {
                $dataset[$idx]['id']['val'] = $prospektiff->id;
                $dataset[$idx]['status']['val'] = 'prospektiff';
                $dataset[$idx]['parent']['val'] = true;
                $dataset[$idx]['nama_kpi']['val'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
                $dataset[$idx]['tmprospektif_id']['val'] = $prospektiff->id;
                $dataset[$idx]['asign_id']['val'] = '';
                $dataset[$idx]['tmprospektif_sub_id']['val'] = 0;
                $dataset[$idx]['nama_satuan']['val'] = '';
                $dataset[$idx]['target']['val'] = '';
                $dataset[$idx]['nama_polaritas']['val'] = '';
                $dataset[$idx]['sub']['val'] = '';
                $dataset[$idx]['kpi']['val'] = '';
                $dataset[$idx]['total']['val'] = '';
                $c = 1;
                $idx++;

                $fkamus = tmtable_assigment::select(

                    'tmkamus_kpi.id',
                    'tmkamus_kpi.nama_kpi',
                    'tmkamus_kpi.definisi',
                    'tmkamus_kpi.tujuan',
                    'tmkamus_kpi.tmsatuan_id',
                    'tmkamus_kpi.formula_penilaian',
                    'tmkamus_kpi.target',
                    'tmkamus_kpi.tmfrekuensi_id',
                    'tmkamus_kpi.tmpolaritas_id',
                    'tmkamus_kpi.unit_pemilik_kpi',
                    'tmkamus_kpi.unit_pengelola_kpi',
                    'tmkamus_kpi.sumber_data',
                    'tmkamus_kpi.jenis_pengukuran',
                    'tmkamus_kpi.catatan',
                    'tmfrekuensi.nama_frekuensi',
                    'tmfrekuensi.kode',
                    'tmpolaritas.kode',
                    'tmpolaritas.nama_polaritas',
                    'tmsatuan.kode',
                    'tmsatuan.nama_satuan',
                    'tmtahun.tahun',
                    'tmkamus_kpi.created_at',
                    'tmkamus_kpi.updated_at',
                    'tmunit.nama as nama_unit',
                    \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                    'tmtable_assigment.id as idassign',
                    'tmtable_assigment.sub',
                    'tmtable_assigment.kpi',
                    'tmtable_assigment.total',
                    'tmtable_assigment.tmprospektif_id',
                    'tmtable_assigment.tmprospektif_sub_id'

                )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')

                    ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                    ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                    ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                    ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                    ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id')->where('tmtable_assigment.tmprospektif_id', $prospektiff->id)->get();
                $idx++;

                $a = 1;
                foreach ($fkamus as $klamuss) {
                    $dataset[$idx]['id'] = $klamuss->id;
                    $dataset[$idx]['status'] = '';
                    $dataset[$idx]['tmprospektif_id']['val'] = 0;
                    $dataset[$idx]['tmprospektif_sub_id']['val'] = '';
                    $dataset[$idx]['parent']['val'] = false;
                    $dataset[$idx]['nama_kpi']['val'] = $this->parentIdassign(
                        $klamuss->tmprospektif_id,
                        $klamuss->tmprospektif_sub_id,
                    ) . $this->listProspektif(
                        $klamuss->id
                    );
                    $dataset[$idx]['asign_id']['val'] = $klamuss->idassign;

                    $dataset[$idx]['nama_satuan']['val'] = $this->chilProspective('nama_satuan', $klamuss->nama_satuan);
                    $dataset[$idx]['target']['val'] = $this->chilProspective('target', $klamuss->target);
                    $dataset[$idx]['nama_polaritas']['val'] = $this->chilProspective('nama_polaritas', $klamuss->nama_polaritas);
                    $dataset[$idx]['sub']['val'] = $this->chilProspective('sub', $klamuss->sub);
                    $dataset[$idx]['kpi']['val'] = $this->chilProspective('kpi', $klamuss->kpi);
                    $dataset[$idx]['total']['val'] = $this->chilProspective('total', $klamuss->total);
                    $dataset[$idx]['action']['val'] = '';
                    $idx++;
                    $a++;
                }

                $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
                foreach ($tmprospektif_sub as $tmprospektif_subs) {
                    $dataset[$idx]['id']['val'] = $tmprospektif_subs->id;
                    $dataset[$idx]['status']['val'] = 'subprospektiff';
                    $dataset[$idx]['parent']['val'] = true;
                    $dataset[$idx]['nama_kpi']['val'] = '<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                    $dataset[$idx]['tmprospektif_id']['val'] = 0;
                    $dataset[$idx]['tmprospektif_sub_id']['val'] = $tmprospektif_subs->id;
                    $dataset[$idx]['nama_satuan']['val'] = '';
                    $dataset[$idx]['asign_id']['val'] = 0;
                    $dataset[$idx]['target']['val'] = '';
                    $dataset[$idx]['nama_polaritas']['val'] = '';
                    $dataset[$idx]['sub']['val'] = '';
                    $dataset[$idx]['kpi']['val'] = '';
                    $dataset[$idx]['total ']['val'] = '';
                    $c++;
                    $idx++;
                    $kamus = tmtable_assigment::select(
                        'tmkamus_kpi.id',
                        'tmkamus_kpi.nama_kpi',
                        'tmkamus_kpi.definisi',
                        'tmkamus_kpi.tujuan',
                        'tmkamus_kpi.tmsatuan_id',
                        'tmkamus_kpi.formula_penilaian',
                        'tmkamus_kpi.target',
                        'tmkamus_kpi.tmfrekuensi_id',
                        'tmkamus_kpi.tmpolaritas_id',
                        'tmkamus_kpi.unit_pemilik_kpi',
                        'tmkamus_kpi.unit_pengelola_kpi',
                        'tmkamus_kpi.sumber_data',
                        'tmkamus_kpi.jenis_pengukuran',
                        'tmkamus_kpi.catatan',
                        'tmfrekuensi.nama_frekuensi',
                        'tmfrekuensi.kode',
                        'tmpolaritas.kode',
                        'tmpolaritas.nama_polaritas',
                        'tmsatuan.kode',
                        'tmsatuan.nama_satuan',
                        'tmtahun.tahun',
                        'tmkamus_kpi.created_at',
                        'tmkamus_kpi.updated_at',
                        'tmunit.nama as nama_unit',
                        \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll'),
                        'tmtable_assigment.id as idassign',
                        'tmtable_assigment.sub',
                        'tmtable_assigment.kpi',
                        'tmtable_assigment.total',
                        'tmtable_assigment.tmprospektif_id',
                        'tmtable_assigment.tmprospektif_sub_id'

                    )->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')
                        ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
                        ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                        ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                        ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                        ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
                        ->where('tmtable_assigment.tmprospektif_sub_id', $tmprospektif_subs->id)->get();
                    $d = 1;
                    foreach ($kamus as $kamuss) {
                        $dataset[$idx]['id']['val'] = $kamuss->id;
                        $dataset[$idx]['status']['val'] = '';
                        $dataset[$idx]['parent']['val'] = false;
                        $dataset[$idx]['nama_kpi']['val'] = $this->parentIdassign(
                            $kamuss->tmprospektif_id,
                            $kamuss->tmprospektif_sub_id,
                        ) . $this->listProspektif($kamuss->id);
                        $dataset[$idx]['asign_id']['val'] = $kamuss->idassign;
                        $dataset[$idx]['nama_satuan']['val'] = $this->chilProspective('nama_satuan', $kamuss->nama_satuan);
                        $dataset[$idx]['target']['val'] = $this->chilProspective('target', $kamuss->target);
                        $dataset[$idx]['nama_polaritas']['val'] = $this->chilProspective('nama_polaritas', $kamuss->nama_polaritas);
                        $dataset[$idx]['sub']['val'] = $this->chilProspective('sub', $kamuss->sub);
                        $dataset[$idx]['kpi']['val'] = $this->chilProspective('kpi', $kamuss->kpi);
                        $dataset[$idx]['total']['val'] = $this->chilProspective('total', $kamuss->total);
                        $dataset[$idx]['tmprospektif_id']['val'] = 0;
                        $dataset[$idx]['tmprospektif_sub_id']['val'] = '';
                        $dataset[$idx]['action'] = '';
                        $idx++;
                        $d = 1;
                    }
                }
            }
            $idx++;
            $unit_kerja = tmunit::whereId($tmunit_id)->first();
            $tahun = tmtahun::whereId($tmtahun_id)->first();

            $runit_kerja = $unit_kerja->nama ?? $unit_kerja->nama;
            $rtahun = $tahun->tahun ?? $tahun->tahun;

            return view($this->view . 'form_edit', [
                'render' => $dataset,
                'unit' => tmunit::get(),
                'tahun' => tmtahun::get(),
                'tmkamus_kpi' => tmkamus_kpi::get(),
                'unit_kerja' => $runit_kerja,
                'tahun' => $rtahun,
                'batch' => $batch,
                'id' => $id,
            ]);
        } else {
            return response()->json([
                'data' => 'paremeter is missing',
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $asign_id = $this->request->asign_id;
        $jdata = count($this->request->tmkamus_kpi_id);
        $tmtahun_id = $this->request->tmtahun_id;
        $tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
        $tmprospektif_id = $this->request->tmprospektif_id;
        $tmprospektif_sub_id = $this->request->tmprospektif_sub_id;
        $tmunit_id = $this->request->tmunit_id;
        $catatan = $this->request->catatan;
        $user_id = $this->request->user_id;
        $status = $this->request->status;
        $sub = $this->request->sub;
        $kpi = $this->request->kpi;
        $total = $this->request->total;
        $batch = $this->request->batch;
        $min_sub = min($this->AddToArray($sub));
        $max_sub = max($this->AddToArray($sub));
        $t_total = array_sum($total);
        if ($t_total == 0) {
            return response()->json([
                'status' => 2,
                'msg' => 'Gagal target tidak boleh sama denga 0',
            ]);
        } else {

            $target = ($max_sub - $min_sub) / (int) $t_total * 100;
            if ($target > 20) {

                return response()->json([
                    'status' => 2,
                    'msg' => 'Tidak sesuai dengan ketentuan persentase melebih  20% Result : ' . $target,
                ]);

            } else {
                for ($i = 0; $i < $jdata; $i++) {
                    $data = tmtable_assigment::find($asign_id[$i]);
                    $data->tmtahun_id = Properti_app::getTahunActive();
                    $data->tmkamus_kpi_id = $tmkamus_kpi_id[$i];
                    $data->tmprospektif_id = $tmprospektif_id[$i];
                    $data->tmprospektif_sub_id = $tmprospektif_sub_id[$i];
                    $data->tmunit_id = $tmunit_id;
                    $data->tmtahun_id = $tmtahun_id;
                    $data->user_id = Auth::user()->id;
                    $data->status = 1;
                    $data->sub = $sub[$i];
                    $data->kpi = $kpi[$i];
                    $data->total = $total[$i];
                    $data->batch = Properti_app::batch();
                    $data->save();

                }
            }
            Session::flash('msg', '<div class="alert alert-info">Data berhasil di update</div>');
            return response()->json([

                'status' => 1,
                'msg' => 'Data berhasil di update , sesuai dengan ketentuan nilai persentase assigment : ' . $target,

            ]);
        }

    }

    public function destroy($id)
    {
        try {
            if (is_array($this->request->id)) {
                tmtable_assigment::whereIn('id', $this->request->id);
            } else {
                tmtable_assigment::whereId($this->request->id);
            }
            return response()->json(['status' => 1, 'msg' => 'data berashil di hapus']);
        } catch (tmtable_assigment $th) {
            //throw $th;F
            return response()->json(['status' => 1, 'msg' => $th->getMessage()]);

        }
    }
}
