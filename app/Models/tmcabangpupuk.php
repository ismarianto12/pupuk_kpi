<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmcabangpupuk extends Model
{
    use HasFactory;
    protected $table = 'tmcabang_pupuk';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}
