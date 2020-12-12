<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = "repuesto";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function marca() {
        return $this->hasOne(Marca::class, 'id', 'marca_id')->selectRaw('id, descripcion')
            ->where('estado_tabla', 1)->where('estado', 1);
    }

    public function categoria() {
        return $this->hasOne(Categoria::class, 'id', 'categoria_id')->selectRaw('id, descripcion')
            ->where('estado_tabla', 1)->where('estado', 1);
    }
}