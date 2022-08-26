<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmjabatan extends Model
{
    use HasFactory;

    protected $table = 'tmjabatan';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
