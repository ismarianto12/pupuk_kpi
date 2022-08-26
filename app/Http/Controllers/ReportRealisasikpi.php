<?php

namespace App\Http\Controllers;

use App\Models\tmkamus_kpi;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportRealisasikpi extends Controller
{
    protected $request;
    protected $route;
    protected $view;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.reportrealisasi.';
        $this->route = 'master.reportrealisasi.';
    }

    public function index()
    {
        $title = 'Setting data tahun';
        return view($this->view . 'index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!$this->request->ajax()) {
            return redirect(route('master.tahun'));
            exit();
        }
        $title = 'Tambah data master tahun';
        return view($this->view . 'form_add', compact('title'));
    }

    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;
            $data = tmtahun::find($id);
            $data->active = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tahun berhasil disimpan ',
            ]);
        }
    }

    public function api()
    {

        $tahun_id = $this->request->tahun_id;
        $user_id = Auth::user()->id;

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

        )->join('tmrealisasi_kpi', 'tmrealisasi_kpi.tmkamus_kpi_id', '=', 'tmkamus_kpi.id', 'left outer')
            ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left')
            ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left');
        // ->where('tmrealisasi_kpi.users_id',$user_id);

        // if ($tahun_id) {
        //     $data->where('tmtahun.id', $tahun_id);
        // }
        $sql = $data->get();

        return DataTables::of($sql)
            ->editColumn('sub', function ($p) {
                return $p->sub;
            }, true)->editColumn('kpi', function ($p) {
            return $p->skor_kpi;
        }, true)->editColumn('total', function ($p) {
            return $p->total;
        }, true)
            ->editColumn('action', function ($p) {
                return '<button class="btn btn-sm btn-clean btn-icon" id="view"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-eye"></i></button>';

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
            'tahun' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = new tmtahun();
            $data->tahun = $this->request->tahun;
            $data->kode = $this->request->kode;
            $data->active = $this->request->active;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (\tmtahun $t) {
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
        $data = tmtahun::find($id);
        $kode = $data->kode;
        $tahun = $data->tahun;
        $active = $data->active;
        $id = $data->id;

        $title = 'Edit data master tahun';
        return view($this->view . 'form_edit', compact(
            'title',
            'kode',
            'tahun',
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
            'tahun' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmtahun::find($id);
            $data->tahun = $this->request->tahun;
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

    public function report_realisasi($type)
    {
        switch ($type) {
            case 'excel':
                return $this->excel();
                break;
            case 'pdf':
                return $this->pdf();
                break;

        }
    }

    public function excel()
    {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . 'report_realisasi_kpi.xls');

        return view($this->view . 'report', [
            'title' => 'Report Realisasi KPI',
        ]);
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
                tmtahun::whereIn('id', $this->request->id)->delete();
            } else {
                tmtahun::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmtahun $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
