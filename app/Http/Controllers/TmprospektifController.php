<?php

namespace App\Http\Controllers;

use App\Models\tmprospektif;
use App\Models\tmtahun;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TmprospektifController extends Controller
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
        $this->view = '.tmprospektif.';
        $this->route = 'master.tmprospektif.';
    }

    public function index()
    {
        $title = 'Prospektif';
        $tahun = tmtahun::get();
        return view($this->view . 'index', compact('title', 'tahun'));
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

            return response()->json([
                'msg' => 'Set tahun berhasil disimpan ',
            ]);
        }
    }

    public function api()
    {
        $data = tmprospektif::select(
            'tmprospektif.id',
            'tmprospektif.nama_prospektif',
            'tmprospektif.kode',
            'tmprospektif.created_at',
            'tmprospektif.updated_at',
            'tmprospektif.user_id',
            'tmprospektif.tmlevel_id',
            'tmprospektif.tmtahun_id',
            'tmtahun.tahun',
            'users.name'

        )->join('users', 'tmprospektif.user_id', '=', 'users.id','left')
            ->join('tmtahun', 'tmtahun.id', '=', 'tmprospektif.tmtahun_id','left');

        if ($this->request->tmtahun_id) {
            $data->whehere('tmtahun_id', $this->request->tmtahun_id);
        }
        $sql = $data->get();
        return DataTables::of($sql)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->id . "' />";
            })
            ->editColumn('action', function ($p) {
                return '
                <div class="d-flex justify-content-end flex-shrink-0">
                <a href="#" id="edit" data-id="' . $p->id . '" class="btn btn-icon btn-warning btn-sm me-1"><i class="fa fa-edit"></i></a>
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
            'tmlevel_id'=> 'required',
            'tmtahun_id' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = new tmprospektif;
            $data->nama_prospektif = $this->request->nama_prospektif;
            $data->kode = $this->request->kode;
            $data->created_at = $this->request->created_at;
            $data->updated_at = $this->request->updated_at;
            $data->user_id = Auth::user()->id;
            $data->tmlevel_id = implode(',', $this->request->tmlevel_id);
            $data->target = $this->request->target;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (tmprospektif $t) {
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

//     id
        // nama_prospektif
        // kode
        // tmlevel_id
        // tmtahun_id
        // user_id
        // updated_at
        // created_at
        $data = tmprospektif::find($id);
        $id = $data->id;
        $nama_prospektif = $data->nama_prospektif;
        $kode = $data->kode;
      
        $user_id = $data->user_id;
        $tmlevel_id = $data->tmlevel_id;
        $parent_id = $data->parent_id;

        $title = 'Edit data master tahun';
        return view($this->view . 'form_edit', compact( 
            'id',
            'nama_prospektif',
            'kode',
         
            'user_id',
            'tmlevel_id',
            'parent_id',
        ));
    }
    public function update($id)
    {
        $this->request->validate([
            'tmlevel_id'=> 'required',
            'tmtahun_id' => 'required',
            'kode' => 'required',
        ]);
        try {
            $data = tmprospektif::find($id);
            $data->nama_prospektif = $this->request->nama_prospektif;
            $data->kode = $this->request->kode;
          
            $data->user_id = Auth::user()->id;
            $data->tmlevel_id = implode(',', $this->request->tmlevel_id);
            $data->target = $this->request->target;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil simpan',
            ]);
        } catch (tmprospektif $t) {
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
                tmprospektif::whereIn('id', $this->request->id)->delete();
            } else {
                tmprospektif::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmprospektif $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
