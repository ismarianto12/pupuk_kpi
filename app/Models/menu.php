<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $protected = 'id_menu';
    public $incrementing = false;
    public $datetime = false;
    protected $guarded = [];
}