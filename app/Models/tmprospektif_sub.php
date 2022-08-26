<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmprospektif_sub extends Model
{
    protected $table = 'tmprospektif_sub';
    use HasFactory;
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}
