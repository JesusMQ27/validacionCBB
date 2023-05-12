<?php

namespace App;

ini_set('max_execution_time', 300); //3 minutes

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Devengados extends Model {
    /* pruebas

     */

    public static function loadSerie() {
        $query = "select * from tb_serie;";
        $data = DB::select($query);
        return ($data);
    }

    public static function loadSerie_desc($serie_desc) {
        $data = DB::table('tb_serie')->where('serie_desc', $serie_desc)->first();
        return $data;
    }

    public static function filtraSerie($id_serie, array $series) {
        foreach ($series as $f) {
            if ($f->serie_desc == $id_serie) {
                return $f->id_serie;
            }
        }
    }

    /*     * *************** CARGA DEVENGADOS ****************** */

    public static function insertGrupo($insert_data) {
        DB::table('tb_devengado_grupo')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        return $id;
    }

    public static function uploadDevengados($insert_data) {
        $insert = $insert_data['deve_boleta'];
        $data = DB::table('tb_devengado')->where('deve_estado', '<>', '0')->where('deve_boleta', "$insert")->first();
        if (count((array) $data) == 0) {
            DB::table('tb_devengado')->insert($insert_data);
        }
    }

    public static function uploadDevengadosAlexia($insert_data) {
        $insert = $insert_data['deve_boleta'];
        $data = DB::table('tb_devengado')->where('deve_estado', '<>', '0')->where('deve_boleta', "$insert")->first();
        if (count((array) $data) == 0) {
            DB::table('tb_devengado')->insert($insert_data);
        }
    }

    public static function uploadDevengadosAlexia_tmp_create($tabla_tmp) {
        $query_drop = "DROP TABLE IF EXISTS $tabla_tmp;";
        DB::select($query_drop);
        $query = "
CREATE TABLE $tabla_tmp  (
  id_devengado int(11) NOT NULL AUTO_INCREMENT,
  deve_anio int(4) NULL DEFAULT NULL,
  deve_fecha_emicar date NULL DEFAULT NULL,
  deve_fecha_venc date NULL DEFAULT NULL,
  deve_fecha_pag date NULL DEFAULT NULL,
  deve_fecha date NULL DEFAULT NULL,
  deve_grado varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_dni varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  id_serie int(11) NULL DEFAULT NULL,
  deve_num varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_boleta varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_alumno varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_concepto varchar(140) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_serie_ticke varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_dscto varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_base_imp varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_igv varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_cuota varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_monto double(8, 2) NULL DEFAULT NULL,
  deve_monto_cancelado double(8, 0) NULL DEFAULT NULL,
  deve_pago double(8, 2) NULL DEFAULT NULL,
  deve_pago_anulado double(8, 2) NULL DEFAULT NULL,
  deve_tc varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_tipo varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_centro varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_estado_tipo varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  deve_banco varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  id_grupo int(11) NULL DEFAULT NULL,
  deve_estado char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  comentario_anulacion varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (id_devengado) USING BTREE,
  INDEX fk_serie(id_serie) USING BTREE,
  INDEX fk_grupo(id_grupo) USING BTREE,
  INDEX `fk_tmp_deve_anio`(`deve_anio`) USING BTREE,
  INDEX `fk_tmp_deve_boleta`(`deve_boleta`) USING BTREE,
  INDEX `fk_tmp_deve_num`(`deve_num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
            ";
        DB::select($query);
    }

    public static function uploadDevengadosAlexia_tmp($insert_data, $tabla_tmp) {
        //$deve_boleta = $insert_data['deve_boleta'];
        //$data = DB::table($tabla_tmp)->where('deve_estado', '<>', '0')->where('deve_boleta', "$deve_boleta")->first();
        //if (count((array) $data) == 0) {
        DB::table($tabla_tmp)->insert($insert_data);
        //}
    }

    public static function selectDevengados_tmp($table_name) {
        $query = "
SELECT
IF
  ( b.id_devengado IS NOT NULL, 'ENCONTRADO', 'NUEVO' ) AS deve_detalle,
        c.serie_desc,
  a.* 
FROM
  $table_name a
        INNER JOIN tb_serie c on a.id_serie = c.id_serie
  LEFT JOIN tb_devengado b ON ( a.deve_anio = b.deve_anio AND a.deve_boleta = b.deve_boleta AND a.deve_num = b.deve_num) 
  OR ( a.id_serie = b.id_serie AND a.deve_num = b.deve_num ) 
WHERE
  a.id_serie IS NOT NULL 
ORDER BY 
        a.deve_anio,a.id_serie,a.deve_num
            ";
        $data = DB::select($query);
        //$data = DB::table($table_name)->where('id_serie', '<>', null)->get();
        return $data;
    }

    public static function selectDevengados_tmp_error($table_name) {
        $data = DB::table($table_name)->where('id_serie')->get();
        return $data;
    }

    public static function selectDevengados_error($id_grupo) {
        $data = DB::table('tb_devengado')->where('id_grupo', $id_grupo)->where('id_serie')->get();
        return $data;
    }

    public static function load_devengado_modal($id_devengado) {
        $data = DB::table('tb_devengado')->where('id_devengado', $id_devengado)->first();
        return $data;
    }

    public static function load_devengado_tmp_modal($table_name, $id_devengado) {
        $data = DB::table($table_name)->where('id_devengado', $id_devengado)->first();
        return $data;
    }

    public static function load_devengado($id_devengado) {
        $query = "
SELECT
  a.*,
  b.serie_desc,
  b.serie_sede,
  b.serie_tipo,
  b.serie_estado 
FROM
  tb_devengado a
  LEFT JOIN tb_serie b ON a.id_serie = b.id_serie 
WHERE
  a.id_devengado = '$id_devengado'";
        $data = DB::select($query);
        return $data;
    }

    public static function load_deve_boleta($deve_boleta) {
        $data = DB::table('tb_devengado')->where('deve_boleta', $deve_boleta)->where('deve_estado', '<>', '0')->first();
        return $data;
    }

    public static function update_devengado(array $data, $id_devengado) {
        $data = DB::table('tb_devengado')->where('id_devengado', $id_devengado)->update($data);
        if ($data) {
            return 1;
        } else {
            return 2;
        }
    }

    public static function update_devengado_tmp(array $data, $id_devengado, $tmp_table) {
        $data_r = DB::table($tmp_table)->where('id_devengado', $id_devengado)->update($data);
        if ($data_r) {
            return 1;
        } else {
            return 2;
        }
    }

    public static function upload_devengadoAlexia($table_name) {
        $query = "
insert tb_devengado(
  deve_anio,
  deve_fecha_emicar,
  deve_fecha_venc,
  deve_fecha_pag,
  deve_fecha,
  deve_grado,
  deve_dni,
  id_serie,
  deve_num,
  deve_boleta,
  deve_alumno,
  deve_concepto,
  deve_serie_ticke,
  deve_dscto,
  deve_base_imp,
  deve_igv,
  deve_cuota,
  deve_monto,
  deve_monto_cancelado,
  deve_pago,
  deve_pago_anulado,
  deve_tc,
  deve_tipo,
  deve_centro,
  deve_estado_tipo,
  deve_banco,
  id_grupo,
  deve_estado,
  comentario_anulacion
)
SELECT
  t1.deve_anio,
  t1.deve_fecha_emicar,
  t1.deve_fecha_venc,
  t1.deve_fecha_pag,
  t1.deve_fecha,
  t1.deve_grado,
  t1.deve_dni,
  t1.id_serie,
  t1.deve_num,
  t1.deve_boleta,
  t1.deve_alumno,
  t1.deve_concepto,
  t1.deve_serie_ticke,
  t1.deve_dscto,
  t1.deve_base_imp,
  t1.deve_igv,
  t1.deve_cuota,
  t1.deve_monto,
  t1.deve_monto_cancelado,
  t1.deve_pago,
  t1.deve_pago_anulado,
  t1.deve_tc,
  t1.deve_tipo,
  t1.deve_centro,
  t1.deve_estado_tipo,
  t1.deve_banco,
  t1.id_grupo,
  t1.deve_estado,
  t1.comentario_anulacion from(
SELECT
  a.*,
IF
  ( b.id_devengado IS NOT NULL, 'ENCONTRADO', 'NUEVO' ) AS deve_detalle 
FROM
  $table_name a
  INNER JOIN tb_serie c ON a.id_serie = c.id_serie
  LEFT JOIN tb_devengado b ON ( a.deve_anio = b.deve_anio AND a.deve_boleta = b.deve_boleta  AND a.deve_num = b.deve_num ) 
  OR ( a.id_serie = b.id_serie AND a.deve_num = b.deve_num ) 
WHERE
  a.id_serie IS NOT NULL 
ORDER BY
  a.deve_anio,
  a.id_serie,
  a.deve_num) as t1 where t1.deve_detalle not like 'ENCONTRADO'
            ";
        DB::select($query);
    }

    /*     * ****************** CARGO PAGOS ********************** */

    public static function insertGrupoPago($insert_data) {
        DB::table('tb_pagos_grupo')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        return $id;
    }

    public static function uploadPagos($insert_data) {
        $data = DB::table('tb_pagos')->where('pago_estado', '<>', '0')->where('pago_boleta', $insert_data['pago_boleta'])->first();
        if (count((array) $data) == 0) {
            DB::table('tb_pagos')->insert($insert_data);
            DB::table('tb_devengado')->where('deve_boleta', $insert_data['pago_boleta'])->where('deve_estado', '<>', '10')->update(["deve_pago" => $insert_data['pago_monto']]);
        }
    }

    
    public static function uploadAllPagos($insert_data, $table) {
        $data = DB::table($table)->where('pago_estado', '<>', '0')->where('pago_boleta', $insert_data['pago_boleta'])->first();
        if (count((array) $data) == 0) {
            DB::table($table)->insert($insert_data);
        }
    }

    public static function uploadPagosAlexia($insert_data) {
        $data = DB::table('tb_pagos')->where('pago_estado', '<>', '10')->where('pago_boleta', $insert_data['pago_boleta'])->first();
        if (count((array) $data) == 0) {
            DB::table('tb_pagos')->insert($insert_data);
            DB::table('tb_devengado')->where('deve_boleta', $insert_data['pago_boleta'])->where('deve_estado', '<>', '10')->update(["deve_pago" => $insert_data['pago_monto']]);
        }
    }

    public static function uploadPagosAlexia_tmp_create($tabla_tmp) {
        $query_drop = "DROP TABLE IF EXISTS $tabla_tmp;";
        DB::select($query_drop);
        $query = "
CREATE TABLE $tabla_tmp  (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `pago_anio` int(11) NULL DEFAULT NULL,
  `pago_fecha_emicar` date NULL DEFAULT NULL,
  `pago_fecha_venc` date NULL DEFAULT NULL,
  `pago_fecha` date NULL DEFAULT NULL,
  `pago_emision` date NULL DEFAULT NULL,
  `pago_grado` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_serie` int(11) NULL DEFAULT NULL,
  `pago_num` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_dni` varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_boleta` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_alumno` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_concepto` varchar(140) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_serie_ticke` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_dscto` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_base_imp` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_igv` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_cuota` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_monto` double(8, 2) NULL DEFAULT NULL,
  `pago_monto_cancelado` double(8, 2) NULL DEFAULT NULL,
  `pago_tc` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_tipo` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_centro` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_estado_tipo` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pago_banco` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_grupo_pago` int(11) NULL DEFAULT NULL,
  `pago_estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_pago`) USING BTREE,
  INDEX `fk_pagos_grupo`(`id_grupo_pago`) USING BTREE,
  INDEX `fk_tmp_pago_anio`(`pago_anio`) USING BTREE,
  INDEX `fk_tmp_pago_boleta`(`pago_boleta`) USING BTREE,
  INDEX `fk_tmp_pago_num`(`pago_num`) USING BTREE,
  INDEX `fk_tmp_id_serie`(`id_serie`) USING BTREE  
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;         
            ";
        DB::select($query);
    }

    public static function uploadPagosAlexia_tmp($insert_data, $table_tmp) {
        //$data = DB::table($table_tmp)->where('pago_estado', '<>', '10')->where('pago_boleta', $insert_data['pago_boleta'])->first();
        //if (count((array) $data) == 0) {
        DB::table($table_tmp)->insert($insert_data);
        //DB::table('tb_devengado')->where('deve_boleta', $insert_data['pago_boleta'])->where('deve_estado', '<>', '10')->update(["deve_pago" => $insert_data['pago_monto']]);
        //}
    }

    public static function selectPagos_tmp_error($table_name) {
        $data = DB::table($table_name)->where('id_serie')->get();
        return $data;
    }

    public static function selectPagos($id_grupo) {
        $data = DB::table('tb_pagos')->where('id_grupo_pago', $id_grupo)->where('id_serie')->get();
        return $data;
    }

    public static function selectPagos_tmp($table_name) {
        /* $query = "
          SELECT
          IF
          ( c.id_pago IS NOT NULL, 'ENCONTRADO', 'NUEVO' ) AS pago_detalle,
          a.*
          FROM
          $table_name a
          INNER JOIN tb_serie b ON a.id_serie = b.id_serie
          LEFT JOIN tb_pagos c ON ( a.pago_anio = c.pago_anio AND a.pago_boleta = c.pago_boleta )
          OR ( a.id_serie = c.id_serie AND a.pago_num = c.pago_num )
          "; */
        $query = "
SELECT
IF(d.id_devengado IS NOT NULL,'ENCONTRADO','NUEVO') as detalle,
t1.* from (
SELECT
IF
  ( c.id_pago IS NOT NULL, 'ENCONTRADO', 'NUEVO' ) AS pago_detalle,serie_desc,
  a.* 
FROM
  $table_name a
  INNER JOIN tb_serie b ON a.id_serie = b.id_serie
  LEFT JOIN tb_pagos c ON 
  -- ( a.pago_anio = c.pago_anio AND a.pago_boleta = c.pago_boleta ) OR 
  ( a.id_serie = c.id_serie AND a.pago_num = c.pago_num )     
  ) as t1 
  left join tb_devengado d ON ( t1.pago_anio = d.deve_anio AND t1.pago_boleta = d.deve_boleta and t1.pago_num = d.deve_num) 
  OR ( t1.id_serie = d.id_serie AND t1.pago_num = d.deve_num /*AND t1.pago_boleta = d.deve_boleta*/ )           
            ";
        //$data = DB::table($table_name)->where('id_grupo_pago', $id_grupo)->where('id_serie')->get();
        $data = DB::select($query);
        return $data;
    }

    public static function load_pago_modal($id_pago) {
        $data = DB::table('tb_pagos')->where('id_pago', $id_pago)->first();
        return $data;
    }

    public static function load_pago_tmp_modal($id_pago, $table_name) {
        $data = DB::table($table_name)->where('id_pago', $id_pago)->first();
        return $data;
    }

    public static function load_pago_boleta($pago_boleta) {
        $data = DB::table('tb_pagos')->where('pago_boleta', $pago_boleta)->where('pago_estado', '<>', '0')->first();
        return $data;
    }

    public static function update_pago(array $data, $id_pago) {
        DB::table('tb_pagos')->where('id_pago', $id_pago)->update($data);
    }

    public static function update_pago_tmp(array $data, $id_pago, $tmp_table) {
        DB::table($tmp_table)->where('id_pago', $id_pago)->update($data);
    }

    public static function update_pago_devengado($deve_boleta) {
        DB::table('tb_devengado')->where('deve_boleta', "$deve_boleta")->where('deve_estado', '<>', '0')->update(['deve_estado' => '2']);
    }

    public static function upload_pagoAlexia($table_name) {
        $query = "
insert into tb_pagos(
pago_anio,
pago_fecha_emicar,
pago_fecha_venc,
pago_fecha,
pago_emision,
pago_grado,
id_serie,
pago_num,
pago_dni,
pago_boleta,
pago_alumno,
pago_concepto,
pago_serie_ticke,
pago_dscto,
pago_base_imp,
pago_igv,
pago_cuota,
pago_monto,
pago_monto_cancelado,
pago_tc,
pago_tipo,
pago_centro,
pago_estado_tipo,
pago_banco,
id_grupo_pago,
pago_estado
)   
select 
t2.pago_anio,
t2.pago_fecha_emicar,
t2.pago_fecha_venc,
t2.pago_fecha,
t2.pago_emision,
t2.pago_grado,
t2.id_serie,
t2.pago_num,
t2.pago_dni,
t2.pago_boleta,
t2.pago_alumno,
t2.pago_concepto,
t2.pago_serie_ticke,
t2.pago_dscto,
t2.pago_base_imp,
t2.pago_igv,
t2.pago_cuota,
t2.pago_monto,
t2.pago_monto_cancelado,
t2.pago_tc,
t2.pago_tipo,
t2.pago_centro,
t2.pago_estado_tipo,
t2.pago_banco,
t2.id_grupo_pago,
t2.pago_estado
from (
select 
  IF(d.id_devengado is not null,'ENCONTRADO', 'NUEVO') as detalle,
t1.*  from(
SELECT
IF
  ( c.id_pago IS NOT NULL, 'ENCONTRADO', 'NUEVO' ) AS pago_detalle,
  a.* 
FROM
  $table_name a
  INNER JOIN tb_serie b ON a.id_serie = b.id_serie
  LEFT JOIN tb_pagos c ON ( a.pago_anio = c.pago_anio AND a.pago_boleta = c.pago_boleta ) 
  OR ( a.id_serie = c.id_serie AND a.pago_num = c.pago_num )
  )as t1 
  LEFT JOIN tb_devengado d on ( t1.pago_anio = d.deve_anio AND t1.pago_boleta = d.deve_boleta and t1.pago_num = d.deve_num) 
  OR ( t1.id_serie = d.id_serie AND t1.pago_num = d.deve_num )
  where t1.pago_detalle = 'NUEVO') as t2 where t2.detalle = 'ENCONTRADO'";
        DB::select($query);
        $query_update = "
UPDATE tb_devengado AS t2,
$table_name AS t1 
SET t2.deve_pago = t1.pago_monto_cancelado,
t2.deve_estado = 2 
WHERE
  ( t1.pago_anio = t2.deve_anio AND t1.pago_boleta = t2.deve_boleta and t1.pago_num = t2.deve_num) 
  OR ( t1.id_serie = t2.id_serie AND t1.pago_num = t2.deve_num );            
            ";
        DB::select($query_update);
    }

    /*     * ************* ADMINISTRACION **************** */

    public static function lista_grupo_pago() {
        $query = "
SELECT
  pg.id_grupo_pago,
  pg.grupo_fecha,
  CONCAT( p.per_pate, ' ', p.per_mate, ', ', p.per_nomb ) AS nombre,
  COUNT( pa.id_pago ) AS cant_total,
  COUNT( pa.id_serie ) AS cant_sin_serie 
FROM
  tb_pagos_grupo pg
  INNER JOIN tb_personal p ON pg.id_per = p.id
  LEFT JOIN tb_pagos pa ON pg.id_grupo_pago = pa.id_grupo_pago 
WHERE
  pg.grupo_estado = 1 
        AND pa.pago_estado <> 0 
GROUP BY
  pg.id_grupo_pago,
  pg.grupo_fecha,
  nombre 
ORDER BY
  pg.id_grupo_pago DESC
            ";
        $data = DB::select($query);
        return $data;
    }

    public static function lista_pago($id_grupo) {
        $data = DB::table('tb_pagos')->where('id_grupo_pago', "$id_grupo")->get();
        return $data;
    }

    public static function deleteGrupopagos($id_grupo) {
        DB::table('tb_pagos')->where('id_grupo_pago', "$id_grupo")->update(['pago_estado' => "10"]);
        DB::table('tb_pagos_grupo')->where('id_grupo_pago', "$id_grupo")->update(['grupo_estado' => "10"]);
    }

    public static function anulacion_pago($id_pago, $comentario) {
        DB::table('tb_pagos')->where('id_pago', "$id_pago")->update(['pago_estado' => "0", 'comentario_anulacion' => "$comentario"]);
    }

    public static function lista_grupo_devengado() {
        $query = "
SELECT
  dg.id_grupo,
  dg.grupo_fecha,
  CONCAT( per_pate, ' ', per_mate, ', ', per_nomb ) AS nombre,
  count( d.id_devengado ) AS cant_total,
  count( d.id_serie ) AS cant_sin_serie 
FROM
  tb_devengado_grupo dg
  INNER JOIN tb_personal p ON dg.id_per = p.id
  LEFT JOIN tb_devengado d ON dg.id_grupo = d.id_grupo 
WHERE
  grupo_estado = 1 
  AND deve_anio > 2016 
GROUP BY
  dg.id_grupo,
  dg.id_grupo,
  dg.grupo_fecha,
  nombre 
ORDER BY
  dg.id_grupo DESC;
            ";
        $data = DB::select($query);
        return $data;
    }

    public static function lista_devengado($id_grupo) {
        $query = "
SELECT
  d.id_devengado,
  d.deve_anio,
  d.deve_fecha_emicar,
  d.deve_fecha_venc,
  d.deve_fecha_pag,
  d.deve_fecha,
  d.deve_grado,
  d.deve_dni,
  d.id_serie,
  s.serie_desc,
  d.deve_num,
  d.deve_boleta,
  d.deve_alumno,
  d.deve_concepto,
  d.deve_serie_ticke,
  d.deve_dscto,
  d.deve_base_imp,
  d.deve_igv,
  d.deve_cuota,
  d.deve_monto,
  d.deve_monto_cancelado,
  d.deve_pago,
  d.deve_pago_anulado,
  d.deve_tc,
  d.deve_tipo,
  d.deve_centro,
  d.deve_estado_tipo,
  d.deve_banco,
  d.id_grupo,
  d.deve_estado,
  d.comentario_anulacion,
  d.not_id as nota,
  t.tsus_desc as mensaje,
  n.not_monto as pago_anulado
FROM
  tb_devengado d
  LEFT JOIN tb_serie s ON d.id_serie = s.id_serie 
  LEFT JOIN tb_nota_credito n ON d.not_id=n.not_id
  LEFT JOIN tb_tipo_sustento t ON n.not_tsus_id=t.tsus_id
WHERE
  d.id_grupo = '$id_grupo' 
ORDER BY
        d.deve_anio,
  s.serie_desc,
  d.deve_num            
            ";
        $data = DB::select($query);
        //$data = DB::table('tb_devengado')->where('id_grupo', "$id_grupo")->orderBy('deve_estado', 'desc')->orderBY('deve_fecha', 'asc')->get();
        return $data;
    }

    public static function deleteGrupoDevengados($id_grupo) {
        DB::table('tb_devengado')->where('id_grupo', "$id_grupo")->update(['deve_estado' => "10"]);
        DB::table('tb_devengado_grupo')->where('id_grupo', "$id_grupo")->update(['grupo_estado' => "10"]);
    }

    public static function anulacion_devengado($insert_data, $id_devengado) {
        DB::table('tb_nota_credito')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        DB::table('tb_devengado')->where('id_devengado', $id_devengado)->update(["deve_estado" => "0", "not_id" => "$id"]);
    }

    /*     * ************* CARGA REPORTES **************** */

    public static function deve_reporte_excel($insert_data) {
        $fini = $insert_data['devepfini'];
        $ffin = $insert_data['devepffin'];
        $serie = $insert_data['deverpserie'];
        $tipo = $insert_data['deverptipo'];
        /* $fini = $insert_data->fini;
          $ffin = $insert_data->ffin;
          $serie = $insert_data->serie;
          $tipo = $insert_data->tipo; */
        $query_serie = "";
        if ($serie != "1") {
            $query_serie = "AND s.id_serie LIKE '$serie' ";
        }
        if ($tipo == "1") {//devengado
            $query = "
          SELECT
          'Fecha de Emision' AS fecha,
          'DNI' AS dni,
          'Matricula' as grado,
          'Boleta' AS boleta,
          'Alumno' AS alumno,
          'Cuota' AS cuota,
          'Monto' AS monto,
          'Estado' AS estado UNION ALL
          SELECT
         * 
          FROM
          (
          SELECT
          d.deve_fecha AS fecha,
          d.deve_dni AS dni,
          d.deve_grado as grado,
          d.deve_boleta AS boleta,
          d.deve_alumno AS alumno,
          d.deve_cuota AS cuota,
          d.deve_monto AS monto,
          CASE
          d.deve_estado
          WHEN 1 THEN
          'Devengado'
          WHEN 2 THEN
          'Pagado'
          END AS estados
          FROM
          tb_devengado d
          LEFT JOIN tb_serie s ON d.id_serie = s.id_serie
          WHERE
          d.deve_estado = 1
          AND d.deve_fecha BETWEEN '$fini'
          AND '$ffin'
          $query_serie
          GROUP BY
          d.deve_boleta,
          d.deve_fecha,
          d.deve_dni,
          d.deve_alumno,
          d.deve_cuota,
          d.deve_monto,
          d.deve_estado
          ORDER BY d.deve_fecha,d.deve_boleta) as t1
          ;
          ";
        } else if ($tipo == "2") {//pago
            $query = "
          SELECT
          'Fecha de Emision' AS fecha,
          'DNI' AS dni,
          'Matricula' as grado,
          'BOLETA' AS boleta,
          'ALUMNO' AS alumno,
          'CUOTA' AS cuota,
          'MONTO' AS monto,
          'CONCEPTO' AS estado,
          'FECHA DE PAGO' as fecha_pago,
          'BANCO' as pago_banco,
          'DETALLE' as pago_estado_tipo
          -- 'COMENTARIO' as comentario_anulacion
          UNION ALL
          SELECT
         * 
          FROM
          (
          SELECT
          p.pago_emision as fecha,
          p.pago_dni as dni,
          p.pago_grado as grado,
          p.pago_boleta as boleta,
          p.pago_alumno as alumno,
          IF(pago_cuota<>'' OR pago_cuota is not null,ELT(MONTH(p.pago_emision), 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'),ELT(MONTH(p.pago_fecha_venc), 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE')) AS cuota,
          p.pago_monto as monto,
          p.pago_concepto as estado,
          p.pago_fecha as fecha_pago,
          p.pago_banco,
          p.pago_estado_tipo
          -- p.comentario_anulacion
          FROM
          tb_pagos p
          LEFT JOIN tb_serie s ON p.id_serie = s.id_serie
          WHERE
          p.pago_estado <> 0
          AND p.pago_fecha BETWEEN '$fini'
          AND '$ffin' $query_serie
          GROUP BY
          p.pago_boleta,
          p.pago_emision,
          p.pago_fecha,
          p.pago_dni,
          p.pago_alumno,
          p.pago_cuota,
          p.pago_monto,
          p.pago_estado
          ORDER BY
          p.pago_emision,
          p.pago_fecha)as t1
          ";
        } else if ($tipo == "3") {//anulado
            $query = "SELECT
        'Fecha de Emision' AS fecha,
        'DNI' AS dni,
        'Matricula' as grado,
        'Boleta' AS boleta,
        'Alumno' AS alumno,
        'Cuota' AS cuota,
        'Monto' AS monto,
        'Estado' AS estado,
        'Descripción' AS comentario_anulacion,
        'Boleta afectada' AS doc_afecta
        UNION ALL
        SELECT fecha,dni,grado,boleta,alumno,cuota,monto,estado,comentario_anulacion,doc_afecta FROM (
        SELECT not_fecha as fecha,
        deve_dni as dni,
        deve_grado as grado,
        not_documento as boleta,
        deve_alumno as alumno,
        deve_cuota as cuota,
        not_monto as monto,
        IF(b.not_id is null,'No hay Doc. Afectado encontrado', if(not_estado=1,'Activo','Inactivo')) as estado,
        not_descripcion as comentario_anulacion,
        not_doc_afecta as doc_afecta,
        a.not_serie as id_serie
        FROM tb_nota_credito a
        LEFT JOIN tb_devengado b ON a.not_id=b.not_id
        LEFT JOIN tb_serie s ON a.not_serie=s.id_serie
        where not_estado=1 AND (not_fecha BETWEEN '$fini' AND '$ffin') OR not_fecha is null
        GROUP BY not_documento,not_fecha,deve_dni,deve_alumno,deve_cuota,not_monto,not_estado
        ORDER BY not_fecha,not_documento ) as s where 1=1 $query_serie;";
        } else if ($tipo == "4") {
            $query = "
          SELECT
        'Fecha de Emision' AS fecha,
  'DNI' AS dni,
        'Matricula' as grado,
  'Boleta' AS boleta,
  'Alumno' AS alumno,
  'Cuota' AS cuota,
  'Monto Total' AS monto,
  'Estado' AS estado,
  'DETALLE' AS comentario_anulacion UNION ALL
          SELECT
         * 
          FROM
          (
          SELECT
          d.deve_fecha AS fecha,
          d.deve_dni AS dni,
          d.deve_grado as grado,
          d.deve_boleta AS boleta,
          d.deve_alumno AS alumno,
          d.deve_cuota AS cuota,
          d.deve_monto AS monto,
          CASE
          d.deve_estado
          WHEN 1 THEN
          'Devengado'
          WHEN 2 THEN
          'Pagado'
          ELSE 'Anulado'
          END AS estados,
          IF(d.comentario_anulacion is not null ,CONCAT(d.comentario_anulacion, '  ' ,d.deve_pago_anulado),'' ) AS comentario_anulacion 
          FROM
          tb_devengado d
          LEFT JOIN tb_serie s ON d.id_serie = s.id_serie
          WHERE
          d.deve_estado <> 10
          AND d.deve_fecha BETWEEN '$fini'
          AND '$ffin'
          $query_serie
          GROUP BY
          d.deve_boleta,
          d.deve_fecha,
          d.deve_dni,
          d.deve_alumno,
          d.deve_cuota,
          d.deve_monto,
          d.deve_estado
          ORDER BY d.deve_fecha,d.deve_boleta) as t1
          ;
          ";
        } else if ($tipo == "5") { //chinitos
            if ($serie != "1") {
                $query_serie = "AND a.id_serie LIKE '$serie' ";
            } else {
                $query_serie = "";
            }
            $query = "SELECT
            'Nro.' as nro,
            'Tipo Documento' AS tipo,
            'Nro. Doc. Identidad' AS docIden,
            'Comprobante' as comprobante,
            'Fecha envio' AS fechaEnvio,
            'Nombres' AS nombres,
            'Cod. Interno' AS codInterno,
            'Descuento' AS descuento,
            'Recargo' AS recargo,
            'Gratuito' AS gratuito,
            'IGV' AS igv,
            'ISC' AS isc,
            'Neto' AS neto,
            'Total' AS total,
            'Moneda' AS moneda,
            'Tipo moneda' AS tipomoneda,
            'Observacion' AS observacion,
            'Fecha sistema' AS fechasistema,
            'Estado' AS estado UNION ALL (
            SELECT (@cnt := @cnt + 1) AS nro,com_tipo_documento,com_doc_iden,concat(com_serie,'-',com_numero) as comprobante,com_fecha_envio,
            com_nombres,com_cod_interno,com_descuento,com_recargo,com_gratuito,com_igv,com_isc,com_neto,com_total,
            com_tip_moneda,com_tip_cambio,com_observacion,com_fecha_sistema,
            IF(com_estado=1,'Activo','Inactivo') as estado 
            FROM tb_comprobantes_ose a CROSS JOIN (SELECT @cnt := 0) AS dummy WHERE 1=1 AND ";
            $query .= " com_fecha_envio>='$fini 00:00:00' and com_fecha_envio<='$ffin 23:59:59' $query_serie 
            ORDER BY com_fecha_envio,com_serie,com_numero);";
            /* $query .= "com_fecha_sistema>='2021-09-01 00:00:00' and com_fecha_sistema<='2021-09-31 23:59:59'
              ORDER BY com_fecha_sistema,com_serie,com_numero;" */
        }//chinitos
        $data = DB::select($query);
        return collect($data);
    }

    public static function deve_reporte($insert_data) {
        $fini = $insert_data['fini'];
        $ffin = $insert_data['ffin'];
        $serie = $insert_data['serie'];
        $tipo = $insert_data['tipo'];
        $query_serie = "";
        if ($serie != "1") {
            $query_serie = "AND s.id_serie LIKE '$serie' ";
        }

        if ($tipo == "1") {//devengado
            $query = "
SELECT
  d.deve_fecha as fecha,
  d.deve_dni as dni,
        d.deve_grado as grado,
  d.deve_boleta as boleta,
  d.deve_alumno as alumno,
  d.deve_cuota as cuota,
  d.deve_monto as monto,
  d.deve_estado as estado
FROM
  tb_devengado d
  LEFT JOIN tb_serie s ON d.id_serie = s.id_serie 
WHERE
  d.deve_estado = 1
        AND d.deve_fecha BETWEEN '$fini' 
  AND '$ffin' 
  $query_serie
GROUP BY
  d.deve_boleta,
        d.deve_fecha,
        d.deve_dni,
        d.deve_alumno,
        d.deve_cuota,
        d.deve_monto,
        d.deve_estado 
ORDER BY d.deve_fecha,d.deve_boleta
        ;            
            ";
        } else if ($tipo == "2") {//pago
            $query = "
SELECT
  p.pago_emision as fecha,
  p.pago_dni as dni,
  p.pago_grado as grado,
  p.pago_boleta as boleta,
  p.pago_alumno as alumno,
  IF(pago_cuota<>'' OR pago_cuota is not null,ELT(MONTH(p.pago_emision), 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'),ELT(MONTH(p.pago_fecha_venc), 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE')) AS cuota,
  p.pago_monto as monto,
  p.pago_concepto as estado,
  p.pago_estado as estado_tipo,
  p.pago_fecha as fecha_pago,
  p.pago_banco,
  p.pago_estado_tipo,
  p.comentario_anulacion
FROM
  tb_pagos p
  LEFT JOIN tb_serie s ON p.id_serie = s.id_serie 
WHERE
  p.pago_estado <> 10 
  AND p.pago_fecha BETWEEN '$fini' 
  AND '$ffin' $query_serie 
GROUP BY
  p.pago_boleta,
        p.pago_emision,
  p.pago_fecha,
  p.pago_dni,
  p.pago_alumno,
  p.pago_cuota,
  p.pago_monto,
  p.pago_estado    
ORDER BY 
        p.pago_emision,
        p.pago_fecha
                ";
        } else if ($tipo == "3") {//anulado
            $query = "
SELECT * from (
SELECT not_fecha as fecha,deve_dni as dni,deve_grado as grado,
not_documento as boleta, deve_alumno as alumno,deve_cuota as cuota,
not_monto as monto,not_estado as estado,not_descripcion as comentario_anulacion,
deve_pago_anulado,not_doc_afecta as doc_afecta,if(b.not_id is null,0,1) as dev_esta,s.id_serie
FROM tb_nota_credito a
LEFT JOIN tb_devengado b ON a.not_id=b.not_id
LEFT JOIN tb_serie s ON a.not_serie=s.id_serie
where not_estado=1 AND (not_fecha BETWEEN '$fini' AND '$ffin') OR not_fecha is null
GROUP BY not_documento,not_fecha,deve_dni,deve_alumno,deve_cuota,not_monto,not_estado
ORDER BY not_fecha,not_documento) as s where 1=1 $query_serie ";
        } else if ($tipo == "4") {//devengado
            $query = "
SELECT
  d.deve_fecha as fecha,
  d.deve_dni as dni,
        d.deve_grado as grado,
  d.deve_boleta as boleta,
  d.deve_alumno as alumno,
  d.deve_cuota as cuota,
  d.deve_monto as monto,
  d.deve_estado as estado
FROM
  tb_devengado d
  LEFT JOIN tb_serie s ON d.id_serie = s.id_serie 
WHERE
  d.deve_estado <>10 
        AND d.deve_fecha BETWEEN '$fini' 
  AND '$ffin' 
  $query_serie
GROUP BY
  d.deve_boleta,
        d.deve_fecha,
        d.deve_dni,
        d.deve_alumno,
        d.deve_cuota,
        d.deve_monto,
        d.deve_estado 
ORDER BY d.deve_fecha,d.deve_boleta
        ;            
            ";
        } else if ($tipo == "5") { //chinitos
            if ($serie != "1") {
                $query_serie = "AND a.id_serie LIKE '$serie' ";
            } else {
                $query_serie = "";
            }
            $query = "SELECT a.*,IF(com_estado=1,'Activo','Inactivo') as estado 
            FROM tb_comprobantes_ose a WHERE 1=1 AND ";
            $query .= " com_fecha_envio>='$fini 00:00:00' and com_fecha_envio<='$ffin 23:59:59' $query_serie 
            ORDER BY com_fecha_envio,com_serie,com_numero;";
            /* $query .= "com_fecha_sistema>='2021-09-01 00:00:00' and com_fecha_sistema<='2021-09-31 23:59:59'
              ORDER BY com_fecha_sistema,com_serie,com_numero;" */
        }//chinitos
        $data = DB::select($query);
        return $data;
    }

    //Jesus M
    public static function fecha_hoy() {
        $query = "SELECT DATE_FORMAT(CURDATE(),'%d/%m/%Y') as fecha";
        $data = DB::select($query);
        return $data;
    }

    public static function load_nota_credito($id_nota) {
        $query = "SELECT not_id,
            not_anio,
            not_documento,
            not_serie,
            not_numero,
            DATE_FORMAT(not_fecha,'%d/%m/%Y') as not_fecha,
            DATE_FORMAT(not_fechor,'%d/%m/%Y %h:%i:%s') as not_fechor,            
            not_doc_afecta,
            tsus_desc as tipo,
            not_monto,
            not_descripcion,
            a.not_tsus_id as tsus_id,
            c.serie_desc as serie,
            case not_estado when '0' then 'Inactivo' when '1' then 'Activo' end as estado 
            FROM tb_nota_credito a LEFT JOIN tb_tipo_sustento b on a.not_tsus_id=b.tsus_id
            LEFT JOIN tb_serie c ON a.not_serie=c.id_serie
            WHERE not_id=$id_nota";
        $data = DB::select($query);
        return $data;
    }

    public static function tipo_sustento() {
        $query = "SELECT tsus_id as id,
        tsus_desc as nombre,
        tsus_estado as estado
        FROM tb_tipo_sustento WHERE tsus_estado='1';";
        $data = DB::select($query);
        return $data;
    }

    public static function load_serieDetalle_x_id($codigo) {
        $query = "SELECT * FROM  tb_serie where id_serie=$codigo";
        $data = DB::select($query);
        return $data;
    }

    public static function validar_existe_nota_credito($notaCod, $documento) {
        $query = "SELECT count(*) as cantidad from tb_nota_credito WHERE not_id<>$notaCod AND not_documento='$documento' AND not_estado='1';";
        $data = DB::select($query);
        return $data;
    }

    public static function update_nota_credito(array $data, $id_nota) {
        DB::table('tb_nota_credito')->where('not_id', $id_nota)->update($data);
    }

    public static function delete_nota_credito($id_nota) {
        DB::table('tb_nota_credito')->where('not_id', $id_nota)->update(["not_estado" => "0"]);
        DB::table('tb_devengado')->where('not_id', $id_nota)->update(["deve_estado" => "1", "not_id" => ""]);
    }

    public static function loadSerieNotasCreditos() {
        $query = "select * from tb_serie where serie_estado=2;";
        $data = DB::select($query);
        return ($data);
    }

    //jesus
    public static function insertGrupNotaCredito($insert_data) {
        DB::table('tb_nota_credito_grupo')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        return $id;
    }

    public static function uploadNotasCreditos_tmp_create($tabla_tmp) {
        $query_drop = "DROP TABLE IF EXISTS $tabla_tmp;";
        DB::select($query_drop);
        $query = "
CREATE TABLE $tabla_tmp  (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `nota_anio` int(11) NULL DEFAULT NULL,
  `nota_fecha_emicar` date NULL DEFAULT NULL,
  `nota_fecha_venc` date NULL DEFAULT NULL,
  `nota_fecha` date NULL DEFAULT NULL,
  `nota_emision` date NULL DEFAULT NULL,
  `nota_grado` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_serie` int(11) NULL DEFAULT NULL,
  `nota_num` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_dni` varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_boleta` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `nota_alumno` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_concepto` varchar(140) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_serie_ticke` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_dscto` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_base_imp` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_igv` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_total` double(8, 2) NULL DEFAULT NULL,
  `nota_monto_cancelado` double(8, 2) NULL DEFAULT NULL,
  `nota_tc` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_tipo` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_centro` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_estado_tipo` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_banco` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_observaciones` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nota_nro_avisos` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_grupo_pago` int(11) NULL DEFAULT NULL,
  `nota_estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_nota`) USING BTREE,
  INDEX `fk_pagos_grupo`(`id_grupo_pago`) USING BTREE,
  INDEX `fk_tmp_nota_anio`(`nota_anio`) USING BTREE,
  INDEX `fk_tmp_nota_boleta`(`nota_boleta`) USING BTREE,
  INDEX `fk_tmp_nota_num`(`nota_num`) USING BTREE,
  INDEX `fk_tmp_id_serie`(`id_serie`) USING BTREE  
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;";
        DB::select($query);
    }

    public static function uploadNotasCreditos_tmp($insert_data, $table_tmp) {
        DB::table($table_tmp)->insert($insert_data);
    }

    public static function selectNotasCreditos_tmp($table_name) {
        $query = "SELECT IF(c.not_id IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.serie_desc,b.serie_estado,
    REPLACE(SUBSTRING_INDEX(a.nota_concepto,'dito:',-1), '/', '-') as doc_afecta
    FROM
    $table_name a
    INNER JOIN tb_serie b ON a.id_serie = b.id_serie
    left join tb_nota_credito c ON a.nota_boleta = c.not_documento;";
        $data = DB::select($query);
        return $data;
    }

    public static function selectNotasCreditos_tmp_error($table_name) {
        $data = DB::table($table_name)->where('id_serie')->get();
        return $data;
    }

    public static function upload_notas($table_name) {
        $query = "insert into tb_nota_credito(
not_anio,
not_documento,
not_serie,
not_numero,
not_fecha,
not_fechor,
not_doc_afecta,
not_tsus_id,
not_descripcion,
not_monto,
id_grupo,
not_dni,
not_nombres,
not_estado
)
SELECT t2.anio,t2.boleta,t2.serie,t2.nota_num,t2.fecha,t2.fechahora,t2.doc_afecta,
if(deve_monto is null,'',if(deve_monto-monto>0,2,1)) as tipo_sustento,t2.nota_observaciones,t2.monto,t2.id_grupo_pago,t2.nota_dni,t2.nota_alumno,t2.estado
FROM (
SELECT nota_anio as anio,nota_boleta as boleta,id_serie as serie,nota_num,DATE(nota_fecha) as fecha,
now() as fechahora,doc_afecta, nota_observaciones,(nota_total*(-1)) as monto,1 as estado,nota_dni,nota_alumno,id_grupo_pago
FROM (
SELECT IF(c.not_id IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.serie_desc,b.serie_estado,
REPLACE(SUBSTRING_INDEX(a.nota_concepto,'dito:',-1), '/', '-') as doc_afecta
FROM
$table_name a
INNER JOIN tb_serie b ON a.id_serie = b.id_serie
LEFT JOIN tb_nota_credito c ON a.nota_boleta = c.not_documento ) as t1 WHERE detalle='NUEVO') as t2 
LEFT JOIN tb_devengado t3 ON t2.doc_afecta=t3.deve_boleta;";

        DB::select($query);
        $query_update = "UPDATE tb_devengado AS s1,
(SELECT t2.not_id,t2.anio,t2.boleta,t2.serie,t2.nota_num,t2.fecha,t2.fechahora,t2.doc_afecta,
if(deve_monto is null,'',if(deve_monto-monto>0,2,1)) as tipo_sustento,t2.nota_observaciones,t2.monto,t2.estado
FROM (
SELECT t1.not_id,nota_anio as anio,nota_boleta as boleta,id_serie as serie,nota_num,DATE(nota_fecha) as fecha,now() as fechahora,doc_afecta, nota_observaciones,(nota_total*(-1)) as monto,1 as estado FROM (
SELECT IF(c.not_id IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.serie_desc,b.serie_estado,
REPLACE(SUBSTRING_INDEX(a.nota_concepto,'dito:',-1), '/', '-') as doc_afecta,c.not_id
FROM
$table_name a
INNER JOIN tb_serie b ON a.id_serie = b.id_serie
LEFT JOIN tb_nota_credito c ON a.nota_boleta = c.not_documento ) as t1 WHERE detalle='ENCONTRADO') as t2 
INNER JOIN tb_devengado t3 ON t2.doc_afecta=t3.deve_boleta) as s2 
SET s1.deve_estado='0',s1.not_id=s2.not_id,s1.comentario_anulacion=s2.boleta
WHERE s1.deve_boleta=s2.doc_afecta;";
        DB::select($query_update);
    }

    public static function lista_grupo_nota_credito() {
        $query = "
SELECT
  pg.id_grupo,
  pg.grupo_fecha,
  CONCAT( p.per_pate, ' ', p.per_mate, ', ', p.per_nomb ) AS nombre,
  COUNT( nota.not_id ) AS cant_total,
  COUNT( nota.not_serie ) AS cant_sin_serie 
FROM
  tb_nota_credito_grupo pg
  INNER JOIN tb_personal p ON pg.id_per = p.id
  LEFT JOIN tb_nota_credito nota ON pg.id_grupo = nota.id_grupo 
WHERE
  pg.grupo_estado = 1 AND nota.not_estado <> 0 
GROUP BY
  pg.id_grupo,
  pg.grupo_fecha,
  nombre 
ORDER BY
  pg.id_grupo DESC
            ";
        $data = DB::select($query);
        return $data;
    }

    public static function lista_nota_creditos($id_grupo) {
        $query = "SELECT a.*,tsus_desc as tipo,
IF(c.not_id is NULL,'No hay Doc. Afectado encontrado',IF(not_estado=1,'Activo','Inactivo')) as estado
FROM tb_nota_credito a 
LEFT JOIN tb_tipo_sustento b ON a.not_tsus_id=b.tsus_id 
LEFT JOIN tb_devengado c ON a.not_id=c.not_id
WHERE a.id_grupo=$id_grupo";
        $data = DB::select($query);
        return $data;
    }

    // chinitos
    public static function insertGrupComprobanteOse($insert_data) {
        DB::table('tb_comprobantes_ose_grupo')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        return $id;
    }

    public static function uploadComprobantesOse_tmp_create($tabla_tmp) {
        $query_drop = "DROP TABLE IF EXISTS $tabla_tmp;";
        DB::select($query_drop);
        $query = "CREATE TABLE $tabla_tmp  (
  `id_com` int(11) NOT NULL AUTO_INCREMENT,
  `com_tipo_documento` varchar(60) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_doc_iden` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_serie` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `com_numero` int(255) NULL DEFAULT NULL,
  `com_fecha_envio` date NULL DEFAULT NULL,
  `com_descuento` double(12, 2) NULL DEFAULT NULL,
  `com_cod_interno` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_nombres` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_recargo` double(12, 2) NULL DEFAULT NULL,
  `com_gratuito` double(12, 2) NULL DEFAULT NULL,
  `com_igv` double(12, 2) NULL DEFAULT NULL,
  `com_isc` double(12, 2) NULL DEFAULT NULL,
  `com_neto` double(12, 2) NULL DEFAULT NULL,
  `com_total` double(12, 2) NULL DEFAULT NULL,
  `com_tip_moneda` char(3) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_tip_cambio` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_tot_otra_moneda` double(12, 2) NULL DEFAULT NULL,
  `com_observacion` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_pagado` char(2) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_sucursal` char(2) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_adicional_1` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_adicional_2` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_adicional_3` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_adicional_4` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_adicional_5` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_adicional_6` longtext CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL,
  `com_usuario` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `com_fecha_creado` date NULL DEFAULT NULL,
  `com_baja` char(2) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  `id_grupo` int(255) NULL DEFAULT NULL,
  `com_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_com`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_spanish_ci ROW_FORMAT = Compact;";
        DB::select($query);
    }

    public static function uploadComprobantesOse_tmp($insert_data, $table_tmp) {
        DB::table($table_tmp)->insert($insert_data);
    }

    public static function selectComprobanteOse_tmp($table_name) {
        $query = "SELECT IF(c.id_com IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.id_serie FROM (
	SELECT a.*,CASE com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 1 WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 2 WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN '3' ELSE 1 END as serie_estado  
        FROM $table_name a ) as a INNER JOIN tb_serie b ON b.serie_desc=a.com_serie AND b.serie_estado=a.serie_estado
	LEFT JOIN tb_comprobantes_ose c ON a.com_serie=a.com_serie AND a.com_numero=c.com_numero GROUP BY com_serie,com_numero ORDER BY com_serie,com_numero ";
        $data = DB::select($query);
        return $data;
    }

    public static function verificar_exiten_comprobanteOse_nuevos($table_name) {
        $query = "SELECT * FROM (
	SELECT IF(c.id_com IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.id_serie FROM (
	SELECT a.*,CASE com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 1 WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 2 ELSE 1 END as serie_estado 
        FROM $table_name a ) as a INNER JOIN tb_serie b ON b.serie_desc=a.com_serie AND b.serie_estado=a.serie_estado
	LEFT JOIN tb_comprobantes_ose c ON a.com_serie=a.com_serie AND a.com_numero=c.com_numero ORDER BY com_serie,com_numero) as
	p1 where p1.detalle='NUEVO'";
        $data = DB::select($query);
        return $data;
    }

    public static function upload_tb_comprobantesOse($table_name, $grupoId) {
        $query = "insert into tb_comprobantes_ose(com_tipo_documento,
        com_doc_iden,
        com_serie,
        com_numero,
        com_fecha_envio,
        com_descuento,
        com_cod_interno,
        com_nombres,
        com_recargo,
        com_gratuito,
        com_igv,
        com_isc,
        com_neto,
        com_total,
        com_tip_moneda,
        com_tip_cambio,
        com_tot_otra_moneda,
        com_observacion,
        com_pagado,
        com_sucursal,
        com_adicional_1,
        com_adicional_2,
        com_adicional_3,
        com_adicional_4,
        com_adicional_5,
        com_adicional_6,
        com_usuario,
        com_fecha_creado,
        com_baja,
        id_grupo,
        id_serie,
        com_fecha_sistema,
        com_estado)
        SELECT com_tipo_documento,com_doc_iden,com_serie,com_numero,com_fecha_envio,com_descuento,
        com_cod_interno,com_nombres,com_recargo,com_gratuito,com_igv,com_isc,com_neto,com_total,
	com_tip_moneda,com_tip_cambio,com_tot_otra_moneda,com_observacion,com_pagado,
        com_sucursal,com_adicional_1,com_adicional_2,com_adicional_3,com_adicional_4,com_adicional_5,
	com_adicional_6,com_usuario,com_fecha_creado,com_baja,$grupoId,id_serie,NOW(),com_estado FROM (
        SELECT IF(c.id_com IS NOT NULL,'ENCONTRADO','NUEVO') as detalle, a.*,b.id_serie FROM (
	SELECT a.*,CASE com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 1 WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 2 ELSE 1 END as serie_estado 
        FROM $table_name a ) as a INNER JOIN tb_serie b ON b.serie_desc=a.com_serie AND b.serie_estado=a.serie_estado
	LEFT JOIN tb_comprobantes_ose c ON a.com_serie=a.com_serie AND a.com_numero=c.com_numero ORDER BY com_serie,com_numero) as p1 where p1.detalle='NUEVO';";
        DB::select($query);
    }

    public static function lista_grupo_comprobantes_ose() {
        $query = "SELECT
          pg.id_grupo,
          pg.grupo_fecha,
          CONCAT( p.per_pate, ' ', p.per_mate, ', ', p.per_nomb ) AS nombre,
          COUNT( ose.id_com ) AS cant_total,
          COUNT( ose.id_serie ) AS cant_sin_serie 
        FROM
          tb_comprobantes_ose_grupo pg
          INNER JOIN tb_personal p ON pg.id_per = p.id
          LEFT JOIN tb_comprobantes_ose ose ON pg.id_grupo = ose.id_grupo 
        WHERE
          pg.grupo_estado = 1 AND ose.com_estado <> 0 
        GROUP BY
          pg.id_grupo,
          pg.grupo_fecha,
          nombre 
        ORDER BY
          pg.id_grupo DESC";
        $data = DB::select($query);
        return $data;
    }

    public static function lista_comprobantes_ose($id_grupo) {
        $query = "SELECT a.*,IF(com_estado=1,'Activo','Inactivo') as estado
        FROM tb_comprobantes_ose a 
        WHERE a.id_grupo=$id_grupo";
        $data = DB::select($query);
        return $data;
    }

}
