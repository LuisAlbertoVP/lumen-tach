<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
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
        $proveedores = array(
            'proveedores' => Proveedor::selectRaw('id, descripcion, convenio, telefono, direccion, tipo_proveedor as tipoProveedor,'.
                    'contacto, telefono_contacto as telefonoContacto, correo_contacto as correoContacto, estado, usr_ing as usrIngreso,'. 
                    'fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Proveedor::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condicion)->count()
        );
        return $proveedores;
    }

    public function insertOrUpdate(Request $request) {
        DB::beginTransaction();
        try {
            DB::connection()->getPdo()->prepare('CALL AddProveedor(?)')->execute([$request->getContent()]);
            DB::commit();
            return response()->json('Proveedor actualizado correctamente', 200);
        } catch(\Exception $ex) {
            DB::rollBack();
            return response()->json('Proveedor no actualizado', 500);
        }
    }

    public function setStatus(Request $request, $id) {
        $proveedor = json_decode($request->getContent());
        $status = Proveedor::where('id', $id)->update(['estado' => $proveedor->estado]);
        if($status == 1)
            return response()->json($proveedor->estado ? 'Proveedor habilitado' : 'Proveedor deshabilitado', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Proveedor::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Proveedor eliminado correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}