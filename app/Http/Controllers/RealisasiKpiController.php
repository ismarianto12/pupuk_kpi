<?php

namespace App\Http\Controllers;

use App\Models\tmkamus_kpi;
use App\Models\tmrealisasi_kpi;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealisasiKpiController extends Controller
{
    protected $request;
    protected $route;
    protected $view;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.realisasi_kpi.';
        $this->route = 'master.tmrealiasi_kpi.';
    }

    public function index()
    {

        $title = '';
        return view($this->view . 'index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Input data master realisasi KPI';
        return view($this->view . 'form_add', compact('title'));
    }
    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;
            $data = tmrealisasi_kpi::find($id);
            $data->active = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tmrealiasi_kpi berhasil disimpan ',
            ]);
        }
    }

    public function api()
    {

        $tahun_id = $this->request->tahun_id;
        $user_id = Auth::user()->id;
        $tmlevel_id = Auth::user()->tmlevel_id;

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
            'tmfrekuensi.nama_frekuensi',

            'tmrealisasi_kpi.tmkamus_kpi_id',
            'tmrealisasi_kpi.sub',
            'tmrealisasi_kpi.skor_kpi',
            'tmrealisasi_kpi.total',
            'tmrealisasi_kpi.users_id',
            'tmrealisasi_kpi.created_at',
            'tmrealisasi_kpi.updated_at',

            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmkamus_kpi.created_at',
            'tmkamus_kpi.updated_at',
            'tmunit.nama as nama_unit',

            // \DB::raw('sum(tmrealisasi_kpi.sub) as t_sub',
            //     'sum(tmrealisasi_kpi.skor_kpi) as t_skor_kpi',
            //     'sum(tmrealisasi_kpi.total) as t_total')

        )->join('tmrealisasi_kpi', 'tmrealisasi_kpi.tmkamus_kpi_id', '=', 'tmkamus_kpi.id', 'left')
            ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
            ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmkamus_kpi.unit_pengelola_kpi', $tmlevel_id);

        if ($tahun_id) {
            $data->where('tmkamus_kpi.tmtahun_id', $tahun_id);

        }
        $sql = $data->get();
        return DataTables::of($sql)
            ->editColumn('sub', function ($p) {
                return '<input type="text" name="sub"  value="' . $p->sub . '" class="form-control" />';
            }, true)->editColumn('kpi', function ($p) {
            return '<input type="text" name="kpi" value="' . $p->skor_kpi . '" class="form-control" />';
        }, true)->editColumn('total', function ($p) {
            return '<input type="text" name="total" value="' . $p->total . '" class="form-control" />';
        }, true)
            ->editColumn('action', function ($p) {
                return '<button class="btn btn-sm btn-clean btn-icon" title="Simpan data " id="simpan_xx" data-id="' . $p->idnya . '"><i class="fa fa-save"></i></button>
                         <button class="btn btn-sm btn-clean btn-icon" id="view"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-eye"></i></button>';

            }, true)
            ->addIndexColumn()
            ->rawColumns(['usercrate', 'action', 'id',
                'sub',
                'kpi',
                'total',
            ])
            ->toJson();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->request->validate([
            'tmrealiasi_kpi' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = new tmrealisasi_kpi();
            $data->tmrealiasi_kpi = $this->request->tmrealiasi_kpi;
            $data->kode = $this->request->kode;
            $data->active = $this->request->active;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (\tmrealisasi_kpi $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
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

        if (!$this->request->ajax()) {
            return response()->json([
                'data' => 'data null',
                'aspx' => 'response aspx fail ',
            ]);
        }
        $data = tmrealisasi_kpi::find($id);
        $kode = $data->kode;
        $tmrealiasi_kpi = $data->tmrealiasi_kpi;
        $active = $data->active;
        $id = $data->id;

        $title = 'Edit data master tmrealiasi_kpi';
        return view($this->view . 'form_edit', compact(
            'title',
            'kode',
            'tmrealiasi_kpi',
            'active',
            'id'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->request->validate([
            'tmrealiasi_kpi' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmrealisasi_kpi::find($id);
            $data->tmrealiasi_kpi = $this->request->tmrealiasi_kpi;
            $data->kode = $this->request->kode;
            $data->active = $this->request->active;
            $data->user_id = AUth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil simpan',
            ]);
        } catch (\Tmlevel $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }

    // funsimpan_data_kpi
    public function simpan_data_kpi()
    {

        try {
            $data = tmrealisasi_kpi::where('tmkamus_kpi_id', $this->request->tmkamus_kpi_id)->get();
            if ($data->count() > 0) {

                tmrealisasi_kpi::where('tmkamus_kpi_id', $this->request->tmkamus_kpi_id)->update([
                    'tmkamus_kpi_id' => ($this->request->tmkamus_kpi_id) ? $this->request->tmkamus_kpi_id : 0,
                    'sub' => ($this->request->sub) ? $this->request->sub : 0,
                    'skor_kpi' => ($this->request->kpi) ? $this->request->kpi : 0,
                    'total' => ($this->request->total) ? $this->request->total : 0,
                    'users_id' => ($this->request->users_id) ? $this->request->users_id : 0,
                    'created_at' => ($this->request->created_at) ? $this->request->created_at : 0,
                    'updated_at' => ($this->request->updated_at) ? $this->request->updated_at : 0,
                ]);
            } else {
                $data = new tmrealisasi_kpi;
                $data->tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
                $data->sub = $this->request->sub;
                $data->skor_kpi = ($this->request->kpi) ? $this->request->kpi : 0;
                $data->total = $this->request->total;
                $data->users_id = Auth::user()->id;
                $data->created_at = $this->request->created_at;
                $data->updated_at = $this->request->updated_at;
                $data->save();
            }

            return response()->json([
                'status' => 'ok',
                'msg' => 'data berhasil di simpan',
            ]);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        try {
            if (is_array($this->request->id)) {
                tmrealisasi_kpi::whereIn('id', $this->request->id)->delete();
            } else {
                tmrealisasi_kpi::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmrealisasi_kpi $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
