<?php

namespace App\Http\Controllers;

use App\Helpers\Properti_app;
use App\Models\tmfrekuensi;
use App\Models\tmhistory_pengajuan_kamus;
use App\Models\tmjenis_pengukuran;
use App\Models\tmkamus_kpi;
use App\Models\tmpengajuan_kamus_kpi;
use App\Models\tmpolaritas;
use App\Models\tmsatuan;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TmpengajuanKamusKpiController extends Controller
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
        $this->view = '.pengajuan_kamus_kpi.';
        $this->route = 'master.pengajuan_kamus_kpi.';
    }

    public function index()
    {
        $title = 'Pengajuan Kamus KPI';
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
        $user_id = Auth::user()->id;
        $data = tmpengajuan_kamus_kpi::select(
            'tmpengajuan_kamus_kpi.id as idnya',
            'tmpengajuan_kamus_kpi.nama_kpi',
            'tmpengajuan_kamus_kpi.definisi',
            'tmpengajuan_kamus_kpi.tujuan',
            'tmpengajuan_kamus_kpi.tmsatuan_id',
            'tmpengajuan_kamus_kpi.formula_penilaian',
            'tmpengajuan_kamus_kpi.target',
            'tmpengajuan_kamus_kpi.tmfrekuensi_id',
            'tmpengajuan_kamus_kpi.tmpolaritas_id',
            'tmpengajuan_kamus_kpi.unit_pemilik_kpi',
            'tmpengajuan_kamus_kpi.unit_pengelola_kpi',
            'tmpengajuan_kamus_kpi.sumber_data',
            'tmpengajuan_kamus_kpi.status',
            'tmpengajuan_kamus_kpi.jenis_pengukuran',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmpengajuan_kamus_kpi.created_at',
            'tmpengajuan_kamus_kpi.updated_at',
            'tmunit.nama as nama_unit'
        )->join('tmsatuan', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmpengajuan_kamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmpengajuan_kamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmpengajuan_kamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmpengajuan_kamus_kpi.user_id', $user_id);
        if ($this->request->tahun_id) {
            $data->where('tmpengajuan_kamus_kpi.tmtahun_id', $this->request->tahun_id);

        }
        $sql = $data->get();
        return DataTables::of($sql)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->idnya . "' />";
            })

            ->editColumn('action', function ($p) {
                $html =
                '<a href="" class="btn btn-sm btn-clean btn-icon" title="Edit data " id="edit" data-id="' . $p->idnya . '"><i class="fa fa-edit"></i></a>
                 <a href="" class="btn btn-sm btn-clean btn-icon" id="view"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-eye"></i></a>';

                if ($p->status == 1 || $p->status == null || $p->status == '' || $p->status == 0) {
                    $html .= '<a href="" class="btn btn-sm btn-clean btn-icon" id="assign_to"  title="Assign To" data-id="' . $p->idnya . '"><i class="fa fa-copy"></i></a>';
                } else {
                    $html .= '';
                }
                return $html;
            }, true)
            ->editColumn('tmtahun_id', function ($p) {
                return $p->tahun;
            }, true)
            ->editColumn('unit_pengelola_kpi', function ($p) {
                return $p->unit_pengelola_kpi;
            }, true)
            ->editColumn('assign_status', function ($p) {
                if ($p->status == 1 || $p->status == null || $p->status == '' || $p->status == 0) {
                    return '<span class="label label-lg label-light-info label-inline">Baru</span>';
                } else if ($p->status == 2) {
                    return '<span class="label label-lg label-light-warning label-inline">Pending</span>';
                } else if ($p->status == 3) {
                    return '<span class="label label-lg label-light-danger label-inline">Reject</span>';
                } else if ($p->status == 4) {
                    return '<span class="label label-lg label-light-success label-inline">Approved</span>';

                }
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->name;
            }, true)
            ->addIndexColumn()
            ->rawColumns(['usercrate', 'assign_status', 'action', 'id'])
            ->toJson();
    }

    public function assignto($id)
    {
        if (!$this->request->ajax()) {
            return response()->json(['status' => 'data not found']);
            exit();
        }
        $tmunit_id = Auth::user()->tmunit_id;
        $bidang = tmunit::where('id', '!=', $tmunit_id)->get();
        $id = $id;
        return view('pengajuan_kamus_kpi.assing_to', [
            'bidang' => $bidang,
            'id' => $id,
        ]);
    }

    public function getdataildata_modal()
    {
        if (!$this->request->ajax()) {
            return response()->json(['status' => 'data not found']);
            exit();
        }

        $id = $this->request->id;
        $data = tmpengajuan_kamus_kpi::select(
            'tmpengajuan_kamus_kpi.id as idnya',
            'tmpengajuan_kamus_kpi.nama_kpi',
            'tmpengajuan_kamus_kpi.definisi',
            'tmpengajuan_kamus_kpi.tujuan',
            'tmpengajuan_kamus_kpi.tmsatuan_id',
            'tmpengajuan_kamus_kpi.formula_penilaian',
            'tmpengajuan_kamus_kpi.target',
            'tmpengajuan_kamus_kpi.tmfrekuensi_id',
            'tmpengajuan_kamus_kpi.tmpolaritas_id',
            'tmpengajuan_kamus_kpi.unit_pemilik_kpi',
            'tmpengajuan_kamus_kpi.unit_pengelola_kpi',
            'tmpengajuan_kamus_kpi.sumber_data',
            'tmpengajuan_kamus_kpi.jenis_pengukuran',
            'tmpengajuan_kamus_kpi.catatan',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmpengajuan_kamus_kpi.created_at',
            'tmpengajuan_kamus_kpi.updated_at',
            'tmunit.nama as nama_unit',
            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmpengajuan_kamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll')
        )
            ->join('tmsatuan', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmpengajuan_kamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmpengajuan_kamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmpengajuan_kamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmpengajuan_kamus_kpi.id', $id)->firstOrFail();

        $status = Properti_app::status_app();
        return view('pengajuan_kamus_kpi.getdataildata_modal', [
            'bidang' => $data,
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function store()
    {
        $this->request->validate([
            'nama_kpi' => 'required',
            'definisi' => 'required',
            'tujuan' => 'required',
            'tmsatuan_id' => 'required',
            'formula_penilaian' => 'required',
            'target' => 'required',
            'tmfrekuensi_id' => 'required',
            'tmpolaritas_id' => 'required',
            'unit_pemilik_kpi' => 'required',
            'unit_pengelola_kpi' => 'required',
        ]);
        try {
            $data = new tmpengajuan_kamus_kpi();

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
            $data->tmtahun_id = $this->request->tahun_id;
            $data->from = Auth::user()->id;
            $data->user_id = Auth::user()->id;

            if ($this->request->jenis_pengukuran) {
                $data->jenis_pengukuran = implode(',', $this->request->jenis_pengukuran);
            } else {
                $data->jenis_pengukuran = [];
            }
            $data->save();
            tmhistory_pengajuan_kamus::insert([
                'tmpengajuan_kamus_kpi_id' => $data->id,
                'status' => '',
                'tahun_id' => $this->request->tahun_id,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (\App\Models\tmpengajuan_kamus_kpi $t) {
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

        $data = tmpengajuan_kamus_kpi::select(
            'tmpengajuan_kamus_kpi.id as idnya',
            'tmpengajuan_kamus_kpi.nama_kpi',
            'tmpengajuan_kamus_kpi.definisi',
            'tmpengajuan_kamus_kpi.tujuan',
            'tmpengajuan_kamus_kpi.tmsatuan_id',
            'tmpengajuan_kamus_kpi.formula_penilaian',
            'tmpengajuan_kamus_kpi.target',
            'tmpengajuan_kamus_kpi.tmfrekuensi_id',
            'tmpengajuan_kamus_kpi.tmpolaritas_id',
            'tmpengajuan_kamus_kpi.unit_pemilik_kpi',
            'tmpengajuan_kamus_kpi.unit_pengelola_kpi',
            'tmpengajuan_kamus_kpi.sumber_data',
            'tmpengajuan_kamus_kpi.jenis_pengukuran',
            'tmpengajuan_kamus_kpi.catatan',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmpengajuan_kamus_kpi.created_at',
            'tmpengajuan_kamus_kpi.updated_at',
            'tmunit.nama as nama_unit',
            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmpengajuan_kamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll')
        )
            ->join('tmsatuan', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmpengajuan_kamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmpengajuan_kamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmpengajuan_kamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmpengajuan_kamus_kpi.id', $id)->firstOrFail();

        // dd($data);

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
        $pengukuran_ll = $data->pengukuran_ll;
        $created_at = $data->created_at;
        $updated_at = $data->updated_at;

        $satuan = tmsatuan::get();
        $frekuensi = tmfrekuensi::get();
        $polaritas = tmpolaritas::get();
        $unit_pengelola = tmunit::get();
        $jenis_pengukuran = tmjenis_pengukuran::get();
        $tahun = tmtahun::get();

        return view($this->view . 'form_show', [
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
            'nama_satuan' => $data->nama_satuan,
            'nama_frekuensi' => $data->nama_frekuensi,
            'nama_polaritas' => $data->nama_polaritas,
            'catatan' => $data->catatan,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'satuan' => $satuan,
            'frekuensi' => $frekuensi,
            'pengukuran_ll' => $pengukuran_ll,
            'unit_pengelola' => $unit_pengelola,
            'polaritas' => $polaritas,
            'vjenis_pengukuran' => $vjenis_pengukuran,
            'tahun' => $tahun,
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

        $data = tmpengajuan_kamus_kpi::findOrFail($id);
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
            'catatan' => $data->catatan,
            'polaritas' => $polaritas,
            'vjenis_pengukuran' => $vjenis_pengukuran,
            'tahun' => $tahun,
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
            $data = tmpengajuan_kamus_kpi::find($id);

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
            $data->tmtahun_id = $this->request->tahun_id;

            if ($this->request->jenis_pengukuran) {
                $data->jenis_pengukuran = implode(',', $this->request->jenis_pengukuran);
            } else {
                $data->jenis_pengukuran = [];
            }
            $data->from = Auth::user()->id;
            $data->user_id = Auth::user()->id;

            $data->save();
            tmhistory_pengajuan_kamus::where('tmpengajuan_kamus_kpi_id', $data->id)->update([
                // 'tmpengajuan_kamus_kpi_id' => $data->id,
                'status' => '',
                'tahun_id' => $this->request->tmtahun_id,
                'user_id' => Auth::user()->id,
            ]);
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
    public function assignto_save()
    {

        $tmunit_id = $this->request->tmunit_id;
        $id = $this->request->id;

        try {
            tmpengajuan_kamus_kpi::find($id)->update([
                'assign_to' => $tmunit_id,
                'status' => 2,
            ]);
            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil assign',
            ]);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }

    }

    public function notifikasi_perunit()
    {

        $data = tmpengajuan_kamus_kpi::select(
            'tmpengajuan_kamus_kpi.id as idnya',
            'tmpengajuan_kamus_kpi.nama_kpi',
            'tmpengajuan_kamus_kpi.definisi',
            'tmpengajuan_kamus_kpi.tujuan',
            'tmpengajuan_kamus_kpi.tmsatuan_id',
            'tmpengajuan_kamus_kpi.formula_penilaian',
            'tmpengajuan_kamus_kpi.target',
            'tmpengajuan_kamus_kpi.tmfrekuensi_id',
            'tmpengajuan_kamus_kpi.tmpolaritas_id',
            'tmpengajuan_kamus_kpi.unit_pemilik_kpi',
            'tmpengajuan_kamus_kpi.unit_pengelola_kpi',
            'tmpengajuan_kamus_kpi.sumber_data',
            'tmpengajuan_kamus_kpi.jenis_pengukuran',
            'tmpengajuan_kamus_kpi.catatan',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmpengajuan_kamus_kpi.created_at',
            'tmpengajuan_kamus_kpi.updated_at',
            'tmunit.nama as nama_unit',
            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmpengajuan_kamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll')
        )
            ->join('tmsatuan', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmpengajuan_kamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmpengajuan_kamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmpengajuan_kamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmpengajuan_kamus_kpi.status', '!=', 4)
            ->where('tmpengajuan_kamus_kpi.assign_to', '!=', Auth::user()->id)
            ->get();

        return view($this->view . '.notifikasi_pengajuan', [
            'data' => $data,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save_getdataildata_modal()
    {
        if ($this->request->ajax()) {
            $id = $this->request->id;
            $status = $this->request->status;
            if ($status == 4) {

                $sql = tmpengajuan_kamus_kpi::find($id);

                $mapping = new tmkamus_kpi();
                $mapping->nama_kpi = $sql->nama_kpi;
                $mapping->definisi = $sql->definisi;
                $mapping->tujuan = $sql->tujuan;
                $mapping->tmsatuan_id = $sql->tmsatuan_id;
                $mapping->formula_penilaian = $sql->formula_penilaian;
                $mapping->target = $sql->target;
                $mapping->tmfrekuensi_id = $sql->tmfrekuensi_id;
                $mapping->tmpolaritas_id = $sql->tmpolaritas_id;
                $mapping->unit_pemilik_kpi = $sql->unit_pemilik_kpi;
                $mapping->unit_pengelola_kpi = $sql->unit_pengelola_kpi;
                $mapping->sumber_data = $sql->sumber_data;
                $mapping->tmtahun_id = $sql->tmtahun_id;

                if ($sql->jenis_pengukuran != '') {
                    $mapping->jenis_pengukuran = $sql->jenis_pengukuran;
                }
                $mapping->save();
                tmpengajuan_kamus_kpi::find($id)->update([
                    'status' => $status,
                ]);
            } else {
                tmpengajuan_kamus_kpi::find($id)->update([
                    'status' => $status,
                ]);

            }
            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);

        }
    }
    public function notifikasi_data()
    {

        if (!$this->request->ajax()) {
            return response()->json([
                'status' => 'parameter',
            ]);
        }
        $data = tmpengajuan_kamus_kpi::select(
            'tmpengajuan_kamus_kpi.id as idnya',
            'tmpengajuan_kamus_kpi.nama_kpi',
            'tmpengajuan_kamus_kpi.definisi',
            'tmpengajuan_kamus_kpi.tujuan',
            'tmpengajuan_kamus_kpi.tmsatuan_id',
            'tmpengajuan_kamus_kpi.formula_penilaian',
            'tmpengajuan_kamus_kpi.target',
            'tmpengajuan_kamus_kpi.tmfrekuensi_id',
            'tmpengajuan_kamus_kpi.tmpolaritas_id',
            'tmpengajuan_kamus_kpi.unit_pemilik_kpi',
            'tmpengajuan_kamus_kpi.unit_pengelola_kpi',
            'tmpengajuan_kamus_kpi.sumber_data',
            'tmpengajuan_kamus_kpi.jenis_pengukuran',
            'tmpengajuan_kamus_kpi.catatan',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',
            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmtahun.tahun',
            'tmpengajuan_kamus_kpi.created_at',
            'tmpengajuan_kamus_kpi.updated_at',
            'tmunit.nama as nama_unit',
            \DB::raw('(SELECT GROUP_CONCAT(tmjenis_pengukuran.jenis_pengukuran) from tmjenis_pengukuran where FIND_IN_SET(tmjenis_pengukuran.id,tmpengajuan_kamus_kpi.jenis_pengukuran) > 0) as pengukuran_ll')
        )
            ->join('tmsatuan', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmsatuan.id')
            ->join('tmfrekuensi', 'tmpengajuan_kamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmpengajuan_kamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmpengajuan_kamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmpengajuan_kamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->where('tmpengajuan_kamus_kpi.status', '!=', 4)
            ->where('tmpengajuan_kamus_kpi.assign_to', '!=', Auth::user()->id)
            ->get();

        return $data->count();
    }

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
