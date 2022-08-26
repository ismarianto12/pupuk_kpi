<?php

namespace App\Http\Controllers;

use App\Models\menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
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
        $this->view    = '.menu.';
        $this->route   = 'master.menu.';
    }


    public function index()
    {
        $title = 'Master data menu';
        $record = menu::select('*')->orderBy('urutan')->get();
        return view($this->view . 'index', compact(
            'title',
            'record'
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
        $title = 'menu Akses User';
        return view($this->view . 'form_add', compact('title'));
    }

    public function getbangunan()
    {
        $title = 'Master data menu';
        return view($this->view . 'select', compact('title'));
    }

    public function api()
    {
        $data = menu::get();
        return DataTables::of($data)
            ->editColumn('id', function ($p) {
                return "<input type='checkbox' name='cbox[]' value='" . $p->id . "' />";
            })
            ->editColumn('action', function ($p) {
                return  '<a href="" class="btn btn-warning btn-xs" id="edit" data-id="' . $p->id . '"><i class="fa fa-edit"></i>Edit </a> ';
            }, true)
            ->editColumn('nama', function ($p) {
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
            'label' => 'required',
            'link' => 'required',
        ]);
        try {
            $data = new menu();

            $data->id_parent = ($this->request->id_parent) ? $this->request->id_parent : 0;
            $data->nama_menu = $this->request->label;
            $data->icon = $this->request->icon;
            $data->link = $this->request->link;
            $data->aktif = 'Ya';
            $data->urutan = ($this->request->urutan) ? $this->request->urutan : 0;
            $data->position = ($this->request->position) ? $this->request->position : 'Bottom';
            $data->level = $this->request->level;
            $data->user_id = Auth::user()->id;
            $data->save();

            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil ditambah'
            ]);
        } catch (\menu $t) {
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
            'menu_kode' => 'required',
            'menu' => 'required',
            'user_id' => 'required'
        ]);
        try {
            $data = new menu();
            $data->menu_kode = $this->request->menu_kode;
            $data->menu = $this->request->menu;
            $data->user_id = $this->request->user_id;
            $data->find($id)->save();
            return response()->json([
                'status' => 1,
                'msg' => 'data berhasil dtambah'
            ]);
        } catch (\menu $t) {
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
                menu::whereIn('id_menu', $this->request->id)->delete();
            else
                menu::where('id_menu', $this->request->id)->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'Data berhasil di hapus'
            ]);
        } catch (menu $t) {
            return response()->json([
                'status' => 2,
                'msg' => $t
            ]);
        }
    }

    // functiion data update adn save every function in actrion on cascade
    function save_postion()
    {
        $data = json_decode($this->request->data);

        function parseJsonArray($jsonArray, $parentID = 0)
        {
            $return = array();
            foreach ($jsonArray as $subArray) {
                $returnSubSubArray = array();
                if (isset($subArray->children)) {
                    $returnSubSubArray = parseJsonArray($subArray->children, $subArray->id);
                }

                $return[] = array('id' => $subArray->id, 'parentID' => $parentID);
                $return = array_merge($return, $returnSubSubArray);
            }
            return $return;
        }
        $readbleArray = parseJsonArray($data);

        $i = 0;
        foreach ($readbleArray as $row) {
            $i++;

            menu::where('id_menu', $row['id'])
                ->update([
                    'id_parent' => $row['parentID'],
                    'urutan' => $i
                ]);
        }
    }
}
