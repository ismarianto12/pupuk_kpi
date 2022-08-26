<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmjenis_pengukuran extends Model
{
    use HasFactory;
    protected $table = 'tmjenis_pengukuran';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
