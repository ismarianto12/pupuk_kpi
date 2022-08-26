<?php

namespace App\Http\Controllers;

use App\Models\tmjabatan;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class TmjabatanController extends Controller
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
        $this->view    = '.jabatan.';
        $this->route   = 'master.tmjabatan.';
    }


    public function index()
    {
        $title = 'Master data jabatan';
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
        $title = 'jabatan Akses User';
        return view($this->view . 'form_add', compact('title'));
    }

    public function getbangunan()
    {
        $title = 'Master data jabatan';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {
        $data = tmjabatan::get();
        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->id . "' />";
            })
            ->editColumn('action', function ($p) {
                return  ' 
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" class="btn btn-icon btn-warning btn-sm me-1" id="edit" data-id="' . $p->id . '"><i class="fa fa-edit"></i></a>
                <a href="#" class="btn btn-icon btn-danger btn-sm me-1" id="delete" data-id="' . $p->id . '"><i class="fa fa-trash"></i></a> 
            </div>
                ';
            }, true)
            ->addIndexColumn()
            ->rawColumns(['action', 'id'])
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
            'n_jabatan' => 'required',
        ]);
        try {
            $data = new tmjabatan();
            $data->kode = $this->request->kode;
            $data->n_jabatan = $this->request->n_jabatan;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\tmjabatan $t) {
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
        $data = tmjabatan::find($id);
        return view($this->view . 'form_edit', [
            'id' => $data->id,
            'kode' => $data->kode,
            'n_jabatan' => $data->n_jabatan,
            'kode_jabatan' => $data->kode_jabatan,
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
            'kode' => 'required',
            'n_jabatan' => 'required',
        ]);
        try {
            $data = tmjabatan::find($id);
            $data->kode = $this->request->kode;
            $data->n_jabatan = $this->request->n_jabatan;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil edit'
            ]);
        } catch (\tmjabatan $t) {
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
                tmjabatan::whereIn('id', $this->request->id)->delete();
            else
                tmjabatan::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmjabatan $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
