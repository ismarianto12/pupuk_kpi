<%

   public function api()
    {
        $a = tmprospektif::select(
            'tmkamus_kpi.id as idnya',
            'tmkamus_kpi.nama_kpi',
            'tmkamus_kpi.definisi',
            'tmkamus_kpi.tujuan',

            'tmkamus_kpi.tmsatuan_id',
            'tmkamus_kpi.formula_penilaian',
            'tmkamus_kpi.target',
            'tmkamus_kpi.tmfrekuensi_id',
            'tmkamus_kpi.tmpolaritas_id',
            'tmkamus_kpi.unit_pemilik_kpi',
            'tmkamus_kpi.unit_pengelola_kpi',
            'tmkamus_kpi.sumber_data',
            'tmkamus_kpi.jenis_pengukuran',
            'tmkamus_kpi.created_at',
            'tmkamus_kpi.updated_at',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',

            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',

            'tmsatuan.kode',
            'tmsatuan.nama_satuan',

            'tmtahun.tahun',
            'tmunit.nama as nama_unit',

            'tmprospektif.id',
            'tmprospektif.nama_prospektif',

            'tmprospektif_sub.id',
            'tmprospektif_sub.nama_prospektif_sub'

        )
            ->join('tmprospektif_sub', 'tmprospektif_sub.tmprospektif_id', '=', 'tmprospektif.id', 'LEFT')
            ->join('tmkamus_kpi', 'tmprospektif.id', '=', 'tmkamus_kpi.tmprospektif_id')
            ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left');

        $b = tmprospektif_sub::select(
            'tmkamus_kpi.id as idnya',
            'tmkamus_kpi.nama_kpi',
            'tmkamus_kpi.definisi',
            'tmkamus_kpi.tujuan',

            'tmkamus_kpi.tmsatuan_id',
            'tmkamus_kpi.formula_penilaian',
            'tmkamus_kpi.target',
            'tmkamus_kpi.tmfrekuensi_id',
            'tmkamus_kpi.tmpolaritas_id',
            'tmkamus_kpi.unit_pemilik_kpi',
            'tmkamus_kpi.unit_pengelola_kpi',
            'tmkamus_kpi.sumber_data',
            'tmkamus_kpi.jenis_pengukuran',
            'tmkamus_kpi.created_at',
            'tmkamus_kpi.updated_at',
            'tmfrekuensi.nama_frekuensi',
            'tmfrekuensi.kode',

            'tmpolaritas.kode',
            'tmpolaritas.nama_polaritas',

            'tmsatuan.kode',
            'tmsatuan.nama_satuan',

            'tmtahun.tahun',
            'tmunit.nama as nama_unit',

            'tmprospektif.id',
            'tmprospektif.nama_prospektif',

            'tmprospektif_sub.id',
            'tmprospektif_sub.nama_prospektif_sub'
        )
            ->join('tmprospektif', 'tmprospektif_sub.tmprospektif_id', '=', 'tmprospektif.id', 'LEFT')
            ->join('tmkamus_kpi', 'tmprospektif_sub.id', '=', 'tmkamus_kpi.tmprospektif_sub_id')
            ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
            ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
            ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
            ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
            ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left');

        if ($this->request->tahun_id) {
            $b->where('tmkamus_kpi.tmtahun_id', $this->request->tahun_id);
        }
        if ($this->request->unit_id) {
            $b->where('tmkamus_kpi.unit_pengelola_kpi', $this->request->unit_id);
        }

        $sql = $b->union($a)->groupBy('tmkamus_kpi.id')->get();

        return \DataTables::of($sql)
            ->editColumn('status', function ($p) {
                $id = isset($p->tmtable_assigment_id) ? $p->tmtable_assigment_id : 0;
                return Properti_app::assignment_status($id);

            }, true)
            ->editColumn('action', function ($p) {
                return '<a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" id="create" data-id="' . $p->idnya . '"><i class="fa fa-plus"></i></a>
                        <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-minus"></i></a> ';
            }, true)
            ->editColumn('sub', function ($p) {
                return '';
            }, true)
            ->editColumn('total', function ($p) {
                return '';

            }, true)
            ->editColumn('kpi', function ($p) {
                return '';

            }, true)
            ->editColumn('tmtahun_id', function ($p) {
                return $p->tahun;
            }, true)
            ->editColumn('unit_pengelola_kpi', function ($p) {
                return $p->unit_pengelola_kpi;
            }, true)
            ->editColumn('nama', function ($p) {
                return $p->name;
            }, true)
            ->addIndexColumn()
            ->rawColumns(['usercrate', 'action', 'id', 'status',
                'sub',
                'kpi',
                'child_prospective',
                'total',

            ])->toJson();
    }


    //


      // public function api_app()
    // {
    //     $tmtahun_id = $this->request->tahun_id;
    //     $data = tmprospektif::select(
    //         'tmprospektif.nama_prospektif',
    //         'tmprospektif.kode',
    //         'tmprospektif.tmlevel_id',
    //         'tmprospektif.tmtahun_id',
    //         'tmprospektif.user_id',
    //         'tmprospektif.updated_at',
    //         'tmprospektif.created_at',
    //         'tmprospektif_sub.nama_prospektif_sub',
    //         'tmprospektif_sub.kode_sub',
    //         'tmprospektif_sub.tmlevel_id',
    //         'tmprospektif_sub.tmtahun_id',
    //         'tmprospektif_sub.user_id',
    //         'tmprospektif_sub.tmprospektif_id'
    //     )
    //         ->join('tmtable_assigment', 'tmtable_assigment.tmprospektif_id', '=', 'tmprospektif.id', 'left')
    //         ->join('tmprospektif_sub', 'tmprospektif_sub.tmprospektif_id', '=', 'tmprospektif.id', 'left');

    //     if ($this->request->tahun_id) {
    //         $data->where('tmprospektif.tmtahun_id', $tmtahun_id);
    //     }
    //     $sql = $data->get();
    //     return \DataTables::of($sql)
    //         ->editColumn('nama_kpi', function ($p) {
    //             return $p->nama_prospektif;
    //         })
    //         ->editColumn('nama_kpi', function ($p) {
    //             return $p->nama_prospektif;
    //         })
    //         ->editColumn('nama_satuan', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('target', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('nama_polaritas', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('sub', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('kpi', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('total', function ($p) {
    //             return '';
    //         })
    //         ->editColumn('action', function ($p) {
    //             return '<a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" id="create" data-id="' . $p->idnya . '"><i class="fa fa-plus"></i></a>
    //                     <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-minus"></i></a> ';
    //         }, true)
    //         ->addIndexColumn()
    //         ->rawColumns([
    //             'nama_kpi',
    //             'nama_satuan',
    //             'target',
    //             'nama_polaritas',
    //             'sub',
    //             'kpi',
    //             'total ',
    //             'action',
    //         ])->toJson();
    // }

    // public function api()
    // {
    //     $a = tmprospektif::distinct()->select(
    //         'tmkamus_kpi.id as idnya',
    //         'tmkamus_kpi.nama_kpi',
    //         'tmkamus_kpi.definisi',
    //         'tmkamus_kpi.tujuan',

    //         'tmkamus_kpi.tmsatuan_id',
    //         'tmkamus_kpi.formula_penilaian',
    //         'tmkamus_kpi.target',
    //         'tmkamus_kpi.tmfrekuensi_id',
    //         'tmkamus_kpi.tmpolaritas_id',
    //         'tmkamus_kpi.unit_pemilik_kpi',
    //         'tmkamus_kpi.unit_pengelola_kpi',
    //         'tmkamus_kpi.sumber_data',
    //         'tmkamus_kpi.jenis_pengukuran',
    //         'tmkamus_kpi.created_at',
    //         'tmkamus_kpi.updated_at',
    //         'tmfrekuensi.nama_frekuensi',
    //         'tmfrekuensi.kode',

    //         'tmpolaritas.kode',
    //         'tmpolaritas.nama_polaritas',

    //         'tmsatuan.kode',
    //         'tmsatuan.nama_satuan',

    //         'tmtahun.tahun',
    //         'tmunit.nama as nama_unit',

    //         'tmprospektif.id',
    //         'tmprospektif.nama_prospektif',
    //         'tmprospektif_sub.nama_prospektif_sub'

    //     )
    //         ->join('tmprospektif_sub', 'tmprospektif_sub.tmprospektif_id', '=', 'tmprospektif.id', 'left')
    //         ->join('tmkamus_kpi', 'tmkamus_kpi.tmprospektif_id', '=', 'tmprospektif.id', 'left')
    //         ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
    //         ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
    //         ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
    //         ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
    //         ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left');

    //     $b = tmprospektif_sub::distinct()->select(
    //         'tmkamus_kpi.id as idnya',
    //         'tmkamus_kpi.nama_kpi',
    //         'tmkamus_kpi.definisi',
    //         'tmkamus_kpi.tujuan',

    //         'tmkamus_kpi.tmsatuan_id',
    //         'tmkamus_kpi.formula_penilaian',
    //         'tmkamus_kpi.target',
    //         'tmkamus_kpi.tmfrekuensi_id',
    //         'tmkamus_kpi.tmpolaritas_id',
    //         'tmkamus_kpi.unit_pemilik_kpi',
    //         'tmkamus_kpi.unit_pengelola_kpi',
    //         'tmkamus_kpi.sumber_data',
    //         'tmkamus_kpi.jenis_pengukuran',
    //         'tmkamus_kpi.created_at',
    //         'tmkamus_kpi.updated_at',
    //         'tmfrekuensi.nama_frekuensi',
    //         'tmfrekuensi.kode',

    //         'tmpolaritas.kode',
    //         'tmpolaritas.nama_polaritas',

    //         'tmsatuan.kode',
    //         'tmsatuan.nama_satuan',

    //         'tmtahun.tahun',
    //         'tmunit.nama as nama_unit',

    //         'tmprospektif_sub.id',
    //         'tmprospektif_sub.nama_prospektif_sub',
    //         'tmprospektif.nama_prospektif'
    //     )

    //         ->join('tmkamus_kpi', 'tmkamus_kpi.tmprospektif_sub_id', '=', 'tmprospektif_sub.id', 'left')
    //         ->join('tmprospektif', 'tmprospektif.id', '=', 'tmprospektif_sub.tmprospektif_id', 'left')

    //         ->join('tmfrekuensi', 'tmkamus_kpi.tmfrekuensi_id', '=', 'tmfrekuensi.id', 'left')
    //         ->join('tmpolaritas', 'tmkamus_kpi.tmsatuan_id', '=', 'tmpolaritas.id', 'left')
    //         ->join('tmtahun', 'tmkamus_kpi.tmtahun_id', '=', 'tmtahun.id', 'left')
    //         ->join('tmunit', 'tmkamus_kpi.unit_pengelola_kpi', '=', 'tmunit.id', 'left')
    //         ->join('tmsatuan', 'tmkamus_kpi.tmsatuan_id', '=', 'tmsatuan.id', 'left');

    //     // if ($this->request->tahun_id) {
    //     //     $b->where('tmkamus_kpi.tmtahun_id', $this->request->tahun_id);
    //     // }
    //     // if ($this->request->unit_id) {
    //     //     $b->where('tmkamus_kpi.unit_pengelola_kpi', $this->request->unit_id);
    //     // }

    //     $sql = $a->union($b)->get();

    //     return \DataTables::of($sql)
    //         ->editColumn('status', function ($p) {
    //             $id = isset($p->tmtable_assigment_id) ? $p->tmtable_assigment_id : 0;
    //             return Properti_app::assignment_status($id);

    //         }, true)
    //         ->editColumn('action', function ($p) {
    //             return '<a href="" class="btn btn-sm btn-clean btn-icon" title="Assignemn data as unit kerja" id="create" data-id="' . $p->idnya . '"><i class="fa fa-plus"></i></a>
    //                     <a href="" class="btn btn-sm btn-clean btn-icon" id="delete"  title="Detail data" data-id="' . $p->idnya . '"><i class="fa fa-minus"></i></a> ';
    //         }, true)
    //         ->editColumn('sub', function ($p) {
    //             return '<input type="text" name="kpi" value="" class="form-control" />';
    //         }, true)
    //         ->editColumn('total', function ($p) {
    //             return '<input type="text" name="kpi" value="" class="form-control" />';

    //         }, true)
    //         ->editColumn('kpi', function ($p) {
    //             return '<input type="text" name="kpi" value="" class="form-control" />';

    //         }, true)
    //         ->editColumn('tmtahun_id', function ($p) {
    //             return $p->tahun;
    //         }, true)
    //         ->editColumn('unit_pengelola_kpi', function ($p) {
    //             return $p->unit_pengelola_kpi;
    //         }, true)
    //         ->editColumn('nama', function ($p) {
    //             return $p->name;
    //         }, true)
    //         ->addIndexColumn()
    //         ->rawColumns(['usercrate', 'action', 'id', 'status',
    //             'sub',
    //             'kpi',
    //             'child_prospective',
    //             'total',

    //         ])->toJson();
    // }




    //

     public function index()
    {
        $prospektif = tmprospektif::get();
        $idx = 0;
        foreach ($prospektif as $prospektiff) {
            $dataset[$idx]['id']['val'] = $prospektiff->id;
            $dataset[$idx]['status']['val'] = 'prospektiff';

            $dataset[$idx]['parent']['val'] = true;
            $dataset[$idx]['nama_kpi']['val'] = '<b>' . $prospektiff->nama_prospektif . '</b>';
            $dataset[$idx]['nama_satuan']['val'] = '';
            $dataset[$idx]['target']['val'] = '';
            $dataset[$idx]['nama_polaritas']['val'] = '';
            $dataset[$idx]['sub']['val'] = '';
            $dataset[$idx]['kpi']['val'] = '';
            $dataset[$idx]['total']['val'] = '';

            $fkamus = tmtable_assigment::join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')->where('tmtable_assigment.tmprospektif_id', $prospektiff->id)->get();
            $idx++;

            $a = 1;
            foreach ($fkamus as $klamuss) {
                $dataset[$idx]['id']['val'] = $klamuss->id;
                $dataset[$idx]['status']['val'] = '';

                $dataset[$idx]['parent']['val'] = false;
                $dataset[$idx]['nama_kpi']['val'] = $klamuss->nama_kpi;
                $dataset[$idx]['nama_satuan']['val'] = '';
                $dataset[$idx]['target']['val'] = '';
                $dataset[$idx]['nama_polaritas']['val'] = '';
                $dataset[$idx]['sub']['val'] = '';
                $dataset[$idx]['kpi']['val'] = '';
                $dataset[$idx]['total ']['val'] = '';
                $idx++;
                $a++;
            }
            // end get prospektif
            $c = 1;
            $tmprospektif_sub = tmprospektif_sub::where('tmprospektif_id', $prospektiff->id)->get();
            foreach ($tmprospektif_sub as $tmprospektif_subs) {
                $dataset[$idx]['id']['val'] = $tmprospektif_subs->id;
                $dataset[$idx]['status']['val'] = 'subprospektiff';
                $dataset[$idx]['parent']['val'] = true;
                $dataset[$idx]['nama_kpi']['val'] = '<b>' . $tmprospektif_subs->nama_prospektif_sub . '</b>';
                $dataset[$idx]['nama_satuan']['val'] = '';
                $dataset[$idx]['target']['val'] = '';
                $dataset[$idx]['nama_polaritas']['val'] = '';
                $dataset[$idx]['sub']['val'] = '';
                $dataset[$idx]['kpi']['val'] = '';
                $dataset[$idx]['total ']['val'] = '';
                $c++;
                $idx++;

                $kamus = tmtable_assigment::join('tmkamus_kpi', 'tmkamus_kpi.id', '=', 'tmtable_assigment.tmkamus_kpi_id')->where('tmtable_assigment.tmprospektif_sub_id', $tmprospektif_subs->id)->get();
                $d = 1;
                foreach ($kamus as $kamuss) {
                    $dataset[$idx]['id']['val'] = $kamuss->id;
                    $dataset[$idx]['status']['val'] = '';
                    $dataset[$idx]['parent']['val'] = false;
                    $dataset[$idx]['nama_kpi']['val'] = $kamuss->nama_kpi;
                    $dataset[$idx]['nama_satuan']['val'] = '';
                    $dataset[$idx]['target']['val'] = '';
                    $dataset[$idx]['nama_polaritas']['val'] = '';
                    $dataset[$idx]['sub']['val'] = '';
                    $dataset[$idx]['kpi']['val'] = '';
                    $dataset[$idx]['total ']['val'] = '';
                    $idx++;
                    $d = 1;

                }

                $idx++;
            }

            $idx++;
        }
        return view($this->view . 'parameter_table', [
            'render' => $dataset,
            'unit' => tmunit::get(),
            'tahun' => tmtahun::get(),

        ]);
    }