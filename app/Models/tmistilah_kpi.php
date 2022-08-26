<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmistilah_kpi extends Model
{
    use HasFactory;
    protected $table = 'tmistilah_kpi';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
