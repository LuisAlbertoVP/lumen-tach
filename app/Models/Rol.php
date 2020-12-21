<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = "rol";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function usuarios() {
        return $this->belongsToMany(User::class)->selectRaw('id, descripcion')
            ->where('estado_tabla', 1)->where('estado', 1);
    }

    public function modulos() {
        return $this->belongsToMany(Modulo::class)->selectRaw('id, descripcion');
    }
}