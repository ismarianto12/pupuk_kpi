<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmkamus_kpi_sub extends Model
{
    use HasFactory;
    protected $table = 'tmkamus_kpi_sub';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
