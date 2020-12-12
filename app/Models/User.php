<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $table = "user";

    protected $casts = [ 'id' => 'string' ];

    public $timestamps = false;

    public function roles() {
        return $this->belongsToMany(Rol::class, 'rol_user')->selectRaw('id, descripcion')
            ->where('estado_tabla', 1)->where('estado', 1);
    }
}