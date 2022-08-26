<?php

namespace App\Http\Controllers;

use App\Models\tmpegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\tmjabatan;
use App\Models\tmunit;
use DataTables;
use App\Models\Tmlevel;

class TmpegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $request;
    protected $route;
    protected $view;
    function __construct(Request $request)
    {
        $this->request = $request;
        $this->view    = '.pegawai.';
        $this->route   = 'master.tmpegawai.';
    }


    public function index()
    {
        $title = 'Data pegawai';
        $jabatan = tmjabatan::get();
        $bidang = tmunit::get();

        return view($this->view . 'index', compact(
            'title',
            'jabatan',
            'bidang',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Master Data Pegawai';
        $level = Tmlevel::get();
        $jabatan = tmjabatan::get();
        $bidang = tmunit::get();
        return view($this->view . 'form_add', compact('title', 'level', 'jabatan', 'bidang'));
    }

    public function getbangunan()
    {
        $title = 'ppegawai Akses User';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {
        $bidang = $this->request->bidang;
        $jabatan = $this->request->jabatan;

        $sql = tmpegawai::select(
            'tmpegawai.id',
            'tmpegawai.tmjabatan_id',
            'tmpegawai.tmunit_id',
            'tmpegawai.nik',
            'tmpegawai.nama',
            'tmpegawai.alamat_lengkap',
            'tmpegawai.jk',
            'tmpegawai.no_hp',
            'tmpegawai.tempat_tgl_lahir',
            'tmpegawai.email',
            'tmunit.nama as nama_bidang',
            'tmunit.unit_kode',
            'tmjabatan.id',
            'tmjabatan.n_jabatan'
        )->join('tmunit', 'tmpegawai.tmunit_id', '=', 'tmunit.id')
            ->join('tmjabatan', 'tmpegawai.tmjabatan_id', '=', 'tmjabatan.id');
        if ($bidang) {
            $sql->where('tmunit.id', $bidang)->get();
        } else if ($jabatan) {
            $sql->where('tmjabatan.id', $jabatan)->get();
        } else if ($bidang && $jabatan) {
            $sql->where([
                'tmjabatan.id' => $jabatan,
                'tmunit.id' => $bidang
            ])->get();
        } else {
            $sql->get();
        }
        return DataTables::of($sql)
            ->editColumn('id', function ($p) {
                return '<label class="checkbox checkbox-single">
                <input type="checkbox" value="" class="group-checkable"/>
                <span></span>
            </label>';
            })
            ->editColumn('action', function ($p) {
                return  ' 
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="' . Url('master/pegawai/' . $p->id . '/edit') . '" class="btn btn-icon btn-warning btn-sm me-1"><i class="fa fa-edit"></i></a>
                <a href="#" class="btn btn-icon btn-danger btn-sm me-1" id="delete" data-id="' . $p->id . '"><i class="fa fa-trash"></i></a>
               
            </div>
                ';
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->nama;
            }, true)
            ->editColumn('jk', function ($p) {
                return $p->jk == 1 ? 'Laki - Laki' : 'Perempuan';
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

        // dd($this->request->all());

        $this->request->validate([
            'tmjabatan_id' => 'required',
            'tmunit_id' => 'required',
            // 'nik' => 'required',
            'nip' => 'required',
            'tgl_masuk' => 'required',
            'nama' => 'required',
            // 'alamat' => 'required',
            'jk' => 'required',
            'no_hp' => 'required',
            // 'ttl' => 'required',
            // 'pengukuhan_thun' => 'required',
            'email' => 'required',
        ]);
        try {
            $data = new tmpegawai();

            $data->tmunit_id  = $this->request->tmunit_id;
            $data->tmjabatan_id  = $this->request->tmjabatan_id;
            $data->nik  = $this->request->nik;
            $data->nama  = $this->request->nama;
            $data->alamat_lengkap  = $this->request->alamat_lengkap;
            $data->tempat_lahir  = $this->request->tempat_lahir;
            $data->jk  = $this->request->jk;
            $data->email  = $this->request->email;
            $data->no_telp  = $this->request->no_telp;
            $data->no_hp  = $this->request->no_hp;
            $data->perguruan_tinggi  = $this->request->perguruan_tinggi;
            $data->tahun_lulus  = $this->request->tahun_lulus;
            $data->gelar_akademik  = $this->request->gelar_akademik;
            $data->karir  = $this->request->karir;
            $data->tahun_pengangkatan  = $this->request->tahun_pengangkatan;
            $data->status_karyawan  = $this->request->status_karyawan;
            $data->user_id = Auth::user()->id;

            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\Tmppegawai $t) {
            return response()->json([
                'status' => 1,
                'msg' =>  $t,
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
        $title = 'Master Data Pegawai Edit';
        $level = Tmlevel::get();
        $jabatan = tmjabatan::get();
        $bidang = tmunit::get();
        return view($this->view . 'form_edit', compact('title', 'level', 'jabatan', 'bidang'));
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
            'jabatan_id' => 'required',
            'bidang_id' => 'required',
            // 'nik' => 'required',
            'nip' => 'required',
            'tgl_masuk' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'jk' => 'required',
            'no_hp' => 'required',
            'ttl' => 'required',
            // 'pengukuhan_thun' => 'required',
            'email' => 'required',
        ]);
        try {
            $data = tmpegawai::find($id);
            $data->tmunit_id  = $this->request->tmunit_id;
            $data->tmjabatan_id  = $this->request->tmjabatan_id;
            $data->nik  = $this->request->nik;
            $data->nama  = $this->request->nama;
            $data->alamat_lengkap  = $this->request->alamat_lengkap;
            $data->tempat_lahir  = $this->request->tempat_lahir;
            $data->jk  = $this->request->jk;
            $data->email  = $this->request->email;
            $data->no_telp  = $this->request->no_telp;
            $data->no_hp  = $this->request->no_hp;
            $data->perguruan_tinggi  = $this->request->perguruan_tinggi;
            $data->tahun_lulus  = $this->request->tahun_lulus;
            $data->gelar_akademik  = $this->request->gelar_akademik;
            $data->karir  = $this->request->karir;
            $data->tahun_pengangkatan  = $this->request->tahun_pengangkatan;
            $data->status_karyawan  = $this->request->status_karyawan;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil update'
            ]);
        } catch (\tmpegawai $t) {
            return response()->json([
                'status' => 1,
                'msg' =>  $t,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (is_array($this->request->id))
                tmpegawai::whereIn('id', $this->request->id)->delete();
            else
                tmpegawai::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmpegawai $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
