<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function __construct() {}

    public function login(Request $request) {
        $user = json_decode($request->getContent());
        $user = User::selectRaw('id, nombre_usuario as nombreUsuario, nombres, estado')
            ->where('nombre_usuario', $user->nombreUsuario)->where('clave', $user->clave)->first();
        if($user) {
            if($user->estado == 1) {
                $expiration = time() + (60*(60*12));
                $payload = array("iss" => "localhost", "aud" => "localhost", "exp" => $expiration,
                    "data" => [ "id" => $user->id ]);
                $jwt = JWT::encode($payload, env('TOKEN'));
                $user->token = array('id' => $jwt, 'expiration' => $expiration);
                return $user;
            }
            return response()->json('Usuario desactivado', 401);
        }
        return response()->json('Las credenciales de acceso son incorrectas', 403);
    }

    public function insert(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL AddAccount(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('La cuenta ha sido creada satisfactoriamente, solicite su activación', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('La cuenta no ha sido creada', 500);
        }
    }
}