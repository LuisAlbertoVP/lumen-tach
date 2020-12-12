<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct() {}

    public function getAll(Request $request) {
        $condition = $request->criterio2 ? ' between ? and ?' : ' like ?';
        $filtro = $request->filtro.$condition;
        $criterio = $request->criterio2 ? array($request->criterio1, $request->criterio2) : array('%'.$request->criterio1.'%');
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $marcas = array(
            'marcas' => Marca::selectRaw('id, descripcion, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
                'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->whereRaw($filtro, $criterio)->orderBy($request->orden, $request->direccion)
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Marca::where('estado_tabla', 1)->whereRaw($filtro, $criterio)->whereIn('estado', $estado)->count()
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