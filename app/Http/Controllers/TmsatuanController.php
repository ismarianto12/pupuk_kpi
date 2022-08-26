<?php

namespace App\Http\Controllers;

use App\Models\tmsatuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class TmsatuanController extends Controller
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
        $this->view    = '.satuan.';
        $this->route   = 'master.tmsatuan.';
    }


    public function index()
    {
        $title = 'Setting data Satuan';
        return view($this->view . 'index', compact('title'));
    }
    public function create()
    {
        if (!$this->request->ajax()) {
            return redirect(route('master.tmsatuan'));
            exit();
        }
        $title = 'Tambah data master satuan';
        return view($this->view . 'form_add', compact('title'));
    }

    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;

            $data = tmsatuan::find($id);
            $data->active  = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tmsatuan berhasil disimpan '
            ]);
        }
    }

    public function api()
    {
        $data = tmsatuan::select(
            'tmsatuan.id as idnya',
            'tmsatuan.kode',
            'tmsatuan.nama_satuan',
            'tmsatuan.users_id',
            'tmsatuan.created_at',
            'tmsatuan.updated_at',

            'users.username',
            'users.password',
            'users.name',
            'users.id'
        )->join('users', 'tmsatuan.users_id', '=', 'users.id')->get();
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
            'nama_satuan' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = new tmsatuan();
            $data->kode = $this->request->kode;
            $data->nama_satuan = $this->request->nama_satuan;
            $data->users_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\tmsatuan $t) {
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
        $data = tmsatuan::find($id);
        $kode = $data->kode;
        $nama_satuan  = $data->nama_satuan;
        $id = $data->id;

        $title = 'Edit data master tmsatuan';
        return view($this->view . 'form_edit', compact(
            'title',
            'kode',
            'nama_satuan',
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
            'nama_satuan' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmsatuan::find($id);
            $data->kode = $this->request->kode;
            $data->nama_satuan = $this->request->nama_satuan;
            $data->users_id = AUth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil simpan'
            ]);
        } catch (\Tmlevel $t) {
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
                tmsatuan::whereIn('id', $this->request->id)->delete();
            else
                tmsatuan::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmsatuan $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
