<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
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
        $categorias = array(
            'categorias' => Categoria::selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
                    'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Categoria::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)->count()
        );
        return $categorias;
    }

    public function insertOrUpdate(Request $request) {
        DB::connection()->getPdo()->prepare('CALL AddCategoria(?)')->execute([$request->getContent()]);
        return response()->json('Categoría actualizada correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $categoria = json_decode($request->getContent());
        $status = Categoria::where('id', $id)->update(['estado' => $categoria->estado]);
        if($status == 1)
            return response()->json($categoria->estado == 1 ? 'Categoría habilitada' : 'Categoría deshabilitada', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Categoria::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Categoría eliminada correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}