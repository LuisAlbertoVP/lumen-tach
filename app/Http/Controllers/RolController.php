<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Rol;
use App\Models\Modulo;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function __construct() {}

    public function getAll(Request $request) {
        $condition = $request->criterio2 ? ' between ? and ?' : ' like ?';
        $filtro = $request->filtro.$condition;
        $criterio = $request->criterio2 ? array($request->criterio1, $request->criterio2) : array('%'.$request->criterio1.'%');
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $rol = array(
            'roles' => Rol::with(['modulos'])->selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso,'.
                'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->whereRaw($filtro, $criterio)->orderBy($request->orden, $request->direccion)
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Rol::where('estado_tabla', 1)->whereRaw($filtro, $criterio)->whereIn('estado', $estado)->count()
        );
        return $rol;
    }

    public function getById(Request $request, $id) {
        return array(
            "rol" => $id != 'nuevo' ? Rol::with(['modulos'])->selectRaw('id, descripcion')
                ->where('estado_tabla', 1)->where('id', $id)->first() : null,
            "modulos" => Modulo::selectRaw('id, descripcion')->where('estado', 1)->get()
        );
    }

    public function insertOrUpdate(Request $request) {
        $json = $request->getContent();
        DB::select('CALL AddRol(?)', [$json]);
        return response()->json('Rol actualizado correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $rol = json_decode($request->getContent());
        $status = Rol::where('id', $id)->update(['estado' => $rol->estado]);
        if($status == 1)
            return response()->json($rol->estado == 1 ? 'Rol habilitado' : 'Rol deshabilitado', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Rol::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Rol eliminado correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}