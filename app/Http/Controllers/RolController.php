<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Rol;
use App\Models\Modulo;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function __construct() {}

    private function applyFilter($query, $filtro) {
        if($filtro['condicion'] != 'between') {
            $query->where($filtro['columna'], $filtro['condicion'], $filtro['criterio1']); 
        } else {
            $query->whereBetween($filtro['columna'], array($filtro['criterio1'], $filtro['criterio2'])); 
        }
    }

    private function forFilters($query, $filtros) {
        foreach($filtros as $filtro) {
            $this->applyFilter($query, $filtro); 
        }
    }

    public function getAll(Request $request) {
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $condition = function($query) use($request) { $this->forFilters($query, $request->filtros); };
        $rol = array(
            'roles' => Rol::with(['modulos'])->selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso,'.
                    'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Rol::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)->count()
        );
        return $rol;
    }

    public function getById(Request $request, $id) {
        return $id != 'nuevo' ? Rol::with(['modulos'])->selectRaw('id, descripcion')
            ->where('estado_tabla', 1)->where('id', $id)->first() : null;
    }

    public function insertOrUpdate(Request $request) {
        DB::beginTransaction();
        try{
            DB::select('CALL AddRol(?)', [$request->getContent()]);
            DB::commit();
            return response()->json('Rol actualizado correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Rol no actualizado', 500);
        }
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