<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = "marca";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function repuestos() {
        return $this->hasMany(Repuesto::class, 'marca_id', 'id')->selectRaw('id')
            ->where('estado_tabla', 1)->where('estado', 1);
    }
}