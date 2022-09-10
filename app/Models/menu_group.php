<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu_group extends Model
{
    use HasFactory;
    protected $table = 'menu_group';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];

}
