<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmpengajuan_kamus_kpi extends Model
{
    use HasFactory;

    // protected $primaryKey = 'id';
    protected $table = 'tmpengajuan_kamus_kpi';
    public $datetime = false;
    protected $guarded = [];

    // protected $fillable = [
    //     'id',
    //     'nama_kpi',
    //     'definisi',
    //     'tujuan',
    //     'tmsatuan_id',
    //     'formula_penilaian',
    //     'target',
    //     'tmfrekuensi_id',
    //     'tmpolaritas_id',
    //     'unit_pemilik_kpi',
    //     'unit_pengelola_kpi',
    //     'sumber_data',
    //     'jenis_pengukuran',
    //     'tmtahun_id',
    //     'approved',
    //     'created_at',
    //     'updated_at',
    //     'assign_to',
    //     'status',
    //     'catatan',
    //     'user_id',
    //     'from',

    // ];
}
