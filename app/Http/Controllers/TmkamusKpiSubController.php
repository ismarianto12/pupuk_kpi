<?php

namespace App\Http\Controllers;

use App\Helpers\Properti_app;
use App\Models\tmfrekuensi;
use App\Models\tmjenis_pengukuran;
use App\Models\tmkamus_kpi;
use App\Models\tmkamus_kpi_sub;
use App\Models\tmpolaritas;
use App\Models\tmsatuan;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TmkamusKpiSubController extends Controller
{
    protected $request;
    protected $route;
    protected $view;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.kamus_sub.';
        $this->route = 'master.tmkamus.';
    }

    public function index()
    {
        $title = 'Master Kamus';
        $bidang = tmunit::get();
        $kamus = tmkamus_kpi::get();
        return view(
            $this->view . 'index',
            compact('title', 'bidang','kamus')
        );
    }

    public function request_ajax()
    {
        if ($this->request->ajax()) {
            $tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
            $data = tmkamus_kpi_sub::where('tmkamus_id', $tmkamus_kpi_id)->get();
            if ($data->count() > 0) {
                $result = $data;
            } else {
                $result = $data;
            }
            return response()->json($result);
        }
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
        $tmkamus_kpi = tmkamus_kpi::get();

        $tahun = tmtahun::get();
        $title = 'kamus Akses User';

        return view($this->view . 'form_add', compact(
            'tmkamus_kpi',
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
        $tmkamus_kpi  = $this->request->tmkamus_kpi;

        $icn = new Properti_app;
        $data = tmkamus_kpi_sub::select(
            'tmkamus_kpi_sub.id as idnya',
            'tmkamus_kpi_sub.nama_kpi_sub',
            'tmkamus_kpi_sub.definisi',
            'tmkamus_kpi_sub.tujuan',
            'tmkamus_kpi_sub.tmsatuan_id',
            'tmkamus_kpi_sub.formula_penilaian',
            'tmkamus_kpi_sub.target',
            'tmkamus_kpi_sub.tmfrekuensi_id',
            'tmkamus_kpi_sub.tmpolaritas_id',
            'tmkamus_kpi_sub.unit_pemilik_kpi',
            'tmkamus_kpi_sub.unit_pengelola_kpi',
            'tmkamus_kpi_sub.sumber_data',
            'tmkamus_kpi_sub.jenis_pengukuran',
            'tmkamus_kpi_sub.tmtahun_id',
            'tmkamus_kpi_sub.created_at',
            'tmkamus_kpi_sub.updated_at',
            'tmkamus_kpi_sub.nonactive',
            'tmkamus_kpi_sub.catatan',
            'tmkamus_kpi_sub.tmkamus_kpi_id',
            'tmkamus_kpi.nama_kpi',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmunit.nama as nama_unit'
        )
            ->join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmkamus_kpi_sub.tmkamus_kpi_id', 'left')
            ->join('tmsatuan', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmkamus_kpi_sub.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi_sub.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi_sub.unit_pengelola_kpi', '=', 'tmunit.id', 'left');
        if ($this->request->tmtahun_id) {
            $data->where('tmkamus_kpi_sub.tmtahun_id', $this->request->tmtahun_id);
        }
        if($tmkamus_kpi){
            $data->where('tmkamus_kpi_sub.tmkamus_kpi_id',$tmkamus_kpi);
        }
        $sql = $data->get();
        return DataTables::of($sql)
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
    public function kamus_sub()
    {
        $tkamus_parent_id = $this->request->tmkamus_parent_id;

        $icn = new Properti_app;
        $data = tmkamus_kpi_sub::select(
            'tmkamus_kpi_sub.nama_kpi_sub',
            'tmkamus_kpi_sub.definisi',
            'tmkamus_kpi_sub.tujuan',
            'tmkamus_kpi_sub.tmsatuan_id',
            'tmkamus_kpi_sub.formula_penilaian',
            'tmkamus_kpi_sub.target',
            'tmkamus_kpi_sub.tmfrekuensi_id',
            'tmkamus_kpi_sub.tmpolaritas_id',
            'tmkamus_kpi_sub.unit_pemilik_kpi',
            'tmkamus_kpi_sub.unit_pengelola_kpi',
            'tmkamus_kpi_sub.sumber_data',
            'tmkamus_kpi_sub.jenis_pengukuran',
            'tmkamus_kpi_sub.tmtahun_id',
            'tmkamus_kpi_sub.created_at',
            'tmkamus_kpi_sub.updated_at',
            'tmkamus_kpi_sub.nonactive',
            'tmkamus_kpi_sub.catatan',
            'tmkamus_kpi_sub.tmkamus_kpi_id',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmunit.nama as nama_unit'
        )->join('tmsatuan', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmkamus_kpi_sub.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi_sub.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi_sub.unit_pengelola_kpi', '=', 'tmunit.id', 'left');
        if ($this->request->tmkamus_parent_id) {
            $data->where('tmkamus_kpi_sub.unit_pengelola_kpi', $tkamus_parent_id);
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
    public function store()
    {
        $this->request->validate([
            'nama_kpi_sub' => 'required',
            'definisi' => 'required',
        ]);
        try {
            $data = new tmkamus_kpi_sub();

            $data->tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
            $data->nama_kpi_sub = $this->request->nama_kpi_sub;
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
            // created_at
            // updated_at
            // nonactive
            $data->catatan = $this->request->catatan;
            $data->jenis_pengukuran = isset($this->request->jenis_pengukuran) ? implode(',', $this->request->jenis_pengukuran) : '';

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

        $data = tmkamus_kpi_sub::select(
            'tmkamus_kpi_sub.id as idnya',
            'tmkamus_kpi_sub.nama_kpi_sub',
            'tmkamus_kpi_sub.definisi',
            'tmkamus_kpi_sub.tujuan',
            'tmkamus_kpi_sub.tmsatuan_id',
            'tmkamus_kpi_sub.formula_penilaian',
            'tmkamus_kpi_sub.target',
            'tmkamus_kpi_sub.tmfrekuensi_id',
            'tmkamus_kpi_sub.tmpolaritas_id',
            'tmkamus_kpi_sub.unit_pemilik_kpi',
            'tmkamus_kpi_sub.unit_pengelola_kpi',
            'tmkamus_kpi_sub.sumber_data',
            'tmkamus_kpi_sub.jenis_pengukuran',
            'tmkamus_kpi_sub.catatan',
            'tmkamus_kpi.nama_kpi as parent_kpi_nama',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmkamus_kpi_sub.created_at',
            'tmkamus_kpi_sub.updated_at',
            'tmunit.nama as nama_unit',
            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmkamus_kpi_sub.jenis_pengukuran) > 0) as pengukuran_ll')
        )
            ->join('tmkamus_kpi', 'tmkamus_kpi.id', 'tmkamus_kpi_sub.tmkamus_kpi_id', 'left')
            ->join('tmsatuan', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmkamus_kpi_sub.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi_sub.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi_sub.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi_sub.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmkamus_kpi_sub.id', $id)->firstOrFail();

        // $satuan = tmsatuan::get();
        // $frekuensi = tmfrekuensi::get();
        // $polaritas = tmpolaritas::get();
        // $unit_pengelola = tmunit::get();
        // $jenis_pengukuran = tmjenis_pengukuran::get();
        // $tahun = tmtahun::get();

        return view($this->view . 'form_show', [
            'id' => $id,
            'nama_kpi_sub' => $data->nama_kpi_sub,
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
            'parent_kpi_nama' => $data->parent_kpi_nama,
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

        $data = tmkamus_kpi_sub::findOrFail($id);
        $id = $id;
        $nama_kpi_sub = $data->nama_kpi_sub;
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
            'tmkamus_kpi' => tmkamus_kpi::get(),
            'nama_kpi_sub' => $nama_kpi_sub,
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
            $data = tmkamus_kpi_sub::find($id);

            $data->nama_kpi_sub = $this->request->nama_kpi_sub;
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
        } catch (\App\Models\tmkamus_kpi_sub $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }

    public function status_kamu($status)
    {
        if ($status == 0) {
            $a = tmkamus_kpi_sub::select('nama_kpi', 'definisi', 'tujuan', 'id')->get();
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
                tmkamus_kpi_sub::whereIn('id', $this->request->id)->delete();
            } else {
                tmkamus_kpi_sub::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmkamus_kpi_sub $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
