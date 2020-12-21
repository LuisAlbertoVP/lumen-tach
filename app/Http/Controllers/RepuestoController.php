<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Repuesto;
use App\Models\Marca;
use App\Models\Categoria;
use Illuminate\Http\Request;

class RepuestoController extends Controller
{
    public function __construct() {}

    private function isRelation($filtro) {
        return $filtro == 'proveedor' || $filtro == 'marca' || $filtro == 'categoria';
    }

    private function applyFilter($query, $filtro) {
        if($filtro['isRelation']) {
            if($filtro['condicion'] != 'between') {
                $condicion = function($query) use($filtro) { $query->where('descripcion', $filtro['condicion'], $filtro['criterio1']); };
            } else {
                $condicion = function($query) use($filtro) { $query->whereBetween('descripcion', array($filtro['criterio1'], $filtro['criterio2'])); };
            }
            $query->whereHas($filtro['columna'], $condicion);
        } else {
            if($filtro['condicion'] != 'between') {
                $query->where($filtro['columna'], $filtro['condicion'], $filtro['criterio1']); 
            } else {
                $query->whereBetween($filtro['columna'], array($filtro['criterio1'], $filtro['criterio2'])); 
            }
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
        $repuesto = array(
            "repuestos" => Repuesto::with(['marca', 'categoria'])->selectRaw('id, codigo, modelo, fecha, stock, precio, descripcion, estado, marca_id, categoria_id,'.
                    'usr_ing as usrIngreso, fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            "total" => Repuesto::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)->count()
        );
        return $repuesto;
    }

    public function getById(Request $request, $id) {
        return array(
            "repuesto" => $id != 'nuevo' ? Repuesto::with(['marca', 'categoria'])->selectRaw('id, codigo, modelo, fecha, stock, precio, descripcion, marca_id,'. 
                    'categoria_id, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->where('estado', 1)->where('id', $id)->first() : null,
            "marcas" => Marca::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->get(),
            "categorias" => Categoria::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->get()
        );
    }

    public function insertOrUpdate(Request $request) {
        $json = $request->getContent();
        DB::select('CALL AddRepuesto(?)', [$json]);
        return response()->json('Repuesto actualizado correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $repuesto = json_decode($request->getContent());
        $status = Repuesto::where('id', $id)->update(['estado' => $repuesto->estado]);
        if($status == 1)
            return response()->json($repuesto->estado == 1 ? 'Repuesto habilitado' : 'Repuesto deshabilitado', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Repuesto::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Repuesto eliminado correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}