<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = "proveedor";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;
}