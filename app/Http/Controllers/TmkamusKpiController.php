<?php

namespace App\Http\Controllers;

use App\Helpers\Properti_app;
use App\Models\tmfrekuensi;
use App\Models\tmjenis_pengukuran;
use App\Models\tmkamus_kpi;
use App\Models\tmpolaritas;
use App\Models\tmsatuan;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TmkamusKpiController extends Controller
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
        $this->view = '.kamus.';
        $this->route = 'master.tmkamus.';
    }

    public function index()
    {
        $title = 'Master Kamus';
        $bidang = tmunit::get();
        return view(
            $this->view . 'index',
            compact('title', 'bidang')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!$this->request->ajax()) {
            return redirect(route('home'));
            exit();
        }
        $satuan = tmsatuan::get();
        $frekuensi = tmfrekuensi::get();
        $polaritas = tmpolaritas::get();
        $unit_pengelola = tmunit::get();
        $jenis_pengukuran = tmjenis_pengukuran::get();
        $tahun = tmtahun::get();
        $title = 'kamus Akses User';
        return view($this->view . 'form_add', compact(
            'title',
            'satuan',
            'frekuensi',
            'unit_pengelola',
            'polaritas',
            'jenis_pengukuran',
            'tahun'
        ));
    }

    public function getbangunan()
    {
        $title = 'kamus Akses User';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {

        $icn = new Properti_app;
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
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left');
        if ($this->request->unit_pengelola_id) {
            $data->where('tmkamus_kpi.unit_pengelola_kpi', $this->request->unit_pengelola_id);
        }
        $data->get();

        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->idnya . "' />";
            })
            ->editColumn('action', function ($p) use ($icn) {
                return '<div class="symbol symbol-40 symbol-light-success mr-5"><div class="symbol-label"><a href="" class="btn btn-sm btn-clean btn-icon" title="Edit data " id="edit" data-id="' . $p->idnya . '">' . $icn->edit_icon() . '</a></div></div>
                        &nbsp;
                        <div class="symbol symbol-40 symbol-light-warning mr-5"><div class="symbol-label"><a href="" class="btn btn-sm btn-clean btn-icon" id="view"  title="Detail data" data-id="' . $p->idnya . '">' . $icn->view_icon() . '</a></div></div>';
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
            ->rawColumns(['usercrate', 'action', 'id'])
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
            'nama_kpi' => 'required',
            'definisi' => 'required',
        ]);
        try {
            $data = new tmkamus_kpi();

            $data->nama_kpi = $this->request->nama_kpi;
            $data->definisi = $this->request->definisi;
            $data->tujuan = $this->request->tujuan;
            $data->tmsatuan_id = $this->request->tmsatuan_id;
            $data->formula_penilaian = $this->request->formula_penilaian;
            $data->target = $this->request->target;
            $data->tmfrekuensi_id = $this->request->tmfrekuensi_id;
            $data->tmpolaritas_id = $this->request->tmpolaritas_id;
            $data->unit_pemilik_kpi = $this->request->unit_pemilik_kpi;
            $data->unit_pengelola_kpi = $this->request->unit_pengelola_kpi;
            $data->sumber_data = $this->request->sumber_data;
            $data->tmtahun_id = $this->request->tmtahun_id;
            $data->catatan = $this->request->catatan_kamus;

            $data->jenis_pengukuran = implode(',', $this->request->jenis_pengukuran);

            $data->save();
            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (\Tmkamus $t) {
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
        if (!$this->request->ajax()) {
            return response()->json([
                'data' => 'data null',
                'aspx' => 'response aspx fail ',
            ]);
        }

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

        // dd($data);

        $id = $id;

        $satuan = tmsatuan::get();
        $frekuensi = tmfrekuensi::get();
        $polaritas = tmpolaritas::get();
        $unit_pengelola = tmunit::get();
        $jenis_pengukuran = tmjenis_pengukuran::get();
        $tahun = tmtahun::get();

        return view($this->view . 'form_show', [
            'id' => $id,
            'nama_kpi' => $data->nama_kpi,
            'definisi' => $data->definisi,
            'tujuan' => $data->tujuan,
            'tmsatuan_id' => $data->tmsatuan_id,
            'formula_penilaian' => $data->formula_penilaian,
            'target' => $data->target,
            'tmfrekuensi_id' => $data->tmfrekuensi_id,
            'tmpolaritas_id' => $data->tmpolaritas_id,
            'unit_pemilik_kpi' => $data->unit_pemilik_kpi,
            'unit_pengelola_kpi' => $data->unit_pengelola_kpi,
            'sumber_data' => $data->sumber_data,
            'jenis_pengukuran' => $data->jenis_pengukuran,
            'nama_satuan' => $data->nama_satuan,
            'nama_frekuensi' => $data->nama_frekuensi,
            'nama_polaritas' => $data->nama_polaritas,
            'catatan' => $data->catatan,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'satuan' => $data->satuan,
            'frekuensi' => $data->frekuensi,
            'pengukuran_ll' => $data->pengukuran_ll,
            'unit_pengelola' => $data->unit_pengelola,
            'polaritas' => $data->polaritas,
            'tahun' => $data->tahun,
            // 'catatan' => $data->catatan,
            'nama_unit' => $data->nama_unit,

        ]);
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

        $data = tmkamus_kpi::findOrFail($id);
        $id = $id;
        $nama_kpi = $data->nama_kpi;
        $definisi = $data->definisi;
        $tujuan = $data->tujuan;
        $tmsatuan_id = $data->tmsatuan_id;
        $formula_penilaian = $data->formula_penilaian;
        $target = $data->target;
        $tmfrekuensi_id = $data->tmfrekuensi_id;
        $tmpolaritas_id = $data->tmpolaritas_id;
        $unit_pemilik_kpi = $data->unit_pemilik_kpi;
        $unit_pengelola_kpi = $data->unit_pengelola_kpi;
        $sumber_data = $data->sumber_data;
        $vjenis_pengukuran = $data->jenis_pengukuran;
        $created_at = $data->created_at;
        $updated_at = $data->updated_at;

        $satuan = tmsatuan::get();
        $frekuensi = tmfrekuensi::get();
        $polaritas = tmpolaritas::get();
        $unit_pengelola = tmunit::get();
        $jenis_pengukuran = tmjenis_pengukuran::get();
        $tahun = tmtahun::get();

        return view($this->view . 'form_edit', [
            'id' => $id,
            'nama_kpi' => $nama_kpi,
            'definisi' => $definisi,
            'tujuan' => $tujuan,
            'tmsatuan_id' => $tmsatuan_id,
            'formula_penilaian' => $formula_penilaian,
            'target' => $target,
            'tmfrekuensi_id' => $tmfrekuensi_id,
            'tmpolaritas_id' => $tmpolaritas_id,
            'unit_pemilik_kpi' => $unit_pemilik_kpi,
            'unit_pengelola_kpi' => $unit_pengelola_kpi,
            'sumber_data' => $sumber_data,
            'jenis_pengukuran' => $jenis_pengukuran,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'satuan' => $satuan,
            'frekuensi' => $frekuensi,
            'unit_pengelola' => $unit_pengelola,
            'polaritas' => $polaritas,
            'vjenis_pengukuran' => $vjenis_pengukuran,
            'tahun' => $tahun,
            'catatan' => $data->catatan,

        ]);
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
            'nama_kpi' => 'required',
            'definisi' => 'required',
        ]);
        try {
            $data = tmkamus_kpi::find($id);

            $data->nama_kpi = $this->request->nama_kpi;
            $data->definisi = $this->request->definisi;
            $data->tujuan = $this->request->tujuan;
            $data->tmsatuan_id = $this->request->tmsatuan_id;
            $data->formula_penilaian = $this->request->formula_penilaian;
            $data->target = $this->request->target;
            $data->tmfrekuensi_id = $this->request->tmfrekuensi_id;
            $data->tmpolaritas_id = $this->request->tmpolaritas_id;
            $data->unit_pemilik_kpi = $this->request->unit_pemilik_kpi;
            $data->unit_pengelola_kpi = $this->request->unit_pengelola_kpi;
            $data->sumber_data = $this->request->sumber_data;
            $data->jenis_pengukuran = implode(',', $this->request->jenis_pengukuran);
            $data->catatan = $this->request->catatan_kamus;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil update',
            ]);
        } catch (\App\Models\tmkamus_kpi $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }

    public function status_kamu($status)
    {
        if ($status == 0) {
            $a = tmkamus_kpi::select('nama_kpi', 'definisi', 'tujuan', 'id')->get();
        } else {
            $a = [];
        }
        return response()->json($a);
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
                tmkamus_kpi::whereIn('id', $this->request->id)->delete();
            } else {
                tmkamus_kpi::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmkamus_kpi $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
