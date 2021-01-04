<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;

class PrincipalController extends Controller
{
    public function __construct() {}

    public function getById($id) {
        return User::selectRaw('id, nombre_usuario as nombreUsuario, nombres, cedula, direccion, telefono,'. 
                'celular, fecha_nacimiento as fechaNacimiento, correo')
            ->where('estado_tabla', 1)->where('estado', 1)->where('id', $id)->first();
    }

    public function update(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL UpdateAccount(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('Cuenta actualizada correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Cuenta no actualizada', 500);
        }
    }
}