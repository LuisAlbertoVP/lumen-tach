<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = "categoria";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function repuestos() {
        return $this->hasMany(Repuesto::class, 'categoria_id', 'id')->selectRaw('id')
            ->where('estado_tabla', 1)->where('estado', 1);
    }
}