<?php

namespace App\Http\Controllers;

use App\Models\tmistilah_kpi;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TmistilahKpiController extends Controller
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
        $this->view = '.tmistilah_kpi.';
        $this->route = 'master.tmistilah_kpi.';
    }

    public function index()
    {
        $title = 'Setting master data istilah kpi';
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
            return redirect(route('master.tmistilah_kpi'));
            exit();
        }
        $title = 'Tambah data master istilah_kpi';
        return view($this->view . 'form_add', compact('title'));
    }

    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;
            $data = tmistilah_kpi::find($id);
            $data->active = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set istilah_kpi berhasil disimpan ',
            ]);
        }
    }

    public function api()
    {
        $data = tmistilah_kpi::select(

            'tmistilah_kpi.id as id_istilah',
            'tmistilah_kpi.kode',
            'tmistilah_kpi.nama_istilah',
            'tmistilah_kpi.keterangan',
            'tmistilah_kpi.created_at',
            'tmistilah_kpi.updated_at',
            'tmistilah_kpi.user_id',

            'users.id'
        )->join('users', 'tmistilah_kpi.user_id', '=', 'users.id')->get();
        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->idnya . "' />";
            })
            ->editColumn('action', function ($p) {
                return '
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" id="edit" data-id="' . $p->id_istilah . '" class="btn btn-icon btn-warning btn-sm me-1"><i class="fa fa-edit"></i></a>
                ';
            }, true)
            ->editColumn('created_by', function ($p) {
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
            'kode' => 'required',
            'nama_istilah' => 'required',
        ]);
        try {
            $data = new tmistilah_kpi();
            $data->id = $this->request->id;
            $data->kode = $this->request->kode;
            $data->nama_istilah = $this->request->nama_istilah;
            $data->keterangan = $this->request->keterangan;
            $data->kode = $this->request->kode;
            $data->user_id = Auth::user()->id;

            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (tmistilah_kpi $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t->getMessage(),
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
        $data = tmistilah_kpi::find($id);
       
        $title = 'Edit data master istilah_kpi';
        return view($this->view . 'form_edit', [
            'id' => $data->id,
            'kode' => $data->kode,
            'nama_istilah' => $data->nama_istilah,
            'keterangan' => $data->keterangan,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'user_id' => $data->user_id,
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
            'istilah_kpi' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmistilah_kpi::find($id);

            $data->id = $this->request->id;
            $data->kode = $this->request->kode;
            $data->nama_instilah = $this->request->nama_instilah;
            $data->keterangan = $this->request->keterangan;
            $data->kode = $this->request->kode;

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
                tmistilah_kpi::whereIn('id', $this->request->id)->delete();
            } else {
                tmistilah_kpi::whereid($this->request->id)->delete();
            }
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmistilah_kpi $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
