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

    private function applyCondition($query, $filtro, $isRelation = false) {
        $column = $isRelation ? 'descripcion' : $filtro['columna'];
        if($filtro['condicion'] == 'between') {
            $query->whereBetween($column, array($filtro['criterio1'], $filtro['criterio2'])); 
        } else if($filtro['condicion'] == 'multiple') {
            $query->whereIn($column, $filtro['criterios']); 
        } else {
            $query->where($column, $filtro['condicion'], $filtro['criterio1']);
        }
    }

    private function applyFilter($query, $filtro) {
        if($filtro['isRelation']) {
            $condicion = function($query) use($filtro) { $this->applyCondition($query, $filtro, true); };
            $query->whereHas($filtro['columna'], $condicion);
        } else {
            $this->applyCondition($query, $filtro);
        }
    }

    private function forFilters($query, $filtros) {
        foreach($filtros as $filtro) {
            $this->applyFilter($query, $filtro); 
        }
    }

    private function sortRelations($sort) {
        switch($sort) {
            case 'categoria': return Categoria::select('descripcion')->whereColumn('categoria.id', 'repuesto.categoria_id');
            case 'marca': return Marca::select('descripcion')->whereColumn('marca.id', 'repuesto.marca_id');
            default: return $sort;
        }
    }

    public function getAll(Request $request) {
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $condicion = function($query) use($request) { $this->forFilters($query, $request->filtros); };
        $sort = $this->sortRelations($request->orden['activo']);
        $repuesto = array(
            "repuestos" => Repuesto::with(['marca', 'categoria'])->selectRaw('id, codigo, modelo, fecha, stock, precio, descripcion, estado, marca_id, categoria_id,'.
                    'usr_ing as usrIngreso, fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)
                ->orderBy($sort, $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            "total" => Repuesto::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)->count()
        );
        return $repuesto;
    }

    public function getForm(Request $request) {
        return array(
            "marcas" => Marca::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->orderBy('descripcion', 'asc')->get(),
            "categorias" => Categoria::selectRaw('id, descripcion')->where('estado_tabla', 1)->where('estado', 1)->orderBy('descripcion', 'asc')->get()
        );
    }

    public function insertOrUpdate(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL AddRepuesto(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('Repuesto actualizado correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Repuesto no actualizado', 500);
        }
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