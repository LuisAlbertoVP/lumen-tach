<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = "venta";

    protected $casts = [ 'id' => 'string' ];

    public function repuestos() {
        return $this->hasMany(Repuesto::class)
            ->selectRaw('id,descripcion,repuesto_id');
    }
}