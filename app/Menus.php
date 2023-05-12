<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menus extends Model {

    public static function valida_menu($href, $id_mo, $per_id) {
        $query = "
SELECT
	a.id,
	a.per_codigo,
	d.id_menuPanel,
	d.id_menuOpciones,
	d.menuOp_href,
	d.menuOp_tittle 
FROM
	tb_personal a
	INNER JOIN tb_puesto b ON a.id_puesto = b.id_puesto
	INNER JOIN tb_menupuesto c ON b.id_puesto = c.id_puesto
	INNER JOIN tb_menuopciones d ON c.id_menuOpciones = d.id_menuOpciones 
WHERE
	a.per_estado <> 0 
	AND b.pue_estado <> 0 
	AND c.menuPu_estado <> 0 
	AND d.menuOp_estado <> 0 
	AND a.id = '$per_id' 
	AND d.id_menuOpciones = '$id_mo'
        AND d.menuOp_href = '$href';";
        $data = DB::select($query);
        return $data;
    }

    public static function carga_opciones($id_menu, $id) {
        $query = "
SELECT
	mp.menuPa_tittle,
	mo.id_menuOpciones AS id,
	mo.menuOp_img AS imagen,
	mo.menuOp_tittle AS titulo,
	mo.menuOp_href AS href,
	SUBSTRING_INDEX( mo.menuOp_href, '/',- 1 ) AS nombre,
	mo.menuOp_estado AS estado 
FROM
	tb_menuopciones mo
	INNER JOIN tb_menupanel mp ON mo.id_menuPanel = mp.id_menuPanel
	INNER JOIN tb_menupuesto pu ON mo.id_menuOpciones = pu.id_menuOpciones
	INNER JOIN tb_puesto p ON pu.id_puesto = p.id_puesto
	INNER JOIN tb_personal pe ON p.id_puesto = pe.id_puesto 
WHERE
	mo.id_menuPanel = '$id_menu' 
	AND pe.id = '$id' 
ORDER BY
	4;";
        $data = DB::select($query);
        return $data;
    }

}
