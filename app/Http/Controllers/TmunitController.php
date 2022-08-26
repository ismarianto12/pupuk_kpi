<?php

namespace App\Http\Controllers;

use App\Models\tmunit;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class TmunitController extends Controller
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
        $this->view    = '.tmunit.';
        $this->route   = 'master.unit.';
    }


    public function index()
    {
        $title = 'Master data unit';
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
            return redirect(route('home'));
            exit();
        }
        $title = 'unit Akses User';
        return view($this->view . 'form_add', compact('title'));
    }

    public function getbangunan()
    {
        $title = 'Master data unit';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {
        $data = tmunit::get();
        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->id . "' />";
            })
            ->editColumn('action', function ($p) {
                return  ' 
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" class="btn btn-icon btn-warning btn-sm me-1" id="edit" data-id="' . $p->id . '"><i class="fa fa-edit"></i></a> 
            </div>
                ';
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->nama;
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
            'unit_kode' => 'required',
            'nama' => 'required',
        ]);
        try {
            $data = new tmunit();
            $data->unit_kode = $this->request->unit_kode;
            $data->nama = $this->request->nama;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\tmunit $t) {
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
        $dat = tmunit::find($id);
        return view($this->view . 'form_edit', [
            'id' => $dat->id,
            'nama' => $dat->nama,
            'unit_kode' => $dat->unit_kode,
            'created_at' => $dat->created_at,
            'updated_at' => $dat->updated_at,
            'user_id' => $dat->user_id,
        ]);

        //
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
            'unit_kode' => 'required',
            'nama' => 'required'
        ]);
        // dd($this->request->all());
        try {
            $data = tmunit::find($id);
            $data->unit_kode = $this->request->unit_kode;
            $data->nama = $this->request->nama;
            $data->user_id = Auth::user()->id;

            $data->save();
            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\tmunit $t) {
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
                tmunit::whereIn('id', $this->request->id)->delete();
            else
                tmunit::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmunit $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
