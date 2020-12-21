<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
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
        $marcas = array(
            'marcas' => Marca::selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
                    'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Marca::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)->count()
        );
        return $marcas;
    }

    public function insertOrUpdate(Request $request) {
        $json = $request->getContent();
        DB::select('CALL AddMarca(?)', [$json]);
        return response()->json('Marca actualizada correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $marca = json_decode($request->getContent());
        $status = Marca::where('id', $id)->update(['estado' => $marca->estado]);
        if($status == 1)
            return response()->json($marca->estado == 1 ? 'Marca habilitada' : 'Marca deshabilitada', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Marca::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Marca eliminada correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}