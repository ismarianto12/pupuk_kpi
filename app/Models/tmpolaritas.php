<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tmpolaritas extends Model
{
    use HasFactory;
    protected $table = 'tmpolaritas';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}
