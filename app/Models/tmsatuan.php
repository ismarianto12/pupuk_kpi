<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmsatuan extends Model
{
    use HasFactory;
    protected $table = 'tmsatuan';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
