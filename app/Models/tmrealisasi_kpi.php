<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmrealisasi_kpi extends Model
{
    use HasFactory;
    protected $table = 'tmrealisasi_kpi';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}
