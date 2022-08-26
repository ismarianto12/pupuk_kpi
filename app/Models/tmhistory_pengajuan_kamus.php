<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmhistory_pengajuan_kamus extends Model
{
    use HasFactory;

    protected $table = 'tmhistory_pengajuan_kamus';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}
