<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function __construct() {}

    public function getAll(Request $request) {
        $condition = $request->criterio2 ? ' between ? and ?' : ' like ?';
        $filtro = $request->filtro.$condition;
        $criterio = $request->criterio2 ? array($request->criterio1, $request->criterio2) : array('%'.$request->criterio1.'%');
        $estado = $request->estado == 2 ? array(0, 1) : array($request->estado);
        $proveedores = array(
            'proveedores' => Proveedor::selectRaw('id, descripcion, convenio, telefono, telefono2, direccion, tipo_proveedor as tipoProveedor, contacto, 
                telefono_contacto as telefonoContacto, correo_contacto as correoContacto, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
                'usr_mod as usrModificacion, fec_mod as fecModificacion')
                ->where('estado_tabla', 1)->whereIn('estado', $estado)->whereRaw($filtro, $criterio)->orderBy($request->orden, $request->direccion)
                ->skip($request->pagina*$request->cantidad)->take($request->cantidad)->get(),
            'total' => Proveedor::where('estado_tabla', 1)->whereRaw($filtro, $criterio)->whereIn('estado', $estado)->count()
        );
        return $proveedores;
    }

    public function getById(Request $request, $id) {
        return $id != 'nuevo' ? Proveedor::selectRaw('id, descripcion, convenio, telefono, telefono2, direccion, tipo_proveedor as tipoProveedor, contacto, 
            telefono_contacto as telefonoContacto, correo_contacto as correoContacto, estado, usr_ing as usrIngreso, fec_ing as fecIngreso, '.
            'usr_mod as usrModificacion, fec_mod as fecModificacion')
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