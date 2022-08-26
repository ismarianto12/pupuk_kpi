<?php

namespace App\Http\Controllers;

use App\Models\tmtahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class TmtahunController extends Controller
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
        $this->view    = '.tahun.';
        $this->route   = 'master.tahun.';
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
            $data->active  = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tahun berhasil disimpan '
            ]);
        }
    }

    public function api()
    {
        $data = tmtahun::select(
            'tmtahun.id as idnya',
            'tmtahun.tahun',
            'tmtahun.id',
            'tmtahun.kode',
            'tmtahun.active',
            'users.username',
            'users.password',
            'users.name',
            'users.id'
        )->join('users', 'tmtahun.user_id', '=', 'users.id')->get();
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
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\tmtahun $t) {
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
        $data = tmtahun::find($id);
        $kode = $data->kode;
        $tahun  = $data->tahun;
        $active  =  $data->active;
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
                tmtahun::whereIn('id', $this->request->id)->delete();
            else
                tmtahun::whereid($this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (tmtahun $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }
}
