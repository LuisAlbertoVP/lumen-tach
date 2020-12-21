<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
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
        $proveedores = array(
            'proveedores' => Proveedor::selectRaw('id, descripcion, convenio, telefono, direccion, tipo_proveedor as tipoProveedor,'.
                    'contacto, telefono_contacto as telefonoContacto, correo_contacto as correoContacto, estado, usr_ing as usrIngreso,'. 
                    'fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)
                ->orderBy($request->orden['activo'], $request->orden['direccion'])
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Proveedor::where('estado_tabla', 1)->whereIn('estado', $estado)->where($condition)->count()
        );
        return $proveedores;
    }

    public function getById(Request $request, $id) {
        return $id != 'nuevo' ? Proveedor::selectRaw('id, descripcion, convenio, telefono, direccion, tipo_proveedor as tipoProveedor,'.
                'contacto, telefono_contacto as telefonoContacto, correo_contacto as correoContacto, estado, usr_ing as usrIngreso,'. 
                'fec_ing as fecIngreso, usr_mod as usrModificacion, fec_mod as fecModificacion')
            ->where('estado_tabla', 1)->where('id', $id)->first() : null;
    }

    public function insertOrUpdate(Request $request) {
        $json = $request->getContent();
        DB::select('CALL AddProveedor(?)', [$json]);
        return response()->json('Proveedor actualizado correctamente', 200);
    }

    public function setStatus(Request $request, $id) {
        $proveedor = json_decode($request->getContent());
        $status = Proveedor::where('id', $id)->update(['estado' => $proveedor->estado]);
        if($status == 1)
            return response()->json($proveedor->estado == 1 ? 'Proveedor habilitado' : 'Proveedor deshabilitado', 200);
        return response()->json('No se han guardado los cambios', 500);
    }

    public function delete($id) {
        $status = Proveedor::where('id', $id)->update(['estado_tabla' => 0]);
        if($status == 1)
            return response()->json('Proveedor eliminado correctamente', 200);
        return response()->json('No se han guardado los cambios', 500);
    }
}