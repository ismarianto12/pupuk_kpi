<?php

namespace App\Http\Controllers;

use App\Models\tmfrekuensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class TmfrekuensiController extends Controller
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
        $this->view    = '.frekuensi.';
        $this->route   = 'master.tmfrekuensi.';
    }


    public function index()
    {
        $title = 'Setting Frekuensi';
        return view($this->view . 'index', compact('title'));
    }
    public function create()
    {
        if (!$this->request->ajax()) {
            return redirect(route('master.tmfrekuensi'));
            exit();
        }
        $title = 'Tambah data master frekuensi';
        return view($this->view . 'form_add', compact('title'));
    }

    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;
            $data = tmfrekuensi::find($id);
            $data->active  = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tmfrekuensi berhasil disimpan '
            ]);
        }
    }

    public function api()
    {
        $data = tmfrekuensi::select(
            'tmfrekuensi.id as idnya',
            'tmfrekuensi.kode',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.users_id',
            'tmfrekuensi.created_at',
            'tmfrekuensi.updated_at',

            'users.username',
            'users.password',
            'users.name',
            'users.id'
        )->join('users', 'tmfrekuensi.users_id', '=', 'users.id')->get();
        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->idnya . "' />";
            })
            ->editColumn('action', function ($p) {
                return  ' 
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" id="edit" data-id="' . $p->idnya . '" class="btn btn-icon btn-warning btn-sm me-1"><i class="fa fa-edit"></i></a>
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
            'nama_frekuensi' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = new tmfrekuensi();
            $data->nama_frekuensi = $this->request->nama_frekuensi;
            $data->kode = $this->request->kode;
            $data->users_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\App\Models\tmfrekuensi $t) {
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

        if (!$this->request->ajax()) {
            return response()->json([
                'data' => 'data null',
                'aspx' => 'response aspx fail '
            ]);
        }
        $data = tmfrekuensi::find($id);
        $kode = $data->kode;
        $nama_frekuensi  = $data->nama_frekuensi;
        $users_id = $data->users_id;
        $id = $data->id;

        $title = 'Edit data master tmfrekuensi';
        return view($this->view . 'form_edit', compact(
            'title',
            'kode',
            'nama_frekuensi',
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
            'nama_frekuensi' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmfrekuensi::find($id);
            $data->nama_frekuensi = $this->request->nama_frekuensi;
            $data->kode = $this->request->kode;
            $data->users_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\App\Models\tmfrekuensi $t) {
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
    public function destroy()
    {
        try {
            if (is_array($this->request->id))
                tmfrekuensi::whereIn('id', $this->request->id)->delete();
            else
                tmfrekuensi::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmfrekuensi $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
