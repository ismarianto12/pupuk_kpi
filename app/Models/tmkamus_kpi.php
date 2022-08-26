<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmkamus_kpi extends Model
{
    use HasFactory;
    protected $table = 'tmkamus_kpi';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
