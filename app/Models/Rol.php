<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = "rol";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function usuarios() {
        return $this->belongsToMany(User::class);
    }

    public function modulos() {
        return $this->belongsToMany(Modulo::class);
    }
}