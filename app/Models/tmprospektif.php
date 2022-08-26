<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmprospektif extends Model
{
    use HasFactory;
    protected $table = 'tmprospektif';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
