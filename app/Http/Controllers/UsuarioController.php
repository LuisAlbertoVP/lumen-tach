<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Rol;
use App\Models\Permiso;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function __construct() {}

    public function login(Request $request) {
        $user = json_decode($request->getContent());
        $user = User::with(['roles'])->selectRaw('id, nombre_usuario as nombreUsuario, nombres, estado')
            ->where('nombre_usuario', $user->nombreUsuario)->where('clave', $user->clave)->first();
        if($user) {
            if($user->estado == 1) {
                $expiration = time() + (60*(60*12));
                $payload = array("iss" => "localhost", "aud" => "localhost", "exp" => $expiration,
                    "data" => [ "id" => "luisv397" ]);
                $jwt = JWT::encode($payload, env('TOKEN'));
                $user->token = array('id' => $jwt, 'expiration' => $expiration);
                return $user;
            }
            return response('Usuario desactivado', 401);
        }
        return response('Las credenciales de acceso son incorrectas', 403);
    }

    public function getAll(Request $request) {
        $condition = $request->criterio2 ? ' between ? and ?' : ' like ?';
        $filtro = $request->filtro.$condition;
        $criterio = $request->criterio2 ? array($request->criterio1, $request->criterio2) : array('%'.$request->criterio1.'%');
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $user = array(
            'usuarios' => User::with(['roles'])->selectRaw('id, nombre_usuario as nombreUsuario, nombres, cedula, direccion, telefono,'. 
                'celular, fecha_nacimiento as fechaNacimiento, correo, fecha_contratacion as fechaContratacion, salario, estado,'. 
                'usr_ing as usrIngreso, fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->whereRaw($filtro, $criterio)->orderBy($request->orden, $request->direccion)
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => User::where('estado_tabla', 1)->whereRaw($filtro, $criterio)->whereIn('estado', $estado)->count()
        );
        return $user;
    }

    public function getById(Request $request, $id) {
        return array(
            "usuario" => $id != 'nuevo' ? User::with(['roles'])->selectRaw('id, nombre_usuario as nombreUsuario, nombres, cedula, direccion, telefono,'. 
                'celular, fecha_nacimiento as fechaNacimiento, correo, fecha_contratacion as fechaContratacion, salario')
                ->where('estado_tabla', 1)->where('id', $id)->first() : null,
            "roles" => Rol::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->get()
        );
    }

    public function insertOrUpdate(Request $request) {
        $json = $request->getContent();
        $usuario = json_decode($json, true);
        Permiso::where('id', $usuario->id)->whereNotIn('rol_id', $usuario->roles)->delete();
        DB::select('CALL AddUsuario(?)', [$json]);
        return response()->json('Usuario actualizado correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $user = json_decode($request->getContent());
        $status = User::where('id', $id)->update(['estado' => $user->estado]);
        if($status == 1)
            return response()->json($user->estado == 1 ? 'Usuario habilitado' : 'Usuario deshabilitado', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = User::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Usuario eliminado correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}