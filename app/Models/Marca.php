<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = "marca";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;
}