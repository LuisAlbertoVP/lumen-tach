<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct() {}

    public function getRolUser($id) {
        return User::with(['roles', 'roles.modulos'])->selectRaw('id')->where('id', $id)->first();
    }

    private function applyFilter($query, $filtro) {
        if($filtro['condicion'] == 'between') {
            $query->whereBetween($filtro['columna'], array($filtro['criterio1'], $filtro['criterio2'])); 
        } else if($filtro['condicion'] == 'multiple') {
            $query->whereIn($filtro['columna'], $filtro['criterios']); 
        } else {
            $query->where($filtro['columna'], $filtro['condicion'], $filtro['criterio1']);
        }
    }

    private function forFilters($query, $filtros) {
        foreach($filtros as $filtro) {
            $this->applyFilter($query, $filtro); 
        }
    }

    public function getAll(Request $request) {
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $condicion = function($query) use($request) { $this->forFilters($query, $request->filtros); };
        $user = array(
            'usuarios' => User::with(['roles'])->selectRaw('id, nombre_usuario as nombreUsuario, nombres, cedula, direccion, telefono,'. 
                    'celular, fecha_nacimiento as fechaNacimiento, correo, fecha_contratacion as fechaContratacion, salario, estado,'. 
                    'usr_ing as usrIngreso, fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => User::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)->count()
        );
        return $user;
    }

    public function getForm(Request $request) {
        return array(
            "roles" => Rol::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->orderBy('descripcion', 'asc')->get()
        );
    }

    public function insertOrUpdate(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL AddUsuario(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('Usuario actualizado correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Usuario no actualizado', 500);
        }
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