<?php

namespace App\Http\Controllers;

use App\Helpers\Properti_app;
use App\Models\tmkamus_kpi;
use App\Models\tmprospektif;
use App\Models\tmtable_assigment;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;

class Assignmentcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $request;
    protected $route;
    protected $view;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.assignment.';
        $this->route = 'kamus.assignment.';
    }

    public function index()
    {
        $unit = tmunit::get();
        $tahun = tmtahun::get();

        return view($this->view . 'index', [
            'unit' => $unit,
            'tahun' => $tahun,
        ]);
    }

    public function load_unit($id)
    {
        if ($this->request->ajax()) {
            $id = $this->request->id;
            $sql = tmkamus_kpi::select(
                
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
                'tmfrekuensi.nama_frekuensi',
                'tmfrekuensi.kode',
                'tmpolaritas.kode',
                'tmpolaritas.nama_polaritas',
                'tmsatuan.kode',
                'tmsatuan.nama_satuan',
                'tmtahun.tahun',
                'tmkamus_kpi.created_at',
                'tmkamus_kpi.updated_at',
                'tmunit.nama as nama_unit'

            )->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
                ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
                ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
                ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
                ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
                ->where('tmkamus_kpi.id', $id)->get();

            if ($sql->count() > 0) {
                $unit = tmunit::get();

                $data = $sql->first();
                return view($this->view . 'assigment_to', [
                    'tmkamus_kpi_id' => $data->idnya,
                    'unit' => $unit,
                ]);
            } else {
                return response()->json([
                    'message' => 'data tidak di temukan silahkan cek parameter anda',
                    'status' => 'error',
                ]);
            }
        }
    }
    public function save_assingment()
    {
        if ($this->request->ajax()) {

            $this->request->validate(
                [
                    'tmkamus_kpi_id' => 'required',
                    'tmunit_id' => 'required',

                ]);

            $data = new tmtable_assigment;
            $data->tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
            $data->tmunit_id = $this->request->tmunit_id;
            $data->catatan = $this->request->catatan;
            $data->save();
            return response()->json([
                'status' => 1,
                'messages' => 'data berhasil disimpan',
            ]);
        }
    }

    /**
     *
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function api()
    {
        $data = tmprospektif::select(

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

            'tmfrekuensi.nama_frekuensi',

            'tmkamus_kpi_sub.nama_kpi_sub',
            'tmkamus_kpi_sub.definisi',
            'tmkamus_kpi_sub.tujuan',
            'tmkamus_kpi_sub.tmsatuan_id',
            'tmkamus_kpi_sub.formula_penilaian',
            'tmkamus_kpi_sub.target as target_sub',
            'tmkamus_kpi_sub.tmfrekuensi_id',
            'tmkamus_kpi_sub.tmpolaritas_id',
            'tmkamus_kpi_sub.unit_pemilik_kpi',
            'tmkamus_kpi_sub.unit_pengelola_kpi',
            'tmkamus_kpi_sub.sumber_data',

            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmkamus_kpi.created_at',
            'tmkamus_kpi.updated_at',
            'tmunit.nama as nama_unit',
            'tmtable_assigment.id as tmtable_assigment_id',
            'tmtable_assigment.id',

            'tmprospektif.nama_prospektif',
            'tmprospektif.id as parent_id_pr',
            'tmprospektif.kode',

            'tmprospektif_sub.tmprospektif_id',
            'tmprospektif_sub.nama_prospektif_sub'
          )

            ->join('tmprospektif_sub', 'tmprospektif.id', '=', 'tmprospektif_sub.tmprospektif_id', 'LEFT')

            ->join('tmkamus_kpi', 'tmkamus_kpi.tmprospektif_id', '=', 'tmprospektif.id', 'LEFT')
            ->join('tmkamus_kpi_sub', 'tmkamus_kpi_sub.tmprospektif_sub_id', '=', 'tmprospektif_sub.id', 'LEFT')
            ->Leftjoin('tmsatuan', function ($join) {
                $join->on('tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')->on('tmkamus_kpi_sub.tmsatuan_id', '=', 'tmsatuan.id');

            })
            ->Leftjoin('tmfrekuensi', function ($join) {
                $join->on('tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id')->on('tmkamus_kpi_sub.tmfrekuensi_id', '=', 'tmfrekuensi.id');

            })
            ->Leftjoin('tmpolaritas', function ($join) {
                $join->on('tmkamus_kpi.tmpolaritas_id', '=', 'tmpolaritas.id')->on('tmkamus_kpi_sub.tmpolaritas_id', '=', 'tmpolaritas.id');
            })
            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->join('tmtable_assigment', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id', 'left outer')
            ->where('tmprospektif.parent_id', 0);

        if ($this->request->tahun_id) {
            $data->where('tmkamus_kpi.tmtahun_id', $this->request->tahun_id);
        }
        if ($this->request->unit_id) {
            $data->where('tmkamus_kpi.unit_pengelola_kpi', $this->request->unit_id);
        }
        $sql = $data->get();

        return DataTables::of($sql)
            ->editColumn('status', function ($p) {

                $id = isset($p->tmtable_assigment_id) ? $p->tmtable_assigment_id : 0;
                return Properti_app::assignment_status($id);

            }, true)

            ->editColumn('child_prospective', function ($p) {
                return Properti_app::Child_prosepective($p->parent_id_pr);
            }, true)

            ->editColumn('action', function ($p) {
                return '<a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" id="view_data" data-id="' . $p->idnya . '"><i class="fa fa-plus"></i></a>
                        <a href="" class="btn btn-sm btn-clean btn-icon" id="view"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-minus"></i></a> ';
            }, true)
            ->editColumn('tmtahun_id', function ($p) {
                return $p->tahun;
            }, true)
            ->editColumn('unit_pengelola_kpi', function ($p) {
                return $p->unit_pengelola_kpi;
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->name;
            }, true)
            ->addIndexColumn()
            ->rawColumns(['usercrate', 'action', 'id', 'status',
                'sub',
                'kpi',
                'child_prospective',
                'total',

            ])->toJson();
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
