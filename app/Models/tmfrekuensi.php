<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmfrekuensi extends Model
{
    use HasFactory;
    protected $table = 'tmfrekuensi';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
