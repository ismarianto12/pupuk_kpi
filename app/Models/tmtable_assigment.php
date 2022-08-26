<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmtable_assigment extends Model
{
    use HasFactory;
    protected $table = 'tmtable_assigment';
    // public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}

