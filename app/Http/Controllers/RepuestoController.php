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

    public function getAll(Request $request) {
        $isRelation = $this->isRelation($request->filtro);
        $method = $isRelation ? 'whereHas' : 'whereRaw';
        $condition = $request->criterio2 ? ' between ? and ?' : ' like ?';
        $filtro = $isRelation ? $request->filtro : $request->filtro.$condition;
        $criterio = $request->criterio2 ? array($request->criterio1, $request->criterio2) : array('%'.$request->criterio1.'%');
        $criterio = $isRelation ? function($query) use($criterio) { return $query->where('descripcion', 'like', '%'.$criterio[0].'%'); } : $criterio;
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $repuesto = array(
            "repuestos" => Repuesto::with(['marca', 'categoria'])
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->$method($filtro, $criterio)->orderBy($request->orden, $request->direccion)
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            "total" => Repuesto::where('estado_tabla', 1)->$method($filtro, $criterio)->whereIn('estado', $estado)->count()
        );
        return $repuesto;
    }

    public function getById(Request $request, $id) {
        return array(
            "repuesto" => $id != 'nuevo' ? Repuesto::with(['marca', 'categoria'])
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