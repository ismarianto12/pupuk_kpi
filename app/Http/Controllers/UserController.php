<?php

namespace App\Http\Controllers;

use App\Models\Tmlevel;
use App\Models\tmunit;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
        $this->view = '.user.';
        $this->route = 'master.user.';
    }

    public function index()
    {
        $title = 'Master Data User';
        $level = Tmlevel::get();
        $unit = tmunit::get();

        return view($this->view . 'index', compact(
            'title',
            'level',
            'unit'
        ));
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
        $title = 'Tambah Data User';
        $level = Tmlevel::get();
        $unit = tmunit::get();
        return view($this->view . 'form_add', compact('title', 'level', 'unit'));
    }

    public function getbangunan()
    {
        $title = 'Master Data User';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {
        $tmunit_id = $this->request->tmunit_id;
        $tmlevel_id = $this->request->tmlevel_id;
        $data = User::select(
            'users.username',
            'users.password',
            'users.email',
            'users.name as nama_usernya',
            'users.tmunit_id',
            'users.tmlevel_id',
            'tmunit.unit_kode',
            'tmunit.nama as nama_unit',
            'tmunit.user_id',
            'tmunit.created_at',
            'tmunit.updated_at',
            'users.id as idusernya',
            'tmlevel.level_kode',
            'tmlevel.level',
            'tmlevel.user_id'

        )->join('tmlevel', 'users.tmlevel_id', '=', 'tmlevel.id')
            ->join('tmunit', 'users.tmunit_id', '=', 'tmunit.id');
        if ($tmlevel_id != '' && $tmunit_id != '') {
            $data->where('tmunit_id', $tmunit_id);
        } elseif ($tmunit_id) {
            $data->where('tmunit_id', $tmunit_id);
        } else if ($tmlevel_id) {
            $data->where('tmlevel_id', $tmunit_id);
        }
        $data->get();

        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->idusernya . "' />";
            })
            ->editColumn('action', function ($p) {
                return '<a href="" class="btn btn-sm btn-clean btn-icon" id="edit" data-id="' . $p->idusernya . '"><i class="fa fa-edit"></i>Edit </a> ';
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->nama_usernya;
            }, true)
            ->addIndexColumn()
            ->rawColumns(['usercreate', 'action', 'id'])
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
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'email' => 'required|unique:users,email',
            'tmunit_id' => 'required',
            'tmlevel_id' => 'required',
        ]);
        try {
            $cekname = tmunit::where('id', $this->request->tmunit_id)->first();
            $namanya = isset($cekname->nama) ? $cekname->nama : 'undefined';

            $data = new User;

            $data->username = $this->request->username;
            $data->password = bcrypt($this->request->password);
            $data->email = $this->request->email;
            $data->tmlevel_id = $this->request->tmlevel_id;
            $data->tmunit_id = $this->request->tmunit_id;
            $data->name = $namanya;
            
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data user berhasil dtambah',
            ]);
        } catch (User $t) {
            return response()->json([
                'status' => 1,
                'msg' => $t,
            ]);
        }
    }
    public function profile()
    {
        //
        $id = Auth::user()->id;

        $data = User::findOrfail($id);
        $name = $data->name;
        $username = $data->username;
        $email = $data->email;
        $id_lev = $data->tmlevel_id;
        $password = $data->password;
        $id = $data->id;
        $level = Tmlevel::get();
        $tmlevel_id = $data->tmlevel_id;
        $photo = $data->foto;

        $title = "Edit Password";
        return view($this->view . 'profil', compact(
            'id',
            'title',
            'name',
            'username',
            'email',
            'level',
            'password',
            'tmlevel_id',
            'photo',
            'id_lev'
        ));
    }

    public function profilesave()
    {
        $this->request->validate([
            'password' => 'required',
            'email' => 'required|unique:users,email',

        ]);

        try {
            $id = Auth::user()->id;

            $data = User::find($id);
            $data->password = bcrypt($this->request->password);
            $data->email = $this->request->email;
            $data->name = $this->request->name;
            $data->tmunit_id = $this->request->tmunit_id;

            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data user berhasil edit',
            ]);
        } catch (User $t) {
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

        if (!$this->request->ajax()) {
            return response()->json([
                'data' => 'data null',
                'aspx' => 'response aspx fail ',
            ]);
        }
        $data = User::findOrfail($id);
        $name = $data->name;
        $username = $data->username;
        $email = $data->email;
        $password = $data->password;
        $id = $data->id;
        $level = Tmlevel::get();
        $unit = tmunit::get();
        $tmunit_id = $data->tmunit_id;
        $tmlevel_id = $data->tmlevel_id;
        // dd($data);

        return view($this->view . 'form_edit', compact(
            'id',
            'name',
            'username',
            'unit',
            'email',
            'level',
            'password',
            'tmunit_id',
            'tmlevel_id'
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

        // dd($this->request->all());

        $this->request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'email' => 'required|unique:users,email',
            'tmunit_id' => 'required',
            'tmlevel_id' => 'required',
        ]);
        try {

            $cekname = tmunit::where('id', $this->request->tmunit_id)->first();
            $namanya = isset($cekname->nama) ? $cekname->nama : 'undefined';

            $data = User::find($id);
            $data->username = $this->request->username;
            $data->password = bcrypt($this->request->password);
            $data->email = $this->request->email;
            $data->tmlevel_id = $this->request->tmlevel_id;
            $data->tmunit_id = $this->request->tmunit_id;
            $data->name = $namanya;

            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data user berhasil update',
            ]);
        } catch (User $t) {
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
    public function destroy($id)
    {
        try {
            if (is_array($this->request->id)) {
                $datas = user::whereIn('id', $this->request->id);
                $datas->delete();
            } else {
                user::whereid($this->request->id)->delete();
            }
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus',
            ]);
        } catch (user $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t,
            ]);
        }
    }
}
