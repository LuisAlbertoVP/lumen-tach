<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Modulo;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function __construct() {}

    public function getAll() {
        return Modulo::where('estado', 1)->orderBy('descripcion')->get();
    }
}