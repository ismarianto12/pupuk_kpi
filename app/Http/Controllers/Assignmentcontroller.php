<?php

namespace App\Http\Controllers;

use App\Models\tmkamus_kpi;
use App\Models\tmprospektif;
use App\Models\tmprospektif_sub;
use App\Models\tmtable_assigment;
use App\Models\tmtahun;
use App\Models\tmunit;
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

        return view($this->view . 'index', [
            'unit' => tmunit::get(),
            'tahun' => tmtahun::get(),

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

    public function api()
    {
        ini_set('memory_limit', '-1');

        $prospektif = tmprospektif::get();
        $idx = 0;
        foreach ($prospektif as $prospektiff) {
            $dataset[$idx]['id'] = $prospektiff->id;
            $dataset[$idx]['status'] = 'prospektiff';

            $dataset[$idx]['parent'] = true;
            $dataset[$idx]['nama_kpi'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
            $dataset[$idx]['nama_satuan'] = '';
            $dataset[$idx]['target'] = '';
            $dataset[$idx]['nama_polaritas'] = '';
            $dataset[$idx]['sub'] = '';
            $dataset[$idx]['kpi'] = '';
            $dataset[$idx]['total'] = '';
            $dataset[$idx]['action'] = '<a href="" class="btn btn-sm btn-clean btn-icon"
            title="Assignemn data as unit kerja" id="create"
            data-id="' . $prospektiff->id . '"
            data-status="prospektiff"><i class="fa fa-plus"></i></a>';

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

                $dataset[$idx]['parent'] = false;
                $dataset[$idx]['nama_kpi'] = '&nbsp;&nbsp;' . $klamuss->nama_kpi;
                $dataset[$idx]['nama_satuan'] = $klamuss->nama_satuan;
                $dataset[$idx]['target'] = $klamuss->target;
                $dataset[$idx]['nama_polaritas'] = $klamuss->nama_polaritas;
                $dataset[$idx]['sub'] = $klamuss->sub;
                $dataset[$idx]['kpi'] = $klamuss->kpi;
                $dataset[$idx]['total'] = $klamuss->total;
                $dataset[$idx]['action'] = ' <a href="" class="btn btn-sm btn-clean btn-icon"
                title="Assignemn data as unit kerja" id="create" data-status="prospektiff"
                data-id="' . $klamuss->id . '"><i class="fa fa-edit"></i></a>
            <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"
                title="Detail data" data-id="' . $klamuss->id . '"><i
                    class="fa fa-trash"></i></a>';

                $idx++;
                $a++;
            }
            // end get prospektif
            $c = 1;
            $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
            foreach ($tmprospektif_sub as $tmprospektif_subs) {
                $dataset[$idx]['id'] = $tmprospektif_subs->id;
                $dataset[$idx]['status'] = 'subprospektiff';
                $dataset[$idx]['parent'] = true;
                $dataset[$idx]['nama_kpi'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                $dataset[$idx]['nama_satuan'] = '';
                $dataset[$idx]['target'] = '';
                $dataset[$idx]['nama_polaritas'] = '';
                $dataset[$idx]['sub'] = '';
                $dataset[$idx]['kpi'] = '';
                $dataset[$idx]['total'] = '';
                $dataset[$idx]['action'] = '<a href="" class="btn btn-sm btn-clean btn-icon"
                title="Assignemn data as unit kerja" id="create"
                data-id="' . $tmprospektif_subs->id . '"
                data-status="subprospektiff"><i class="fa fa-plus"></i></a>';

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
                    $dataset[$idx]['id'] = $kamuss->id;
                    $dataset[$idx]['status'] = '';
                    $dataset[$idx]['parent'] = false;
                    $dataset[$idx]['nama_kpi'] = '&nbsp;&nbsp;' . $kamuss->nama_kpi;
                    $dataset[$idx]['nama_satuan'] = $kamuss->nama_satuan;
                    $dataset[$idx]['target'] = $kamuss->target;
                    $dataset[$idx]['nama_polaritas'] = $kamuss->nama_polaritas;
                    $dataset[$idx]['sub'] = $kamuss->sub;
                    $dataset[$idx]['kpi'] = $kamuss->kpi;
                    $dataset[$idx]['total'] = $kamuss->total;
                    $dataset[$idx]['action'] = ' <a href="" class="btn btn-sm btn-clean btn-icon"
                    title="Assignemn data as unit kerja" id="create"
                    data-id="' . $kamuss->id . '" data-status="subprospektiff"><i class="fa fa-edit"></i></a>
                <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"
                    title="Detail data" data-id="' . $kamuss->id . '" data-status="subprospektiff"><i
                        class="fa fa-trash"></i></a>';

                    $idx++;
                    $d = 1;

                }
                $idx++;
            }
            $idx++;
        }
        $setDataSet = isset($dataset) ? $dataset : [];
        $result = array_merge($setDataSet);
        return response()->json(['data' => $result]);
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

    public function create()
    {

        if ($this->request->ajax()) {
            $assingment_id = $this->request->tmprospektif_id;
            if ($this->request->status == "prospektiff") {
                $data = tmprospektif::find($assingment_id);
                $nama_prospektif = $data->nama_prospektif;
                $tmprospektif_id = $data->id;
                $tmprospektif_sub_id = 0;
            } else if ($this->request->status == "subprospektiff") {
                $data = tmprospektif_sub::find($assingment_id);
                $nama_prospektif = $data->nama_prospektif_sub;
                $tmprospektif_id = 0;
                $tmprospektif_sub_id = $data->id;

            }
            return view($this->view . 'form_add', [
                'title' => 'Tambah data prospektif',
                'nama_prospektif' => $nama_prospektif,
                'tmprospektif_id' => $tmprospektif_id,
                'tmprospektif_sub_id' => $tmprospektif_sub_id,
                'kamus' => tmkamus_kpi::get(),
                'parameter' => '',
            ]);

        }
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
            'tmkamus_kpi_id' => 'required',
            'tmprospektif_id' => 'required',
            'tmprospektif_sub_id' => 'required',
            // 'tmunit_id' => 'required',
            // 'catatan' => 'required',
            // 'status' => 'required',
            'sub' => 'required',
            'kpi' => 'required',
            'total' => 'required',
        ]);

        $data = new tmtable_assigment;
        $data->tmkamus_kpi_id = $this->request->tmkamus_kpi_id;
        $data->tmprospektif_id = $this->request->tmprospektif_id;
        $data->tmprospektif_sub_id = $this->request->tmprospektif_sub_id;
        $data->tmunit_id = $this->request->tmunit_id;
        $data->catatan = $this->request->catatan;
        $data->user_id = $this->request->user_id;
        $data->updated_at = $this->request->updated_at;
        $data->created_at = $this->request->created_at;
        $data->status = $this->request->status;
        $data->sub = $this->request->sub;
        $data->kpi = $this->request->kpi;
        $data->total = $this->request->total;
        $data->save();
        return response()->json([
            'status' => 1,
            'messages' => 'data berhasil disimpan',
        ]);

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
    public function destroy($id)
    {
        try {

            tmtable_assigment::where('tmkamus_kpi_id', $this->request->id)->delete();

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmtable_assigment $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
