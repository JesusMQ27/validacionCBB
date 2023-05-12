<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Principal extends Model {

    protected $table = 'principal';

    public static function load_modulos() {
        $id = Auth::user()->attributes;
        $usuario = Auth::user()->per_codigo;
        $clave = Auth::user()->password;
        //carga menus
        $query = "
SELECT
        d.id_menuPanel,
	d.id_menuOpciones,
        d.menuOp_href,
	e.menuPa_tittle,
	d.menuOp_tittle 
FROM
	tb_personal a
	INNER JOIN tb_puesto b ON a.id_puesto = b.id_puesto
	INNER JOIN tb_menupuesto c ON b.id_puesto = c.id_puesto
	INNER JOIN tb_menuopciones d ON c.id_menuOpciones = d.id_menuOpciones
	INNER JOIN tb_menupanel e ON d.id_menuPanel = e.id_menuPanel 
WHERE
	a.per_estado <> 0 
	AND b.pue_estado <> 0 
	AND c.menuPu_estado <> 0 
	AND d.menuOp_estado <> 0 
	AND e.menuPa_estado <> 0 
	AND a.per_codigo = '$usuario'            
        AND a.password = '$clave'
ORDER BY
        d.id_menuPanel,
        d.menuOp_tittle
            ";
        //$data = DB::select($query);
        $arreglo = DB::select($query);
        $data = array();
        $texto = "";
        foreach ($arreglo as $f) {
            if ($f->menuPa_tittle != $texto) {
                $data[$f->id_menuPanel]['nombre'][] = $f->menuPa_tittle;
                $data[$f->id_menuPanel]['id'][] = $f->id_menuPanel;
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['nombre'] = $f->menuOp_tittle;
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['id'] = $f->id_menuOpciones;
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['href'] = $f->menuOp_href;
            } else {
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['nombre'] = $f->menuOp_tittle;
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['id'] = $f->id_menuOpciones;
                $data[$f->id_menuPanel]['id']['opciones'][$f->id_menuOpciones]['href'] = $f->menuOp_href;
            }
            $texto = $f->menuPa_tittle;
            //$data['panel'][] = $f->menuPa_tittle;
        }
        //dd($data);
        /* $id2 = Auth::user()->all();
          $personal = DB::table('usuarios')
          ->where(['id' => $id])
          ->get(); */
        return $data;
    }

}
