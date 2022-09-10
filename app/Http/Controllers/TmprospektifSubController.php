<?php

namespace App\Http\Controllers;

use App\Models\tmprospektif;
use App\Models\tmprospektif_sub;
use App\Models\tmtahun;
use App\Models\tmunit;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TmprospektifSubController extends Controller
{
    protected $request;
    protected $route;
    protected $view;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view = '.tmprospektif_sub.';
        $this->route = 'master.tmprospektif_sub.';
    }

    public function index()
    {
        $title = 'Master data subprospektif';
        $tahun = tmtahun::get();
        $tmunit_kerja = tmunit::get();
        $prospektif = tmprospektif::get();
        return view($this->view . 'index', compact('title', 'tmunit_kerja', 'tahun', 'prospektif'));
    }

    public function create()
    {
        if (!$this->request->ajax()) {
            return redirect(route('master.tahun'));
            exit();
        }
        $title = 'Tambah data master prospektif';
        $prospektif = tmprospektif::get();
        return view($this->view . 'form_add', compact('title', 'prospektif'));
    }

    public function setActive()
    {
        if ($this->request->ajax()) {

            $id = $this->request->id;
            $active = $this->request->active;
            $data = tmprospektif_sub::find($id);
            $data->active = $active;
            $data->save();

            return response()->json([
                'msg' => 'Set tahun berhasil disimpan ',
            ]);
        }
    }

    public function api()
    {
        $data = tmprospektif_sub::select(
            'tmprospektif_sub.id',
            'tmprospektif_sub.nama_prospektif_sub',
            'tmprospektif_sub.kode_sub',
            'tmprospektif_sub.created_at',
            'tmprospektif_sub.updated_at',
            'tmprospektif_sub.user_id',
            'tmprospektif_sub.tmlevel_id',
            'tmprospektif_sub.tmprospektif_id',
            'tmprospektif_sub.tmtahun_id',
            'tmprospektif.nama_prospektif',

            'tmtahun.tahun',
            'users.name'
        )->join('users', 'tmprospektif_sub.user_id', '=', 'users.id', 'left')
            ->join('tmprospektif', 'tmprospektif_sub.tmprospektif_id', '=', 'tmprospektif.id', 'left')
            ->join('tmtahun', 'tmtahun.id', '=', 'tmprospektif_sub.tmtahun_id', 'left');
        if ($this->request->tmtahun_id) {
            $data->where('tmprospektif.tmtahun_id', $this->request->tmtahun_id);
        }
        if ($this->request->tmunit_kerja) {
            $data->where('tmprospektif.tmlevel_id', $this->request->tmunit_kerja);
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
    public function store()
    {
        $this->request->validate([
            'tmtahun_id' => 'required',
            'kode_sub' => 'required',
        ]);
        try {
            $data = new tmprospektif_sub();
            $data->nama_prospektif_sub = $this->request->nama_prospektif_sub;
            $data->tmprospektif_id = $this->request->tmprospektif_id;
            $data->tmtahun_id = $this->request->tmtahun_id;
            $data->kode_sub = $this->request->kode_sub;
            $data->active = $this->request->active;
            $data->user_id = Auth::user()->id;
            $data->tmlevel_id = implode(',', $this->request->tmlevel_id);
            $data->target = $this->request->target;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah',
            ]);
        } catch (tmprospektif_sub $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }
    public function show($id)
    {

    }
    public function edit($id)
    {

        if (!$this->request->ajax()) {
            return response()->json([
                'data' => 'data null',
                'aspx' => 'response aspx fail ',
            ]);
        }
        $data = tmprospektif_sub::find($id);
        $prospektif = tmprospektif::get();

        $id = $data->id;
        $nama_prospektif_sub = $data->nama_prospektif_sub;
        $kode_sub = $data->kode_sub;
        $tmlevel_id = $data->tmlevel_id;
        $tmtahun_id = $data->tmtahun_id;
        $user_id = $data->user_id;
        $tmprospektif_id = $data->tmprospektif_id;
        $updated_at = $data->updated_at;
        $created_at = $data->created_at;
        $active = $data->active;
        $target = $data->target;

        $title = 'Edit data master prospektif';
        return view($this->view . 'form_edit', compact(
            'id',
            'nama_prospektif_sub',
            'kode_sub',
            'tmlevel_id',
            'tmtahun_id',
            'target',
            'user_id',
            'tmprospektif_id',
            'updated_at',
            'created_at',
            'active',
            'prospektif'
        ));
    }
    public function update($id)
    {
        $this->request->validate([
            'tmtahun_id' => 'required',
            'kode_sub' => 'required',
            'tmlevel_id' => 'required',
        ]);
        try {
            $data = tmprospektif_sub::find($id);
            $data->tmprospektif_id = $this->request->tmprospektif_id;
            $data->nama_prospektif_sub = $this->request->nama_prospektif_sub;
            $data->tmtahun_id = $this->request->tmtahun_id;
            $data->kode_sub = $this->request->kode_sub;
            $data->active = $this->request->active;
            $data->tmlevel_id = implode(',', $this->request->tmlevel_id);
            $data->user_id = AUth::user()->id;
            $data->target = $this->request->target;

            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil simpan',
            ]);
        } catch (tmprospektif_sub $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }
    public function destroy()
    {
        try {
            if (is_array($this->request->id)) {
                tmprospektif_sub::whereIn('id', $this->request->id)->delete();
            } else {
                tmprospektif_sub::whereid($this->request->id)->delete();
            }

            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (tmprospektif_sub $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
