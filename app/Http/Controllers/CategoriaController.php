<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct() {}

    private function applyFilter($query, $filtro) {
        if($filtro['condicion'] == 'between') {
            $query->whereBetween($filtro['id'], array($filtro['criterio1'], $filtro['criterio2'])); 
        } else if($filtro['condicion'] == 'multiple') {
            $query->whereIn($filtro['id'], $filtro['criterios']); 
        } else {
            $query->where($filtro['id'], $filtro['condicion'], $filtro['criterio1']);
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
        $categorias = array(
            'categorias' => Categoria::selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
                    'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Categoria::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)->count()
        );
        return $categorias;
    }

    public function insertOrUpdate(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL AddCategoria(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('Categoría actualizada correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Categoría no actualizada', 500);
        }
    }

    public function setStatus(Request $request, $id) {
        $categoria = json_decode($request->getContent());
        $status = Categoria::where('id', $id)->update(['estado' => $categoria->estado]);
        if($status == 1)
            return response()->json($categoria->estado ? 'Categoría habilitada' : 'Categoría deshabilitada', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Categoria::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Categoría eliminada correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}