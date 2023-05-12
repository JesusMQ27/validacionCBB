<?php

namespace App;

ini_set('max_execution_time', 300); //3 minutes

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contabilidad extends Model {

    public static function contabilidad_informe($fecha_ini, $fecha_fin, $serie) {//aqui jesus
        $db_ext = \DB::connection('mysql');
        $query = "SELECT emision,serie,del,al,IF(faltantes IS NULL,'',faltantes) as faltantes,IF(pension IS NULL,'0',pension) as pension,IF(matricula IS NULL,'0',matricula) as matricula,
        IF(derecho_admision IS NULL,'0',derecho_admision) as derecho_admision,IF(anticipo_pension IS NULL,'0',anticipo_pension) as anticipo_pension,
        IF(anticipo_matricula IS NULL,'0',anticipo_matricula) as anticipo_matricula,IF(mora IS NULL,'0',mora) as mora,
        IF(tramite IS NULL,'0',tramite) as tramite,IF(taller IS NULL,'0',taller) as taller,IF(devengados IS NULL,'0',devengados) as devengados,
        IF(total IS NULL,'0',total) as total
        FROM (
        SELECT p1.com_fecha_envio as emision,p1.com_serie as serie,min(p1.com_numero) as del,max(p1.com_numero) as al,
        fnc_obtener_numeros_faltantes(p1.com_tipo_documento,p1.com_serie,concat(p1.com_fecha_envio,'')) as faltantes,
        SUM(p1.pension) as pension,SUM(p1.matricula) as matricula,SUM(p1.derecho_admision) as derecho_admision,SUM(p1.anticipo_pension) as anticipo_pension,
        SUM(p1.anticipo_matricula) as anticipo_matricula,SUM(p1.mora) as mora,SUM(p1.tramite) as tramite,SUM(p1.taller) as taller,SUM(p1.devengados) as devengados,
        SUM(p1.pension+p1.matricula+p1.derecho_admision+p1.anticipo_pension+p1.anticipo_matricula+p1.mora+p1.tramite+p1.taller+p1.devengados) as total
        FROM(
        SELECT t1.com_fecha_envio,t1.com_serie,t1.com_numero,pago_concepto,nroMes,com_tipo_documento,
        CASE WHEN pago_concepto LIKE '%PENSION%' THEN IF(MONTH(com_fecha_envio)>=nroMes,pago_monto_cancelado,'0') ELSE '0' END AS pension,
        CASE WHEN pago_concepto LIKE '%MATRÍCULA%' THEN IF(YEAR(com_fecha_envio)>=anio,pago_monto_cancelado,'0') ELSE '0' END AS matricula,
        CASE WHEN pago_concepto LIKE '%DERECHO%' THEN pago_monto_cancelado ELSE '0' END AS derecho_admision,
        CASE WHEN pago_concepto LIKE '%PENSION%' THEN IF(MONTH(com_fecha_envio)<nroMes,pago_monto_cancelado,'0') ELSE '0' END AS anticipo_pension,
        CASE WHEN pago_concepto LIKE '%MATRÍCULA%' THEN IF(YEAR(com_fecha_envio)<=anio,pago_monto_cancelado,'0') ELSE '0' END AS anticipo_matricula,
        CASE WHEN pago_concepto LIKE '%MORA%' THEN pago_monto_cancelado ELSE '0' END AS mora,
        CASE WHEN pago_concepto LIKE '%TRÁMITE%' THEN pago_monto_cancelado ELSE '0' END AS tramite,
        CASE WHEN pago_concepto LIKE '%TALLER%' THEN pago_monto_cancelado ELSE '0' END AS taller,
        CASE WHEN pago_concepto LIKE '%DEVENGADOS%' THEN pago_monto_cancelado ELSE '0' END AS devengados
        FROM (
        SELECT com_fecha_envio,com_serie,com_numero,
        pago_concepto,CASE WHEN pago_concepto LIKE '%ENERO%' THEN '01' 
        WHEN pago_concepto LIKE '%ENERO%' THEN '01'
        WHEN pago_concepto LIKE '%FEBREO%' THEN '02'
        WHEN pago_concepto LIKE '%MARZO%' THEN '03' 
        WHEN pago_concepto LIKE '%ABRIL%' THEN '04' 
        WHEN pago_concepto LIKE '%MAYO%' THEN '05' 
        WHEN pago_concepto LIKE '%JUNIO%' THEN '06' 
        WHEN pago_concepto LIKE '%JULIO%' THEN '07' 
        WHEN pago_concepto LIKE '%AGOSTO%' THEN '08'
        WHEN pago_concepto LIKE '%SEPTIEMBRE%' THEN '09'
        WHEN pago_concepto LIKE '%OCTUBRE%' THEN '10'
        WHEN pago_concepto LIKE '%NOVIEMBRE%' THEN '11'
        WHEN pago_concepto LIKE '%DICIEMBRE%' THEN '12' ELSE '' END nroMes,
        CASE WHEN pago_concepto LIKE '%MATRICULA%' THEN TRIM(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(pago_concepto,'-',1),'Matrícula ',''),'CAR',''),'COL',''),'SJL','')) ELSE '' END AS anio,
        pago_monto,pago_monto_cancelado,com_tipo_documento
        FROM tb_comprobantes_ose a
        INNER JOIN tb_lista_pago b ON CONCAT(a.com_serie,'-',a.com_numero)=b.pago_boleta COLLATE latin1_swedish_ci
        WHERE 
        com_fecha_envio >= STR_TO_DATE('$fecha_ini', '%Y-%m-%d') COLLATE latin1_swedish_ci and 
        com_fecha_envio <= STR_TO_DATE('$fecha_fin', '%Y-%m-%d') COLLATE latin1_swedish_ci AND pago_tipo='BOL') AS t1) as p1 WHERE 1=1 ";
        if ($serie != "0") {
            $query .= " AND p1.com_serie ='$serie' ";
        }
        $query .= " GROUP BY p1.com_fecha_envio,p1.com_serie
        ORDER BY p1.com_fecha_envio,p1.com_serie) AS a1 
        GROUP BY a1.emision,a1.serie
        ORDER BY a1.emision,a1.serie;";
        $lista = $db_ext->select($query);
        return collect($lista);
    }

    public static function contabilidad_cbb_detallado($fecha_ini, $fecha_fin, $serie) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT boleta,fecha,serie,numero,tipo,detalle,IF(numero_afectado IS NULL,'',numero_afectado) as numero_afectado,
        IF(fecha_num_afectado IS NULL,'',fecha_num_afectado) AS fecha_num_afectado,dni,alumno,direccion,
        IF(ope_gravadas IS NULL,'0',ope_gravadas) AS ope_gravadas,IF(ope_inafectas IS NULL,'0',ope_inafectas) AS ope_inafectas,
        IF(ope_exoneradas IS NULL,'0',ope_exoneradas) AS ope_exoneradas,IF(ope_gratuitas IS NULL,'0',ope_gratuitas) AS ope_gratuitas,
        IF(venta IS NULL,'0',venta) AS venta,descripcion,cantidad,IF(valor IS NULL,'0',valor) AS valor,IF(ventas IS NULL,'0',ventas) AS ventas
        FROM (
        SELECT CONCAT(com_serie,'-',LPAD(com_numero,8,'0')) as boleta,com_fecha_envio as fecha,com_serie as serie,LPAD(com_numero,8,'0') as numero,
        CASE com_tipo_documento WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN '08' WHEN 'NOTA DE CREDITO ELECTRÓNICA' THEN '07' WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN '03' WHEN 'FACTURA ELECTRÓNICA' THEN '01' ELSE '' END AS tipo,
        CASE com_tipo_documento WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN 'Nota de debito' WHEN 'NOTA DE CREDITO ELECTRÓNICA' THEN 'Nota de Credito' WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 'Boleta' WHEN 'FACTURA ELECTRÓNICA' THEN 'Factura' ELSE '' END AS detalle,
        CASE com_tipo_documento WHEN 'NOTA DE CREDITO ELECTRÓNICA' THEN (SELECT not_doc_afecta FROM tb_nota_credito	WHERE not_documento=CONCAT(com_serie,'-',com_numero)) ELSE '' END AS numero_afectado,
        CASE com_tipo_documento WHEN 'NOTA DE CREDITO ELECTRÓNICA' THEN (SELECT deve_fecha_emicar/*deve_fecha*/ FROM tb_nota_credito a INNER JOIN tb_devengado b ON a.not_id=b.not_id WHERE not_documento=CONCAT(com_serie,'-',com_numero)) ELSE '' END AS fecha_num_afectado,
        com_doc_iden AS dni,UPPER(com_nombres) AS alumno,'-' AS direccion,
        IF(com_igv>0,com_neto,'0') AS ope_gravadas,
        IF(com_igv=0,com_neto,'0') AS ope_inafectas,
        IF(com_recargo>0,com_recargo,'0') AS ope_exoneradas,
        IF(com_gratuito>0,com_gratuito,'0') AS ope_gratuitas,
        IF(com_neto>0,com_neto,'0') AS venta,
        '' AS descripcion,
        count(*) AS cantidad,
        IF(com_neto>0,com_neto,'0') AS valor,
        IF(com_total>0,com_total,'0') AS ventas
        FROM tb_comprobantes_ose WHERE 
        com_fecha_envio >= STR_TO_DATE('$fecha_ini', '%Y-%m-%d') COLLATE latin1_swedish_ci AND 
        com_fecha_envio <= STR_TO_DATE('$fecha_fin', '%Y-%m-%d') COLLATE latin1_swedish_ci ";
        if ($serie != "0") {
            $query .= " AND com_serie IN ('" . $serie . "') ";
        }
        $query .= "GROUP BY com_fecha_envio,com_serie,com_numero,com_tipo_documento
        ORDER BY com_fecha_envio,com_serie,com_numero,com_tipo_documento) AS p1
        ORDER BY fecha,serie,numero,tipo;";
        $lista = $db_ext->select($query);
        return collect($lista);
    }

//crea la tabla temporal e inserta la informacion
    public static function tmp_estado_cuenta($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_estado_cuenta_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_estado_cuenta_" . $codigo . "( 
    id_ec int (11) primary key not null AUTO_INCREMENT, 
    ec_servicio varchar(100), 
    ec_dni varchar(15), 
    ec_nombre varchar(40),
    ec_documento  varchar(20), 
    ec_fecha_venci DATE, 
    ec_moneda varchar(3), 
    ec_total double(8,2),
    ec_importe double(8,2),
    ec_mora double(8,2),
    ec_fecha_proce DATE,
    ec_fecha_pago DATE, 
    ec_forma_pago varchar(20), 
    ec_oficina varchar(4),
    ec_operacion varchar(12), 
    ec_referencia varchar(100),
    ec_tipo_banco varchar(20),
    ec_estado char(1)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_estado_cuenta($tabla, $data) {
//$db_ext = \DB::connection('mysql');
//DB::table('tb_pagos')->insert($insert_data);

        $arreglo_1 = ['ec_servicio' => $data["ec_servicio"],
            'ec_dni' => $data["ec_dni"],
            'ec_nombre' => $data["ec_nombre"],
            'ec_documento' => $data["ec_documento"],
            'ec_fecha_venci' => $data["ec_fecha_venci"],
            'ec_moneda' => $data["ec_moneda"],
            'ec_total' => $data["ec_importe"],
            'ec_importe' => $data["ec_importe"],
            'ec_mora' => $data["ec_mora"],
            'ec_fecha_proce' => $data["ec_fecha_proce"],
            'ec_fecha_pago' => $data["ec_fecha_pago"],
            'ec_forma_pago' => $data["ec_forma_pago"],
            'ec_oficina' => $data["ec_oficina"],
            'ec_operacion' => $data["ec_operacion"],
            'ec_referencia' => $data["ec_referencia"],
            'ec_tipo_banco' => $data["ec_tipo_banco"],
            'ec_estado' => $data["ec_estado"]];

        DB::table($tabla)->insert($arreglo_1);
//print_r(trim($data["ec_mora"]));
        if (trim($data["ec_mora"]) !== "0.0" && trim($data["ec_mora"]) !== "" && trim($data["ec_mora"]) > 0) {
//print_r("holaaaaa");
            $arreglo_2 = ['ec_servicio' => $data["ec_servicio"],
                'ec_dni' => $data["ec_dni"],
                'ec_nombre' => $data["ec_nombre"],
                'ec_documento' => $data["ec_documento"],
                'ec_fecha_venci' => $data["ec_fecha_venci"],
                'ec_moneda' => $data["ec_moneda"],
                'ec_total' => $data["ec_mora"],
                'ec_importe' => $data["ec_importe"],
                'ec_mora' => $data["ec_mora"],
                'ec_fecha_proce' => $data["ec_fecha_proce"],
                'ec_fecha_pago' => $data["ec_fecha_pago"],
                'ec_forma_pago' => $data["ec_forma_pago"],
                'ec_oficina' => $data["ec_oficina"],
                'ec_operacion' => $data["ec_operacion"],
                'ec_referencia' => $data["ec_referencia"],
                'ec_tipo_banco' => $data["ec_tipo_banco"],
                'ec_estado' => $data["ec_estado"]];
            DB::table($tabla)->insert($arreglo_2);
        }

//$db_ext->table($tabla)->insert($data);
    }

    public static function tmp_pagos_alexia($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function tmp_pagos_alexia_concar($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia($tabla, $data) {
        $db_ext = \DB::connection('mysql');
//DB::table('tb_pagos')->insert($insert_data);
        $db_ext->table($tabla)->insert($data);
    }

    public static function carga_pagos_alexia_concar($tabla, $data) {
        $db_ext = \DB::connection('mysql');
//DB::table('tb_pagos')->insert($insert_data);
        $db_ext->table($tabla)->insert($data);
    }

//Jesus M
    public static function crear_temporal_archivos($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_lista_archivo_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_lista_archivo_" . $codigo . "( 
    id_archivo int(11) primary key not null AUTO_INCREMENT, 
    arc_alumno varchar(30), 
    arc_dni varchar(12),
    arc_servicio varchar(60), 
    arc_documento varchar(20), 
    arc_vencimiento date,
    arc_moneda char(3),
    arc_importe_origen double(8,2), 
    arc_importe_depositado double(8,2),
    arc_importe_mora double(8,2),
    arc_fecha_proceso date,
    arc_hora_proceso varchar(5),
    arc_fecha_pago date,
    arc_forma_pago varchar(20),
    arc_oficina varchar(6),
    arc_nro_operacion varchar(13),
    arc_referencia varchar(30),
    arc_banco varchar(12),
    arc_estado char(1),
    INDEX (arc_nro_operacion)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_temporal_archivos($tabla, $data) {
//$db_ext = \DB::connection('mysql');
//$db_ext->table($tabla)->insert($data);
        $insert = $data['arc_nro_operacion'];
        $insert2 = $data['arc_referencia'];
        $data_mysql = DB::connection('mysql')->table($tabla)->where('arc_documento', "$insert")->where('arc_referencia', "$insert2")->first();

        if (count((array) $data_mysql) == 0) {
            DB::connection('mysql')->table($tabla)->insert($data);
        }
    }

    public static function carga_temporal_archivos2($tabla, $data) {
//$db_ext = \DB::connection('mysql');
//$db_ext->table($tabla)->insert($data);
        $insert = $data['arc_nro_operacion'];
        $insert2 = $data['arc_referencia'];
        $data_mysql = DB::connection('mysql')->table($tabla)->where('arc_nro_operacion', "$insert")->where('arc_referencia', "$insert2")->first();
//dd($data);
        if (count((array) $data_mysql) == 0) {
            DB::connection('mysql')->table($tabla)->insert($data);
        }
    }

//Jesus M
    public static function carga_temporal_archivos3($tabla, $data) {
        $insert = $data['arc_nro_operacion'];
        $insert2 = $data['arc_referencia'];
        $insert3 = $data['arc_servicio'];
//$data_mysql = DB::connection('mysql')->table($tabla)->where('arc_nro_operacion', "$insert")->where('arc_referencia', "$insert2")->first();
        $data_mysql = DB::connection('mysql')->table($tabla)->where('arc_nro_operacion', "$insert")->where('arc_referencia', "$insert2")->where('arc_servicio', "$insert3")->first();
        if (count((array) $data_mysql) == 0) {
            DB::connection('mysql')->table($tabla)->insert($data);
        }
    }

    public static function carga_temporal_archivos4($tabla, $data) {
        $insert = $data['arc_dni'];
        $insert2 = $data['arc_referencia'];
        $insert3 = $data['arc_servicio'];
//$data_mysql = DB::connection('mysql')->table($tabla)->where('arc_nro_operacion', "$insert")->where('arc_referencia', "$insert2")->first();
        $data_mysql = DB::connection('mysql')->table($tabla)->where('arc_nro_operacion', "$insert")->where('arc_referencia', "$insert2")->where('arc_servicio', "$insert3")->first();
        if (count((array) $data_mysql) == 0) {
            DB::connection('mysql')->table($tabla)->insert($data);
        }
    }

    public static function eecc_lista_reporte($tabla, $usuario) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT trim(arc_servicio) as arc_servicio,arc_dni,trim(arc_alumno) as arc_alumno ,arc_documento,
DATE_FORMAT(arc_vencimiento,'%d/%m/%Y') as arc_vencimiento,arc_moneda,arc_importe_origen,arc_importe_mora,
DATE_FORMAT(arc_fecha_proceso,'%d/%m/%Y') as arc_fecha_proceso,arc_hora_proceso,DATE_FORMAT(arc_fecha_pago,'%d/%m/%Y') as arc_fecha_pago,
arc_forma_pago,arc_oficina,arc_nro_operacion,arc_referencia,arc_banco
FROM db_cbb." . $tabla . "_" . $usuario;
        $lista = $db_ext->select($query);
        return collect($lista);
    }

    public static function insertarGrupo($insert_data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table('tb_contabilidad_grupo')->insert($insert_data);
        $id = $db_ext->getPdo()->lastInsertId();
        return $id;
    }

    public static function insertarEecc_pagos($per_id, $fecha_inicio, $fecha_fin, $grupo) {
        $db_ext = \DB::connection('mysql');

        $query = "INSERT INTO db_cbb.tb_contabilidad_eecc_pagos(concep_servicio,concep_dni,concep_nombre,concep_documento,concep_fecha_venci,concep_moneda,concep_total,concep_importe,concep_mora,
concep_fecha_proce,concep_fecha_pago,concep_forma_pago,concep_oficina,concep_operacion,concep_referencia,congru_id,concep_banco,concep_estado)
SELECT a.* FROM (
SELECT a.ec_servicio,a.ec_dni as dni,ec_nombre as nombre,ec_documento as documento,ec_fecha_venci,ec_moneda,ec_total as total,ec_importe as importe,ec_mora as mora,ec_fecha_proce,ec_fecha_pago as fecha_pago,ec_forma_pago,ec_oficina,ec_operacion,ec_referencia,$grupo as grupo,ec_tipo_banco as banco,'1'
FROM db_cbb.tmp_estado_cuenta_$per_id a
LEFT JOIN db_cbb.tmp_pagos_alexia_$per_id b ON a.ec_dni=b.pa_ruc_dni AND a.ec_fecha_pago=b.pa_fecha_pago AND a.ec_total=b.pa_total
WHERE ec_fecha_pago>='$fecha_inicio' AND ec_fecha_pago<='$fecha_fin' AND b.id_pa IS NULL) as a
LEFT JOIN tb_contabilidad_eecc_pagos b ON a.dni=b.concep_dni AND a.fecha_pago=b.concep_fecha_pago AND a.importe=b.concep_importe
WHERE b.conep_id IS NULL;";
        $data = $db_ext->insert($query);
        if ($data) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function listaEecc_pagos($per_id, $fecha_inicio, $fecha_fin) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT a.*,IF(b.conep_id IS NULL,'black','red') AS color,IF(b.conep_id IS NULL,1,2) AS orden FROM (
            SELECT a.ec_servicio,a.ec_dni as dni,ec_nombre as nombre,ec_documento as documento,ec_fecha_venci,ec_moneda,ec_total as total,ec_importe as importe,ec_mora as mora,ec_fecha_proce,ec_fecha_pago as fecha_pago,ec_forma_pago,ec_oficina,ec_operacion,ec_referencia,ec_tipo_banco as banco
FROM db_cbb.tmp_estado_cuenta_$per_id a
LEFT JOIN db_cbb.tmp_pagos_alexia_$per_id b ON a.ec_dni=b.pa_ruc_dni AND a.ec_fecha_pago=b.pa_fecha_pago AND a.ec_total=b.pa_total
WHERE ec_fecha_pago>='$fecha_inicio' AND ec_fecha_pago<='$fecha_fin' AND b.id_pa IS NULL) AS a 
 LEFT JOIN tb_contabilidad_eecc_pagos b ON a.dni=b.concep_dni AND a.fecha_pago=b.concep_fecha_pago AND a.importe=b.concep_importe ORDER BY orden;";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function lista_tabla_eecc_pagos_grupos($grupo) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT concep_fecha_venci AS fecha_venci,concep_fecha_pago AS fecha_pago,concep_dni as dni,concep_nombre AS nombre,concep_documento AS documento,concep_moneda AS moneda,
concep_total AS total,concep_importe AS importe,concep_mora as mora,concep_fecha_proce AS fecha_proce,concep_forma_pago AS forma_pago,
concep_oficina AS oficina,concep_operacion AS operacion,concep_referencia AS referencia,concep_banco as banco,IF(a.concep_estado=1,'Activo','Anulado') AS estado
FROM tb_contabilidad_eecc_pagos a WHERE a.congru_id=$grupo;";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function crea_tmp_facturacion($codigo) {
        $db_ext = \DB::connection('mysql');

        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_facturacion_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_facturacion_" . $codigo . "( 
    id_cbb int(11) primary key not null AUTO_INCREMENT, 
    fact_boleta varchar(15), 
    fact_fecha date,
    fact_serie varchar(4), 
    fact_numero varchar(8), 
    fact_tipo_documento char(3),
    fact_detalle varchar(18),
    fact_num_afectado char(15),
    fact_fech_afectado date,
    fact_dni varchar(12),
    fact_nombres varchar(50),
    fact_direccion longtext,
    fact_ope_gravadas double(8,2), 
    fact_ope_inafectas double(8,2),
    fact_ope_exoneradas double(8,2),
    fact_ope_gratuitas double(8,2),
    fact_venta double(8,2),
    fact_descripcion varchar(60),
    fact_cantidad int(3),
    fact_valor double(8,2),
    fact_ventas double(8,2),
    INDEX(fact_boleta),
    INDEX(fact_fecha),
    INDEX(fact_serie),
    INDEX(fact_numero),
    INDEX(fact_dni),
    INDEX(fact_venta)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);

        $query_partitions = "ALTER TABLE tmp_cbb_facturacion_" . $codigo . "  PARTITION BY RANGE (id_cbb)
            (
              PARTITION p1 VALUES LESS THAN (1000),
              PARTITION p2 VALUES LESS THAN (2000), 
              PARTITION p3 VALUES LESS THAN (3000),  
              PARTITION p4 VALUES LESS THAN (4000),
              PARTITION p5 VALUES LESS THAN (5000),
              PARTITION p6 VALUES LESS THAN (6000),
              PARTITION p7 VALUES LESS THAN (7000),
              PARTITION p8 VALUES LESS THAN (8000),
              PARTITION p9 VALUES LESS THAN (9000),
              PARTITION p10 VALUES LESS THAN (10000),
              PARTITION p11 VALUES LESS THAN MAXVALUE 
            );";
        $db_ext->select($query_partitions);
    }

    public static function inserta_tmp_facturacion($tabla, $data) {
        DB::table($tabla)->insert($data);
    }

    public static function insertarpagos_facturacion($per_id, $fecha_inicio, $fecha_fin, $grupo) {
        $db_ext = \DB::connection('mysql');
        /* $query = "INSERT INTO db_cbb.tb_contabilidad_pagos_facturacion(conpf_fecha_pago,conpf_fecha_emi,
          conpf_boleta,conpf_serie,conpf_numero,conpf_ruc_dni,conpf_cliente,conpf_concepto,conpf_descuento,conpf_base_imp,
          conpf_igv,conpf_total,conpf_cancelado,conpf_tipo,conpf_esta_compro,conpf_banco,congru_id,conpf_estado)

          SELECT a.pa_fecha_pago,pa_fecha_emi,CONCAT(trim(pa_serie),'-',trim(pa_numero)) AS boleta,trim(pa_serie) as serie,trim(pa_numero) AS numero,
          pa_ruc_dni AS documento,UPPER(pa_cliente) as pa_cliente,pa_concepto,pa_descuento,pa_base_imp,pa_igv,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,$grupo as grupo,'1'
          FROM tmp_pagos_alexia_$per_id a
          LEFT JOIN tmp_cbb_facturacion_$per_id b ON a.pa_ruc_dni=b.fact_dni AND a.pa_fecha_pago=b.fact_fecha AND a.pa_total=b.fact_venta
          WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' AND b.id_cbb IS NULL;"; */

        $query = "INSERT INTO db_cbb.tb_contabilidad_pagos_facturacion(conpf_fecha_pago,conpf_fecha_emi,
            conpf_boleta,conpf_serie,conpf_numero,conpf_ruc_dni,conpf_cliente,conpf_concepto,conpf_descuento,conpf_base_imp,
            conpf_igv,conpf_total,conpf_cancelado,conpf_tipo,conpf_esta_compro,conpf_banco,congru_id,conpf_estado) 
            
SELECT a.* FROM (
SELECT a.pa_fecha_pago as fecha_pago,pa_fecha_emi,CONCAT(trim(pa_serie),'-',trim(pa_numero)) AS boleta,trim(pa_serie) as serie,trim(pa_numero) AS numero,
pa_ruc_dni AS documento,UPPER(pa_cliente) as cliente,pa_concepto,pa_descuento,pa_base_imp,pa_igv,pa_total as total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,$grupo as grupo,'1'
          FROM tmp_pagos_alexia_$per_id a
          LEFT JOIN tmp_cbb_facturacion_$per_id b ON a.pa_ruc_dni=b.fact_dni AND a.pa_fecha_pago=b.fact_fecha AND a.pa_total=b.fact_venta
          WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' AND b.id_cbb IS NULL) AS a
LEFT JOIN tb_contabilidad_pagos_facturacion b ON a.fecha_pago=b.conpf_fecha_pago AND a.boleta=b.conpf_boleta AND a.documento=b.conpf_ruc_dni AND a.total=b.conpf_total
WHERE b.conpf_id IS NULL ";


        $data = $db_ext->insert($query);
        if ($data) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function listaPagos_facturacion($per_id, $fecha_inicio, $fecha_fin) {
        $db_ext = \DB::connection('mysql');
        /* $query = "SELECT a.*,IF(b.conpf_id IS NULL,'black','red') AS color,IF(b.conpf_id IS NULL,1,2) as orden FROM (
          SELECT a.pa_fecha_pago as fecha_pago,pa_fecha_emi,CONCAT(trim(pa_serie),'-',trim(pa_numero)) AS boleta,trim(pa_serie) as serie,trim(pa_numero) AS numero,
          pa_ruc_dni AS documento,UPPER(pa_cliente) as cliente,pa_concepto,pa_descuento,pa_base_imp,pa_igv,pa_total as total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco
          FROM tmp_pagos_alexia_$per_id a
          left JOIN tmp_cbb_facturacion_$per_id b ON a.pa_ruc_dni=b.fact_dni AND a.pa_fecha_pago=b.fact_fecha AND a.pa_total=b.fact_venta
          WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' AND b.id_cbb IS NULL) AS a
          LEFT JOIN tb_contabilidad_pagos_facturacion b ON a.fecha_pago=b.conpf_fecha_pago AND a.boleta=b.conpf_boleta AND a.documento=b.conpf_ruc_dni AND a.total=b.conpf_total ORDER BY orden,fecha_pago"; */

        $query = "SELECT a.*,IF(b.conpf_id IS NULL,'black','red') AS color,IF(b.conpf_id IS NULL,1,2) as orden FROM (
            SELECT a.pa_fecha_pago as fecha_pago,pa_fecha_emi,CONCAT(trim(pa_serie),'-',trim(pa_numero)) AS boleta,trim(pa_serie) as serie,trim(pa_numero) AS numero,
pa_ruc_dni AS documento,UPPER(pa_cliente) as cliente,pa_concepto,pa_descuento,pa_base_imp,pa_igv,pa_total as total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
IF((SELECT fact_serie FROM tmp_cbb_facturacion_$per_id WHERE fact_serie=a.pa_serie AND fact_numero=a.pa_numero) IS NULL
,'Comprobante no existe',
IF((
SELECT IF(pa_fecha_pago=fact_fecha,0,1) AS resp
FROM tmp_pagos_alexia_$per_id c
INNER JOIN tmp_cbb_facturacion_$per_id b ON c.pa_serie=b.fact_serie AND c.pa_numero=b.fact_numero
WHERE pa_serie=a.pa_serie AND pa_numero=a.pa_numero
)=1,'Fechas no coinciden',
IF((
SELECT IF(pa_total=fact_venta,0,1) AS resp
FROM tmp_pagos_alexia_$per_id c
INNER JOIN tmp_cbb_facturacion_$per_id b ON c.pa_serie=b.fact_serie AND c.pa_numero=b.fact_numero
WHERE pa_serie=a.pa_serie AND pa_numero=a.pa_numero
)=1,'Montos no coinciden',
IF(
(
SELECT IF(pa_ruc_dni=fact_dni,0,1) AS resp
FROM tmp_pagos_alexia_$per_id c
INNER JOIN tmp_cbb_facturacion_$per_id b ON c.pa_serie=b.fact_serie AND c.pa_numero=b.fact_numero
WHERE pa_serie=a.pa_serie AND pa_numero=a.pa_numero
)=1,'DNI o Ruc no coinciden',''
)
)
)
) AS respuesta

FROM tmp_pagos_alexia_$per_id a
left JOIN tmp_cbb_facturacion_$per_id b ON a.pa_ruc_dni=b.fact_dni AND a.pa_fecha_pago=b.fact_fecha AND a.pa_total=b.fact_venta
WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' AND b.id_cbb IS NULL

) AS a
LEFT JOIN tb_contabilidad_pagos_facturacion b ON a.fecha_pago=b.conpf_fecha_pago AND a.boleta=b.conpf_boleta AND a.documento=b.conpf_ruc_dni AND a.total=b.conpf_total ORDER BY orden,fecha_pago";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function listaPagos_facturacion_grupos($grupo) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT conpf_fecha_emi AS fecha_emi,conpf_fecha_pago AS fecha_pago,conpf_boleta AS boleta,conpf_serie as serie,conpf_numero as numero,
conpf_ruc_dni AS documento,conpf_cliente AS cliente,conpf_concepto as concepto,conpf_descuento as descuento,conpf_base_imp as base_imp,
conpf_igv AS igv,conpf_total AS total,conpf_cancelado AS cancelado,conpf_tipo AS tipo,conpf_esta_compro estaCompro,conpf_banco AS banco,
IF(conpf_estado=1,'Activo','Anulado') AS estado
FROM tb_contabilidad_pagos_facturacion a
WHERE a.congru_id=$grupo ";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function genera_data_concar($fecha_inicio, $fecha_fin) {
        $db_ext = \DB::connection('mysql_40_3');

        $query = "SELECT p3.boleta,SUBSTRING_INDEX(p3.boleta,'-',1) AS serie,SUBSTRING_INDEX(p3.boleta,'-',-1) AS numero,p3.fecha,p3.descripcion,
CASE tipo WHEN 'MATRICULA' THEN IF(p3.cadena>year(p3.fecha),'ANTICIPO MATRICULA','MATRICULA') 
ELSE tipo END AS tipo,p3.venta
FROM (
SELECT p2.*,
CASE p2.tipo WHEN 'MATRICULA'
THEN 
trim(replace(SUBSTRING_INDEX(replace(replace(replace(replace(replace(replace(replace(replace(replace(p2.descripcion,'Matricula',''),'-',''),'COLONIAL ',''),'CARABAYLLO ',''),CONCAT('Diciembre ',year(curdate())-1),''),'Descuento',''),'CAR ',''),'COL ',''),'SJL ',''),',',1),'Matricula ',''))
 ELSE '' END as cadena
 FROM (
SELECT p1.boleta,p1.fecha,p1.descripcion,
CASE 
WHEN LOCATE('Anticipo Pension',p1.descripcion)>0 THEN 'ANTICIPO PENSION'
WHEN LOCATE('Pension',p1.descripcion)>0 THEN 'PENSION'
WHEN LOCATE('mora',p1.descripcion)>0 THEN 'MORA' 
WHEN LOCATE('Matricula',p1.descripcion)>0 THEN 'MATRICULA' 
WHEN LOCATE('Taller',p1.descripcion)>0 OR LOCATE('Talleres',p1.descripcion)>0 OR LOCATE('Taller Verano',p1.descripcion)>0 THEN 'TALLER' 
ELSE 'TRAMITES VR'
 END tipo,venta
 FROM (
SELECT 
c1.boleta,c1.fecha,
c1.serie,c1.numero,c1.tipo,c1.detalle,c1.numero_afectado,c1.fecha_num_afectado,c1.dni,
UPPER(c1.alumno) as persona,c1.direccion,
IF(c1.ope_gravadas is null,'0',c1.ope_gravadas) AS ope_gravadas,
IF(c1.ope_inafectas is null,'0',c1.ope_inafectas) AS ope_inafectas,
IF(c1.ope_exoneradas is null,'0',c1.ope_exoneradas) as ope_exoneradas,
IF(c1.ope_gratuitas is null,'0',c1.ope_gratuitas) as ope_gratuitas,
c1.venta,
c1.descripcion,
c1.cantidad,
c1.valor,c1.ventas
FROM (
select a.num_documento as boleta,a.dbv_fec_emision as fecha,SUBSTRING_INDEX(a.num_documento,'-',1) as serie,
SUBSTRING_INDEX(a.num_documento,'-',-1) as numero,a.dbv_cod_tip_documento as tipo,if(a.dbv_cod_tip_documento='03','Boleta','') AS detalle,'' AS numero_afectado,'' AS fecha_num_afectado, dcr_num_documento AS dni,
a.dcr_raz_social AS alumno,a.dcr_direccion as direccion,a.tbv_tot_ven_ope_gravadas AS ope_gravadas,a.tbv_tot_ven_ope_inafectas as ope_inafectas,tbv_tot_ven_ope_exoneradas as ope_exoneradas,tbv_tot_ven_ope_gratuitas as ope_gratuitas,
a.tbv_tot_venta as venta,GROUP_CONCAT(REPLACE(ddi_descripcion,'\n',' ')) as descripcion,SUM(ddi_cantidad) AS cantidad,sum(ddi_valor) as valor,sum(ddi_val_venta) as ventas,a.IDETRANSFERENCIA
FROM db_cpe.tb_boleta a, db_cpe.tb_boleta_detalle b 
WHERE a.num_documento=b.num_documento AND dbv_fec_emision>='$fecha_inicio' AND dbv_fec_emision<='$fecha_fin' 

AND substring(a.num_documento,1,4) in ('B017','B044','B020','B054','B029','B008','B013','B053') 
GROUP BY a.num_documento,a.IDETRANSFERENCIA) AS c1,db_cpe.cpe_transferencia_doc c 
WHERE c1.boleta=c.cod_cpe AND c1.IDETRANSFERENCIA=c.IDETRANSFERENCIA AND c.STSTRANS=1) as p1
) AS p2
) AS p3";

//B034,B051,B052 se eliminaron
        try {
            $data = $db_ext->select($query);
        } catch (\PDOException $error) {
            $data = [];
        }
        return $data;
    }

    public static function buscar_banco_x_boleta($boleta, $tipoDocu, $codigo) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT * FROM tmp_pagos_alexia_concar_$codigo WHERE CONCAT(trim(pa_serie),'-',trim(pa_numero))='" . trim($boleta) . "' AND pa_tipo='$tipoDocu';";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function lista_devengados_anio($anio, $fecha_fin) {
        /*        $query = "SELECT p1.fecha,p1.dni,p1.grado,
          IF(p1.fecha>='2017-03-01',IF(p1.boleta='Boleta','Boleta',CONCAT(SUBSTRING_INDEX(p1.boleta,'-',1),'-',LPAD(SUBSTRING_INDEX(p1.boleta,'-',-1),8,'0'))),IF(p1.boleta='Boleta','Boleta',p1.boleta)) AS boleta,
          p1.alumno,
          if(p1.cuota is null,
          case MONTH(p1.fecha) WHEN 1 THEN 'ENERO'
          WHEN 2 THEN 'FEBRERO'
          WHEN 3 THEN 'MARZO'
          WHEN 4 THEN 'ABRIL'
          WHEN 5 THEN 'MAYO'
          WHEN 6 THEN 'JUNIO'
          WHEN 7 THEN 'JULIO'
          WHEN 8 THEN 'AGOSTO'
          WHEN 9 THEN 'SETIEMBRE'
          WHEN 10 THEN 'OCTUBRE'
          WHEN 11 THEN 'NOVIEMBRE'
          WHEN 12 THEN 'DICIEMBRE'
          END
          ,p1.cuota) as cuota,
          p1.monto,p1.estado FROM (
          SELECT 'Fecha de Emision' AS fecha,'DNI' AS dni,'Matricula' as grado,'Boleta' AS boleta,'Alumno' AS alumno,'Cuota' AS cuota,'Monto' AS monto,
          'Estado' AS estado UNION ALL
          SELECT *
          FROM
          (
          SELECT
          d.deve_fecha AS fecha,d.deve_dni AS dni,d.deve_grado as grado,d.deve_boleta AS boleta,d.deve_alumno AS alumno,d.deve_cuota AS cuota,d.deve_monto AS monto,
          CASE d.deve_estado WHEN 1 THEN 'Devengado' END AS estados
          FROM
          tb_devengado d
          LEFT JOIN tb_serie s ON d.id_serie = s.id_serie
          WHERE year(d.deve_fecha)=$anio AND d.deve_fecha<='$fecha_fin' and d.deve_estado=1
          GROUP BY
          d.deve_boleta,d.deve_fecha,d.deve_dni,d.deve_alumno,d.deve_cuota,d.deve_monto,d.deve_estado
          ORDER BY d.deve_fecha,d.deve_boleta) as t1
          UNION ALL
          SELECT deve_fecha AS fecha,deve_dni AS dni,deve_grado as grado,deve_boleta AS boleta,deve_alumno AS alumno,deve_cuota AS cuota,deve_monto AS monto,
          CASE deve_estado WHEN 1 THEN 'Devengado' WHEN 2 THEN 'Devengado (P)' END AS estados
          FROM tb_devengado a
          INNER JOIN tb_pagos b ON a.id_serie=b.id_serie AND a.deve_num=b.pago_num WHERE  year(pago_fecha)=$anio
          AND year(a.deve_fecha)=$anio AND deve_estado<>0 AND pago_fecha>'$fecha_fin'
          ) as p1";
         */
        $query = "SELECT 'Fecha de Emision' AS fecha,'DNI' AS dni,'Matricula' as grado,'Serie' as serie,'Boleta' AS boleta,'Alumno' AS alumno,'Cuota' AS cuota,'Monto' AS monto,'Estado' AS estado 
	UNION ALL
	SELECT * FROM (
	SELECT t1.fecha,t1.dni,t1.grado,t1.serie,t1.boleta,t1.alumno,t1.cuota,t1.monto_total,IF(id_pago is null,'Devengado','Devengado(P)') as estado FROM (
	SELECT p1.*,p3.not_monto,p2.id_pago,(monto_deve-(IF(pago_monto is null,0,pago_monto)+IF(not_monto is null,0,not_monto))) as monto_total FROM (
	SELECT d.deve_fecha AS fecha,d.deve_dni AS dni,d.deve_grado as grado,d.deve_boleta AS boleta,d.deve_alumno AS alumno,
if(d.deve_cuota is null,
case MONTH(d.deve_fecha) WHEN 1 THEN 'ENERO'
WHEN 2 THEN 'FEBRERO'
WHEN 3 THEN 'MARZO'
WHEN 4 THEN 'ABRIL'
WHEN 5 THEN 'MAYO'
WHEN 6 THEN 'JUNIO'
WHEN 7 THEN 'JULIO'
WHEN 8 THEN 'AGOSTO'
WHEN 9 THEN 'SETIEMBRE'
WHEN 10 THEN 'OCTUBRE'
WHEN 11 THEN 'NOVIEMBRE'
WHEN 12 THEN 'DICIEMBRE'
END 
,d.deve_cuota
) as cuota,d.deve_monto AS monto_deve,CASE d.deve_estado WHEN 1 THEN 'Devengado' END AS estados,s.serie_desc as serie,d.id_serie,deve_num,not_id from tb_devengado d 
	INNEr JOIN tb_serie s ON d.id_serie = s.id_serie 
	where deve_fecha<='$fecha_fin' AND YEAR(deve_fecha)='$anio') AS p1
	LEFT JOIN (
	SELECT id_pago,pago_fecha,pago_boleta,id_serie,pago_num,pago_monto_cancelado as pago_monto FROM tb_pagos WHERE pago_fecha<='$fecha_fin' and pago_estado=1 ) AS p2 ON p1.id_serie=p2.id_serie AND p1.deve_num=p2.pago_num
	LEFT JOIN (
	SELECT not_fecha,not_documento,not_serie,not_numero,not_monto,not_id FROM tb_nota_credito WHERE not_fecha<='$fecha_fin' AND not_estado=1
	) as p3 ON p1.not_id=p3.not_id ) as t1 where t1.monto_total>0 ORDER BY MONTH(t1.fecha),t1.serie) as a1;";
        $data = DB::select($query);
        return collect($data);
    }

    public static function crea_tmp_boleta_colegio($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_boleta_colegio_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_boleta_colegio_" . $codigo . "( 
    col_id int(11) primary key not null AUTO_INCREMENT, 
    col_boleta varchar(15), 
    col_serie varchar(4),
    col_numero int(11),
    col_fecha date,
    col_descripcion longtext, 
    col_tipo varchar(25), 
    col_venta double(8,2), 
    col_banco varchar(20),
    INDEX(col_boleta),
    INDEX(col_fecha),
    INDEX(col_serie),
    INDEX(col_numero),
    INDEX(col_tipo)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_boleta_colegio($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_boleta_colegio_" . $codigo . "(col_boleta,col_serie,col_numero,col_fecha,col_descripcion,col_tipo,col_venta,col_banco) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_cobranza_canti($codigo) {

        $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,UPPER(col_banco) as banco,CONCAT(col_tipo,' ',UPPER(col_banco)) as tipo_final,SUM(col_venta) as monto,COUNT(*) AS cantidad,b.tip_orden as orden,tip_area as area,
            (SELECT CONCAT(MIN(col_numero),'/',MAX(col_numero)) AS numero FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha AND col_serie=a.col_serie and col_banco<>'' ORDER BY col_numero
                ) as numero
FROM tmp_cbb_boleta_colegio_$codigo a
INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc 
where col_banco<>''
GROUP BY col_fecha,col_serie,tip_orden,col_banco ORDER BY col_fecha,col_serie,col_boleta,col_banco;";
        $data = DB::select($query);
        return collect($data);
    }

    public static function lista_cobranza_canti_provision($codigo) {
        /* $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,b.tip_orden as orden,tip_area as area,
          (SELECT CONCAT(MIN(col_numero),'/',MAX(col_numero)) AS numero FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha
          AND col_serie=a.col_serie  ORDER BY col_numero
          ) as numero
          FROM tmp_cbb_boleta_colegio_$codigo a
          INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc
          GROUP BY col_fecha,col_serie,tip_orden ORDER BY col_fecha,col_serie,col_boleta;"; */

        /* $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,a.tip_orden as orden,tip_area as area,
          if(col_tipo<>'PENSION',
          (SELECT CONCAT(MIN(col_numero),'/',MAX(col_numero)) as nume FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha and col_serie=a.col_serie AND col_banco<>''
          ),CONCAT(MIN(col_numero),'/',MAX(col_numero))) AS numero,tip
          from
          (
          SELECT a.*,b.*,IF(col_banco='',2,1) as tip
          FROM tmp_cbb_boleta_colegio_$codigo a
          INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc ) as a
          GROUP BY col_fecha,col_serie,tip_orden,tip ORDER BY col_fecha,col_serie,col_boleta;"; */

        $query = "SELECT * FROM	( 
                    SELECT a.fecha,a.serie,a.tipo,a.monto,a.cantidad,a.orden,a.area,if(a.tip=1,IF(a.minimo=a.maximo,CONCAT(a.minimo),CONCAT(a.minimo,'/',a.maximo)),IF(a.minimo=a.maximo,CONCAT(a.minimo),CONCAT(a.minimo_v,'/',a.maximo_v))) as numero,a.tip from (
			SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,a.tip_orden as orden,tip_area as area,tip,
			(SELECT MIN(col_numero) as nume FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha and col_serie=a.col_serie AND trim(col_banco)<>'') as minimo,
			(SELECT MAX(col_numero) as  nume FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha and col_serie=a.col_serie AND trim(col_banco)<>'') as maximo,
			(SELECT MIN(col_numero) as nume FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha and col_serie=a.col_serie AND trim(col_banco)='') as minimo_v,
			(SELECT MAX(col_numero) as  nume FROM tmp_cbb_boleta_colegio_$codigo WHERE col_fecha=a.col_fecha and col_serie=a.col_serie AND trim(col_banco)='') as maximo_v
			from 
			(
			SELECT a.*,b.*,IF(col_banco='',2,1) as tip
					FROM tmp_cbb_boleta_colegio_$codigo a
					INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc AND col_serie NOT IN ('B013','B008','B029') ORDER BY col_boleta ) as a
			GROUP BY col_fecha,col_serie,tip_orden,tip ORDER BY col_fecha,col_serie,col_boleta) as a WHERE tip='1' ) as a1";
        $data = DB::select($query);
        return collect($data);
    }

    public static function lista_cobranza_detalle($codigo, $fecha, $serie, $tipo, $banco) {
        $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,b.tip_orden as orden,tip_area as area,UPPER(col_banco) as banco
FROM tmp_cbb_boleta_colegio_$codigo a
INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc 
WHERE  a.col_fecha='$fecha' AND a.col_serie='$serie' AND b.tip_orden='$tipo' and upper(a.col_banco)='$banco' 
AND a.col_serie NOT IN ('B013','B008','B029') 
GROUP BY col_fecha,col_serie,col_tipo ORDER BY col_fecha,col_serie,tip_orden;";

        $data = DB::select($query);
        return collect($data);
    }

    public static function lista_cobranza_detalle_provision($codigo, $fecha, $serie, $tipo, $tipoBanco) {
        /* $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,b.tip_orden as orden,tip_area as area
          FROM tmp_cbb_boleta_colegio_$codigo a
          INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc
          WHERE  a.col_fecha='$fecha' AND a.col_serie='$serie' AND b.tip_orden='$tipo'
          GROUP BY col_fecha,col_serie,col_tipo ORDER BY col_fecha,col_serie,tip_orden;"; */

        $query = "SELECT col_fecha as fecha,col_serie as serie,col_tipo as tipo,SUM(col_venta) as monto,COUNT(*) AS cantidad,a.tip_orden as orden,tip_area as area,tip FROM
(SELECT a.*,b.*,IF(col_banco='',2,1) as tip
FROM tmp_cbb_boleta_colegio_$codigo a
INNER JOIN tb_tipo_pago b ON a.col_tipo=b.tip_desc 
WHERE  a.col_fecha='$fecha' AND a.col_serie='$serie' AND b.tip_orden='$tipo' 
AND a.col_serie NOT IN ('B013','B008','B029') 
 ) as a WHERE a.tip=$tipoBanco	
GROUP BY col_fecha,col_serie,col_tipo,tip ORDER BY col_fecha,col_serie,tip_orden;";

        $data = DB::select($query);
        return collect($data);
    }

    public static function crea_tmp_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_serie varchar(15), 
    fi_fecha date,
    fi_tipo varchar(25),
    fi_total double(9,3),
    fi_orden char(3),
    fi_dh char(1), 
    fi_cuenta varchar(10), 
    fi_anexo varchar(10),
    fi_glosa varchar(75),
    fi_moneda char(2),
    fi_correla int,
    fi_subdiario char(3),
    fi_tipdoc char(2),
    fi_numdoc varchar(30),
    fi_fechaven varchar(10),
    fi_area char(3),
    fi_medpago char(3),
    INDEX(fi_serie),
    INDEX(fi_fecha),
    INDEX(fi_tipo),
    INDEX(fi_numdoc),
    INDEX(fi_subdiario)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_final_" . $codigo . "("
                . "fi_serie,fi_fecha,fi_tipo,fi_total,fi_orden,fi_dh,fi_cuenta,fi_anexo,fi_glosa,fi_moneda,fi_correla,fi_subdiario,fi_tipdoc,fi_numdoc,fi_fechaven,fi_area,fi_medpago) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar($codigo) {
        $db_ext = \DB::connection('mysql');
        //DB::select("SET NAMES utf8");		
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'
UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION
SELECT '' AS tamanio,fi_subdiario AS subdiario,CONCAT(LPAD(MONTH(fi_fecha),2,'0'),LPAD(fi_correla,4,'0')) as numeroCom ,
DATE_FORMAT(fi_fecha, '%d/%m/%Y') as fechaCompro,fi_moneda as moneda,fi_glosa AS glosa,'' AS tipoCambio,'V' AS tipoConversion,'S' AS flagConMoneda,
DATE_FORMAT(fi_fecha, '%d/%m/%Y') AS fechaTipCambio,fi_cuenta AS cuentaContable,fi_anexo AS codAnexo,'' AS centroCosto,fi_dh AS dh,fi_total AS importe,
'' AS importeDolar,fi_total AS importeSoles,fi_tipdoc AS tipdoc,fi_numdoc AS numDoc,DATE_FORMAT(fi_fecha, '%d/%m/%Y') AS fechaDoc,DATE_FORMAT(fi_fecha, '%d/%m/%Y') AS fechaVen,
fi_area AS codArea,fi_tipo AS glosaDetalle,'' AS anexoAux,fi_medpago AS medioPago,'' as tipoDocRef,'' AS numDocRef,'' AS fecDocRef,'' AS nroMaqReg,
'' AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_final_$codigo;";
        $data = DB::select(utf8_encode($query_select));
        return collect($data);
    }

    public static function genera_data_concar_ose($fecha_inicio, $fecha_fin, $codigo) {
        /* $query = "SELECT boleta,serie,numero,fecha,descripcion,
          CASE WHEN nroMes>mesActual THEN 'ANTICIPO PENSION' ELSE
          CASE WHEN LOCATE('MORA',descripcion)>0 THEN 'MORA' ELSE
          CASE WHEN LOCATE('MATRICULA',descripcion)>0 OR LOCATE('MATRICULA',descripcion)>0 THEN 'MATRICULA' ELSE
          CASE WHEN LOCATE('TALLER',descripcion)>0 OR LOCATE('TALLERES',descripcion)>0
          OR LOCATE('TALLER VERANO',descripcion)>0 THEN 'TALLER' ELSE
          CASE WHEN LOCATE('PENSION',descripcion)>0 OR LOCATE('PENSION',descripcion)>0 THEN 'PENSION' ELSE
          'TRAMITES VR' END END END END END as tipo,venta
          FROM (
          SELECT a1.*,CASE mes
          WHEN 'ENERO' THEN 1
          WHEN 'FEBRERO' THEN 2
          WHEN 'MARZO' THEN 3
          WHEN 'ABRIL' THEN 4
          WHEN 'MAYO' THEN 5
          WHEN 'JUNIO' THEN 6
          WHEN 'JULIO' THEN 7
          WHEN 'AGOSTO' THEN 8
          WHEN 'SEPTIEMBRE' THEN 9
          WHEN 'OCTUBRE' THEN 10
          WHEN 'NOVIEMBRE' THEN 11
          WHEN 'DICIEMBRE' THEN 12
          END AS nroMes
          FROM (
          SELECT p1.boleta,p1.fecha,p1.serie,p1.numero,p1.tipo,p1.detalle,p1.numero_afectado,p1.fecha_num_afectado,
          p1.dni,p1.persona,p1.direccion,p1.ope_gravadas,p1.ope_inafectas,p1.ope_exoneradas,p1.ope_gratuitas,p1.venta,p2.pa_concepto as descripcion,p1.cantidad,p1.valor,p1.ventas,MONTH(p1.fecha) as mesActual,
          UPPER(TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(REPLACE(TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(p2.pa_concepto,'PENSION',''),'PENSION',''),'COL',''),'CAR',''),'SJL',''),YEAR(p1.fecha),''),'SSEPTIEMBRE','SEPTIEMBRE'),'CCIA ESTUDIOS ',''),'CERT ESTUDIO','')),'certificado de estudios',''),'CERTIFICADO DE ESTUDIOS',''),'Ccia No Adeudo',''),'CONSTANCIA DE NO ADEUDO',''),'Postulacion','')),'S ',''),',',1),'CCIA NO ADEUDO',''),'constancia de no adeudo',''),'certificado de esudios',''),'CERT CONDUCTA',''),'EXCELENCIA ACAD',''),'SAGOSTO','AGOSTO'))) AS mes
          FROM (SELECT CONCAT(com_serie,'-',com_numero) as boleta,com_fecha_envio as fecha,com_serie as serie, com_numero as numero,
          case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRONICA' THEN '03' WHEN 'FACTURA DE VENTA ELECTRONICA' THEN '01'
          WHEN 'NOTA DE CREDITO ELECTRONICA' THEN '07' WHEN 'NOTA DE DEBITO ELECTRONICA' THEN '08' END AS tipo,
          case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRONICA' THEN 'Boleta' WHEN 'FACTURA DE VENTA ELECTRONICA' THEN 'Factura'
          WHEN 'NOTA DE CREDITO ELECTRONICA' THEN 'Credito' WHEN 'NOTA DE DEBITO ELECTRONICA' THEN 'Debito' END AS detalle,
          '' as numero_afectado,'' AS fecha_num_afectado, com_doc_iden AS dni,com_nombres AS persona,'' as direccion,
          IF(com_igv>0,com_total,0) AS ope_gravadas,IF(com_igv=0,com_total,0) AS ope_inafectas,0 AS ope_exoneradas,com_gratuito AS ope_gratuitas,com_total as venta,1 as cantidad,com_neto AS valor,com_total AS ventas
          FROM tb_comprobantes_ose a WHERE com_serie LIKE 'B%' AND com_estado=1) as p1
          INNER JOIN tmp_pagos_alexia_concar_$codigo p2 ON p1.boleta=CONCAT(p2.pa_serie,'-',p2.pa_numero)
          WHERE p1.detalle='Boleta') as a1) as b1 WHERE fecha>='$fecha_inicio' AND fecha<='$fecha_fin'"; */
        //aqui cambio el NOW() por fecha
        /* $query = "SELECT boleta,serie,numero,fecha,descripcion,
          CASE WHEN (LOCATE('MATRÍCULA',descripcion)>0 OR LOCATE('MATRICULA',descripcion)>0) AND anioActual>YEAR(fecha) THEN 'ANTICIPO MATRICULA' ELSE
          CASE WHEN (LOCATE('PENSIÓN',descripcion)>0 OR LOCATE('PENSION',descripcion)>0) and nroMes>mesActual THEN 'ANTICIPO PENSION' ELSE
          CASE WHEN LOCATE('MORA',descripcion)>0 THEN 'MORA' ELSE
          CASE WHEN (LOCATE('MATRÍCULA',descripcion)>0 OR LOCATE('MATRICULA',descripcion)>0) AND anioActual=YEAR(fecha) THEN 'MATRICULA' ELSE
          CASE WHEN LOCATE('TALLER',descripcion)>0 OR LOCATE('TALLERES',descripcion)>0
          OR LOCATE('TALLER VERANO',descripcion)>0 THEN 'TALLER' ELSE
          CASE WHEN LOCATE('PENSIÓN',descripcion)>0 OR LOCATE('PENSION',descripcion)>0 THEN 'PENSION' ELSE
          'TRAMITES VR' END END END END END END as tipo,venta,tipoDocu
          FROM (
          SELECT a1.*,CASE mes
          WHEN 'ENERO' THEN 1
          WHEN 'FEBRERO' THEN 2
          WHEN 'MARZO' THEN 3
          WHEN 'ABRIL' THEN 4
          WHEN 'MAYO' THEN 5
          WHEN 'JUNIO' THEN 6
          WHEN 'JULIO' THEN 7
          WHEN 'AGOSTO' THEN 8
          WHEN 'SEPTIEMBRE' THEN 9
          WHEN 'OCTUBRE' THEN 10
          WHEN 'NOVIEMBRE' THEN 11
          WHEN 'DICIEMBRE' THEN 12
          END AS nroMes
          FROM (
          SELECT boleta,fecha,serie,numero,tipo,detalle,numero_afectado,fecha_num_afectado,dni,persona,direccion,ope_gravadas,
          ope_inafectas,ope_exoneradas,ope_gratuitas,venta,descripcion,cantidad,valor,ventas,mesActual,anioActual,
          CASE WHEN LOCATE('enero',mes)>0 THEN 'ENERO' WHEN LOCATE('febrero',mes)>0 THEN 'FEBRERO' WHEN LOCATE('marzo',mes)>0 THEN 'MARZO'
          WHEN LOCATE('abril',mes)>0 THEN 'ABRIL' WHEN LOCATE('mayo',mes)>0 THEN 'MAYO' WHEN LOCATE('junio',mes)>0 THEN 'JUNIO' WHEN LOCATE('julio',mes)>0 THEN 'JULIO'
          WHEN LOCATE('agosto',mes)>0 THEN 'AGOSTO' WHEN LOCATE('septiembre',mes)>0 THEN 'SEPTIEMBRE' WHEN LOCATE('octubre',mes)>0 THEN 'OCTUBRE'
          WHEN LOCATE('noviembre',mes)>0 THEN 'NOVIEMBRE' WHEN LOCATE('diciembre',mes)>0 THEN 'DICIEMBRE' END mes,tipoDocu FROM (
          SELECT p1.boleta,p1.fecha,p1.serie,p1.numero,p1.tipo,p1.detalle,p1.numero_afectado,p1.fecha_num_afectado,
          p1.dni,p1.persona,p1.direccion,p1.ope_gravadas,p1.ope_inafectas,p1.ope_exoneradas,p1.ope_gratuitas,p1.venta,p2.pa_concepto as descripcion,p1.cantidad,p1.valor,p1.ventas,MONTH(p1.fecha) as mesActual,
          CASE WHEN LOCATE(YEAR(p1.fecha),p2.pa_concepto)>0 THEN YEAR(p1.fecha) ELSE
          CASE WHEN LOCATE(YEAR(DATE_ADD(p1.fecha, INTERVAL 1 YEAR)),p2.pa_concepto)>0 THEN YEAR(DATE_ADD(p1.fecha, INTERVAL 1 YEAR))
          END END AS anioActual,
          REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(p2.pa_concepto),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u') AS mes,tipoDocu
          FROM (SELECT CONCAT(com_serie,'-',com_numero) as boleta,com_fecha_envio as fecha,com_serie as serie, com_numero as numero,
          case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN '03' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN '01'
          WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN '07' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN '08' END AS tipo,
          case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 'Boleta' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN 'Factura'
          WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 'Credito' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN 'Debito' END AS detalle,
          case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 'BOL' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN 'FAC'
          WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 'NC' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN 'ND' END AS tipoDocu,
          '' as numero_afectado,'' AS fecha_num_afectado, com_doc_iden AS dni,com_nombres AS persona,'' as direccion,
          IF(com_igv>0,com_total,0) AS ope_gravadas,IF(com_igv=0,com_total,0) AS ope_inafectas,0 AS ope_exoneradas,com_gratuito AS ope_gratuitas,com_total as venta,1 as cantidad,com_neto AS valor,com_total AS ventas
          FROM tb_comprobantes_ose a WHERE com_serie LIKE 'B%' AND com_estado=1) as p1
          INNER JOIN tmp_pagos_alexia_concar_$codigo p2 ON p1.boleta=CONCAT(p2.pa_serie,'-',p2.pa_numero) AND p1.tipoDocu=p2.pa_tipo
          WHERE p1.detalle='Boleta') as g1 ) as a1) as b1 WHERE fecha>='$fecha_inicio' AND fecha<='$fecha_fin' GROUP BY 1;"; */
        $query = "SELECT boleta,serie,numero,fecha,descripcion,
        CASE WHEN (LOCATE('MATRÍCULA',descripcion)>0 OR LOCATE('MATRICULA',descripcion)>0) AND anioActual>YEAR(fecha) THEN 'ANTICIPO MATRICULA' ELSE
        CASE WHEN (LOCATE('PENSIÓN',descripcion)>0 OR LOCATE('PENSION',descripcion)>0) and nroMes>mesActual THEN 'ANTICIPO PENSION' ELSE 
        CASE WHEN LOCATE('MORA',descripcion)>0 THEN 'MORA' ELSE 
        CASE WHEN (LOCATE('MATRÍCULA',descripcion)>0 OR LOCATE('MATRICULA',descripcion)>0) AND anioActual=YEAR(fecha) THEN 'MATRICULA' ELSE 
        CASE WHEN LOCATE('TALLER',descripcion)>0 OR LOCATE('TALLERES',descripcion)>0 
        OR LOCATE('TALLER VERANO',descripcion)>0 THEN 'TALLER' ELSE 
        CASE WHEN LOCATE('PENSIÓN',descripcion)>0 OR LOCATE('PENSION',descripcion)>0 THEN 'PENSION' ELSE 
        'TRAMITES VR' END END END END END END as tipo,venta,tipoDocu
        FROM (		
        SELECT a1.*,CASE mes 
        WHEN 'ENERO' THEN 1
        WHEN 'FEBRERO' THEN 2
        WHEN 'MARZO' THEN 3
        WHEN 'ABRIL' THEN 4
        WHEN 'MAYO' THEN 5
        WHEN 'JUNIO' THEN 6
        WHEN 'JULIO' THEN 7
        WHEN 'AGOSTO' THEN 8
        WHEN 'SEPTIEMBRE' THEN 9
        WHEN 'OCTUBRE' THEN 10
        WHEN 'NOVIEMBRE' THEN 11
        WHEN 'DICIEMBRE' THEN 12
        END AS nroMes
        FROM (
    SELECT boleta,fecha,serie,numero,tipo,detalle,numero_afectado,fecha_num_afectado,dni,persona,direccion,ope_gravadas,
    ope_inafectas,ope_exoneradas,ope_gratuitas,venta,descripcion,cantidad,valor,ventas,mesActual,anioActual,
    CASE WHEN LOCATE('enero',mes)>0 THEN 'ENERO' WHEN LOCATE('febrero',mes)>0 THEN 'FEBRERO' WHEN LOCATE('marzo',mes)>0 THEN 'MARZO'
    WHEN LOCATE('abril',mes)>0 THEN 'ABRIL' WHEN LOCATE('mayo',mes)>0 THEN 'MAYO' WHEN LOCATE('junio',mes)>0 THEN 'JUNIO' WHEN LOCATE('julio',mes)>0 THEN 'JULIO'	
    WHEN LOCATE('agosto',mes)>0 THEN 'AGOSTO' WHEN LOCATE('septiembre',mes)>0 THEN 'SEPTIEMBRE' WHEN LOCATE('octubre',mes)>0 THEN 'OCTUBRE' 
    WHEN LOCATE('noviembre',mes)>0 THEN 'NOVIEMBRE' WHEN LOCATE('diciembre',mes)>0 THEN 'DICIEMBRE' END mes,tipoDocu FROM (
        SELECT p1.boleta,p1.fecha,p1.serie,p1.numero,p1.tipo,p1.detalle,p1.numero_afectado,p1.fecha_num_afectado,
        p1.dni,p1.persona,p1.direccion,p1.ope_gravadas,p1.ope_inafectas,p1.ope_exoneradas,p1.ope_gratuitas,p1.venta,p2.pa_concepto as descripcion,p1.cantidad,p1.valor,p1.ventas,MONTH(p1.fecha) as mesActual,
        -- CASE WHEN LOCATE(YEAR(p1.fecha),p2.pa_concepto)>0 THEN YEAR(p1.fecha) ELSE
	-- CASE WHEN LOCATE(YEAR(DATE_ADD(p1.fecha, INTERVAL 1 YEAR)),p2.pa_concepto)>0 THEN YEAR(DATE_ADD(p1.fecha, INTERVAL 1 YEAR)) 
	-- END END AS anioActual,
	YEAR(p1.fecha) AS anioActual,
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(p2.pa_concepto),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u') AS mes,tipoDocu
        FROM (SELECT CONCAT(com_serie,'-',com_numero) as boleta,com_fecha_envio as fecha,com_serie as serie, com_numero as numero,
        case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN '03' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN '01'
        WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN '07' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN '08' END AS tipo,
        case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 'Boleta' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN 'Factura'
        WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 'Credito' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN 'Debito' END AS detalle,
		case com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRÓNICA' THEN 'BOL' WHEN 'FACTURA DE VENTA ELECTRÓNICA' THEN 'FAC'
        WHEN 'NOTA DE CRÉDITO ELECTRÓNICA' THEN 'NC' WHEN 'NOTA DE DEBITO ELECTRÓNICA' THEN 'ND' END AS tipoDocu,
        '' as numero_afectado,'' AS fecha_num_afectado, com_doc_iden AS dni,com_nombres AS persona,'' as direccion,
        IF(com_igv>0,com_total,0) AS ope_gravadas,IF(com_igv=0,com_total,0) AS ope_inafectas,0 AS ope_exoneradas,com_gratuito AS ope_gratuitas,com_total as venta,1 as cantidad,com_neto AS valor,com_total AS ventas
        FROM tb_comprobantes_ose a WHERE com_serie LIKE 'B%' AND com_estado=1) as p1 
        INNER JOIN tmp_pagos_alexia_concar_$codigo p2 ON p1.boleta=CONCAT(p2.pa_serie,'-',p2.pa_numero) AND p1.tipoDocu=p2.pa_tipo
        WHERE p1.detalle='Boleta') as g1 ) as a1 				
        ) as b1 WHERE fecha>='$fecha_inicio' AND fecha<='$fecha_fin' GROUP BY 1 ";
        $data = DB::select(utf8_encode($query));
//$data = DB::select($query);
        return $data;
    }

    public static function declaracion_sunat_osse($fecha_ini, $fecha_fin, $serie) {
        $query = "SELECT com_fecha_envio,com_serie,min(com_numero) as inicio,max(com_numero) as fin,count(1) cantidad,
        fnc_obtener_numeros_faltantes(com_tipo_documento,com_serie,concat(com_fecha_envio,'')) as faltantes,
        IF(com_igv>0,sum(com_neto),'0') AS gravadas,
        IF(com_igv=0,sum(com_neto),'0') AS inafectas,
        IF(com_recargo>0,sum(com_recargo),'0') AS exoneradas,
        IF(com_gratuito>0,sum(com_gratuito),'0') AS gratuitas,
        IF(sum(com_neto)>0,sum(com_neto),'0') AS valorVenta,
        IF(sum(com_igv)>0,sum(com_igv),'0') AS igv,
        IF(sum(com_total)>0,sum(com_total),'0') AS total,
        CASE com_tipo_documento WHEN 'BOLETA DE VENTA ELECTRONICA' THEN 'Boleta'
        WHEN 'FACTURA ELECTRONICA' THEN 'Factura'
        WHEN 'NOTA DE DEBITO ELECTRONICA' THEN 'Nota de debito'
        WHEN 'NOTA DE CREDITO ELECTRONICA' THEN 'Nota de credito' END AS tipo,
        0 AS isc,
        0 AS otros
        FROM tb_comprobantes_ose WHERE 
        com_fecha_envio >= STR_TO_DATE('$fecha_ini', '%Y-%m-%d') COLLATE latin1_swedish_ci and 
        com_fecha_envio <= STR_TO_DATE('$fecha_fin', '%Y-%m-%d') COLLATE latin1_swedish_ci ";
        if ($serie != "0") {
            $query .= " AND com_serie='$serie' ";
        }
        $query .= " GROUP BY com_fecha_envio,com_serie,com_tipo_documento
        ORDER BY com_fecha_envio,com_serie,com_tipo_documento;";
        $data = DB::select($query);
        return collect($data);
    }

//DEVENGADO PENSION
    public static function lista_devengados_pensiones_cantidades($codigo, $fecha_inicio, $fecha_fin) {
        $query = "SELECT b.fecha,b.serie,b.tipo,b.monto,b.cantidad,b.orden,b.area,CONCAT(b.minimo,'/',b.maximo) as numero,b.tip FROM (
			SELECT deve_fecha as fecha,serie_desc as serie,'DEVENGADO PENSION' as tipo,SUM(deve_monto) as monto,COUNT(*) AS cantidad,'' as orden,'' as area,'2' as tip,
			(SELECT MIN(deve_num) as nume FROM tb_devengado WHERE deve_fecha=a.deve_fecha and id_serie=a.id_serie AND deve_estado='1') as minimo,
			(SELECT MAX(deve_num) as  nume FROM tb_devengado WHERE deve_fecha=a.deve_fecha and id_serie=a.id_serie AND deve_estado='1') as maximo
			FROM tb_devengado a 
			INNER JOIN tb_serie b ON a.id_serie=b.id_serie
			WHERE deve_fecha>='$fecha_inicio' AND deve_fecha<='$fecha_fin' AND deve_estado='1' AND deve_boleta NOT LIKE 'F%'
			GROUP BY deve_fecha,a.id_serie ORDER BY deve_fecha,a.id_serie,deve_boleta) as b";
        $data = DB::select($query);
        return collect($data);
    }

//DEVENGADO PENSION

    public static function tmp_pagos_alexia_concar_devengados($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_devengados_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_devengados_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia_concar_devengados($tabla, $data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table($tabla)->insert($data);
    }

    public static function genera_data_concar_devengados($fecha_inicio, $fecha_fin, $codigo) {
        $db_ext = \DB::connection('mysql');
        $query = "-- devengados de documentos D
SELECT subDiario,DATE_FORMAT(fechaComprobante, '%d/%m/%Y') as fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,DATE_FORMAT(fechaTipoCambio, '%d/%m/%Y') as fechaTipoCambio,cuentaContable,anexo,centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,
tipoDocumento,CONVERT(numeroDocumento USING utf8) as numeroDocumento,DATE_FORMAT(fechaDocumento, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(fechaVencimiento, '%d/%m/%Y') as fechaVencimiento,codigoArea,glosaDetalle,anexoAuxiliar,medioPago

FROM (
SELECT * FROM (
SELECT subDiario,fechaComprobante,tipoMoneda,CASE tipo WHEN 1 then CONCAT(detallePrincipal,'') WHEN 2 THEN CONCAT(detallePrincipal,'') WHEN 3 then CONCAT(detallePrincipal,' ',YEAR(fechaComprobante)) END AS glosaPrincipal,'' as tipoCambio,tipoConversion,moneda,FechaTipoCambio,cuentaContable,cuentaContable as anexo,'' as centroCosto,'D' as debeHaber,monto as importeOriginal,'' as importeDolares,monto as importeSoles,'EN' as tipoDocumento,CONCAT(SUBSTRING(fechaComprobante,9,2),SUBSTRING(fechaComprobante,6,2),SUBSTRING(fechaComprobante,1,4),SUBSTRING(fechaComprobante,6,2)) as numeroDocumento,fechaComprobante as fechaDocumento,fechaComprobante as fechaVencimiento,codigoArea,glosaDetalle,'' as anexoAuxiliar,'001' as medioPago

FROM ( 
SELECT subDiario,pa_fecha_pago as fechaComprobante,'MN' as tipoMoneda,tipo,CASE tipo WHEN 1 THEN CONCAT('COBRANZA DEVENGADO ',YEAR(pa_fecha_emi)) ELSE
CASE MONTH(pa_fecha_emi)
	WHEN 1 THEN 'COBRANZA DEVENGADO ENERO'
  WHEN 2 THEN 'COBRANZA DEVENGADO FEBRERO'
	WHEN 3 THEN 'COBRANZA DEVENGADO MARZO'
	WHEN 4 THEN 'COBRANZA DEVENGADO ABRIL'
	WHEN 5 THEN 'COBRANZA DEVENGADO MAYO'
	WHEN 6 THEN 'COBRANZA DEVENGADO JUNIO'
	WHEN 7 THEN 'COBRANZA DEVENGADO JULIO'
	WHEN 8 THEN 'COBRANZA DEVENGADO AGOSTO'
	WHEN 9 THEN 'COBRANZA DEVENGADO SETIEMBRE'
	WHEN 10 THEN 'COBRANZA DEVENGADO OCTUBRE'
	WHEN 11 THEN 'COBRANZA DEVENGADO NOVIEMBRE'
	WHEN 12 THEN 'COBRANZA DEVENGADO DICIEMBRE'
END END AS detallePrincipal,'V' as tipoConversion,'S' as moneda,pa_fecha_pago as FechaTipoCambio,pa_serie as serie,pa_banco as banco,
MIN(pa_numero) as minimo,MAX(pa_numero) as maximo,SUM(pa_cancelado) as monto,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 4 YEAR)) THEN '759901'
ELSE
	CASE pa_banco
		WHEN 'Scotiabank' THEN '106117'
		WHEN 'BBVA' THEN '106115'
		WHEN 'BCP' THEN '104109'
		WHEN 'INTERBANK' THEN '104110'
	END
END	
AS cuentaContable,detalle as glosaDetalle,
IF(YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)),CASE sede 
		WHEN 'COLONIAL' THEN 'L05'
		WHEN 'CARABAYLLO' THEN 'C05'
		WHEN 'SAN JUAN' THEN 'S05' END,
	CASE tipoIngresos
		WHEN 'MATRICULA' THEN
			CASE sede
				WHEN 'COLONIAL' THEN 'L01'
				WHEN 'CARABAYLLO' THEN 'C01'
				WHEN 'SAN JUAN' THEN 'S01' END
		WHEN 'PENSION' THEN
			CASE sede
				WHEN 'COLONIAL' THEN 'L02'
				WHEN 'CARABAYLLO' THEN 'C02'
				WHEN 'SAN JUAN' THEN 'S02' END	
		WHEN 'MORA' THEN
			CASE sede
				WHEN 'COLONIAL' THEN 'L03'
				WHEN 'CARABAYLLO' THEN 'C03'
				WHEN 'SAN JUAN' THEN 'S03' END
		WHEN 'TRAMITES VR' THEN
			CASE sede
				WHEN 'COLONIAL' THEN 'L04'
				WHEN 'CARABAYLLO' THEN 'C04'
				WHEN 'SAN JUAN' THEN 'S04' END	
	END) as codigoArea
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede,
CONCAT('CLIENTES VR ',YEAR(pa_fecha_emi)) as detalle, CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'MORA' ELSE 
'TRAMITES VR' END END END as tipoIngresos
FROM tmp_pagos_alexia_concar_devengados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('banco','factura')) as b ON a.pa_serie=b.serie_desc
 WHERE pa_estado=1 and pa_banco<>'' AND pa_fecha_emi<'$fecha_inicio' )  as a WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' 
 GROUP BY tipo,pa_fecha_pago,detallePrincipal,sede,pa_banco,tipoIngresos ORDER BY pa_fecha_pago,sede
) as b ) as c ORDER BY fechaComprobante,glosaPrincipal,cuentaContable) as p1

UNION 

-- devengados de documentos H
SELECT subDiario,DATE_FORMAT(fechaComprobante, '%d/%m/%Y') as fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,DATE_FORMAT(fechaTipoCambio, '%d/%m/%Y') as fechaTipoCambio,cuentaContable,anexo,centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,
tipoDocumento,CONVERT(numeroDocumento USING utf8) as numeroDocumento,DATE_FORMAT(fechaDocumento, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(fechaVencimiento, '%d/%m/%Y') as fechaVencimiento,codigoArea,glosaDetalle,anexoAuxiliar,medioPago

FROM (
SELECT * FROM (
SELECT subDiario,fechaComprobante,tipoMoneda,CASE tipo WHEN 1 then CONCAT(detallePrincipal,'') WHEN 2 THEN CONCAT(detallePrincipal,'') WHEN 3 then CONCAT(detallePrincipal,' ',YEAR(fechaComprobante)) END AS glosaPrincipal,'' as tipoCambio,tipoConversion,moneda,FechaTipoCambio,cuentaContable,codigoAnexo as anexo,'' as centroCosto,'H' as debeHaber,monto as importeOriginal,'' as importeDolares,monto as importeSoles,
'BV' tipoDocumento,CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN CONCAT(serie,'-',YEAR(pa_fecha_emi)) ELSE IF(minimo=maximo,CONCAT(serie,'-',minimo),CONCAT(serie,'-',minimo,'/',maximo)) END AS numeroDocumento,
fechaComprobante as fechaDocumento,fechaComprobante as fechaVencimiento,'' as codigoArea,glosaDetalle,'' as anexoAuxiliar,'001' as medioPago
FROM ( 
SELECT subDiario,pa_fecha_pago as fechaComprobante,'MN' as tipoMoneda,tipo,CASE tipo WHEN 1 THEN CONCAT('COBRANZA DEVENGADO ',YEAR(pa_fecha_emi)) ELSE
CASE MONTH(pa_fecha_emi)
	WHEN 1 THEN 'COBRANZA DEVENGADO ENERO'
  WHEN 2 THEN 'COBRANZA DEVENGADO FEBRERO'
	WHEN 3 THEN 'COBRANZA DEVENGADO MARZO'
	WHEN 4 THEN 'COBRANZA DEVENGADO ABRIL'
	WHEN 5 THEN 'COBRANZA DEVENGADO MAYO'
	WHEN 6 THEN 'COBRANZA DEVENGADO JUNIO'
	WHEN 7 THEN 'COBRANZA DEVENGADO JULIO'
	WHEN 8 THEN 'COBRANZA DEVENGADO AGOSTO'
	WHEN 9 THEN 'COBRANZA DEVENGADO SETIEMBRE'
	WHEN 10 THEN 'COBRANZA DEVENGADO OCTUBRE'
	WHEN 11 THEN 'COBRANZA DEVENGADO NOVIEMBRE'
	WHEN 12 THEN 'COBRANZA DEVENGADO DICIEMBRE'
END END AS detallePrincipal,'V' as tipoConversion,'S' as moneda,pa_fecha_pago as FechaTipoCambio,pa_serie as serie,pa_banco as banco,
(SELECT MIN(pa_numero) as minimo FROM tmp_pagos_alexia_concar_devengados_$codigo WHERE YEAR(pa_fecha_emi)=YEAR(a.pa_fecha_emi) AND MONTH(pa_fecha_emi)=MONTH(a.pa_fecha_emi) AND pa_serie=a.pa_serie 
GROUP BY YEAR(pa_fecha_emi),MONTH(pa_fecha_emi)) as minimo,
(SELECT MAX(pa_numero) as minimo FROM tmp_pagos_alexia_concar_devengados_$codigo WHERE YEAR(pa_fecha_emi)=YEAR(a.pa_fecha_emi) AND MONTH(pa_fecha_emi)=MONTH(a.pa_fecha_emi) AND pa_serie=a.pa_serie 
GROUP BY YEAR(pa_fecha_emi),MONTH(pa_fecha_emi)) as maximo,
SUM(pa_cancelado) as monto,
CASE sede
	WHEN 'COLONIAL' THEN '12132'
	WHEN 'CARABAYLLO' THEN '12133'
	WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,
CASE WHEN YEAR(pa_fecha_emi)=YEAR(DATE_SUB(NOW(),INTERVAL 4 YEAR)) THEN 
CASE sede
	WHEN 'COLONIAL' THEN 'L01'
	WHEN 'CARABAYLLO' THEN 'C01'
	WHEN 'SAN JUAN' THEN 'S01'
END 
WHEN YEAR(pa_fecha_emi)=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) OR YEAR(pa_fecha_emi)=YEAR(DATE_SUB(NOW(),INTERVAL 3 YEAR)) THEN CONCAT('DEVENGADOS',YEAR(pa_fecha_emi)) ELSE CONCAT('0000') END AS codigoAnexo,
detalle as glosaDetalle,pa_fecha_emi
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede,
CONCAT('CLIENTES VR ',YEAR(pa_fecha_emi)) as detalle, 
CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'MORA' ELSE 
'TRAMITES VR' END END END as tipoIngresos
FROM tmp_pagos_alexia_concar_devengados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('banco','factura')) as b ON a.pa_serie=b.serie_desc
 WHERE pa_estado=1 and pa_banco<>'' AND pa_fecha_emi<'$fecha_inicio') as a WHERE pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' GROUP BY tipo,pa_fecha_pago,detallePrincipal,sede ORDER BY pa_fecha_pago,sede
 
) as b ) as c 
-- WHERE glosaPrincipal='COBRANZA DEVENGADO AGOSTO'	
ORDER BY fechaComprobante,glosaPrincipal,cuentaContable ) as p2 ORDER BY fechaComprobante,glosaPrincipal,subDiario,debeHaber";

        $data = $db_ext->select($query);
        return $data;
    }

    public static function crea_tmp_devengados_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_devengados_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_devengados_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_subDiario char(3), 
    fi_numeroComprobante varchar(10),
    fi_fechaComprobante varchar(10),
    fi_tipoMoneda char(2),
    fi_glosaPrincipal varchar(75),
    fi_tipoCambio char(5),
    fi_tipoConversion char(2),
    fi_moneda char(2),
    fi_fechaTipoCambio varchar(10),
    fi_cuentaContable varchar(6),
    fi_anexo varchar(20),
    fi_centroCosto char(3),
    fi_debeHaber char(1),
    fi_importeOriginal double(9,3),
    fi_importeDolares double(9,3),
    fi_importeSoles double(9,3),
    fi_tipoDocumento char(2),
    fi_numeroDocumento char(25),
    fi_fechaDocumento varchar(10),
    fi_fechaVencimiento varchar(10),
    fi_codigoArea char(4),
    fi_glosaDetalle varchar(30),
    fi_anexoAuxiliar char(3),
    fi_medioPago char(3),
    INDEX(fi_subDiario),
    INDEX(fi_fechaComprobante),
    INDEX(fi_glosaPrincipal),
    INDEX(fi_numeroDocumento)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_devengados_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_devengados_final_" . $codigo . "("
                . "fi_subDiario,fi_numeroComprobante,fi_fechaComprobante,fi_tipoMoneda,fi_glosaPrincipal,fi_tipoCambio,fi_tipoConversion,fi_moneda,fi_fechaTipoCambio,fi_cuentaContable,fi_anexo,fi_centroCosto,fi_debeHaber,fi_importeOriginal,
    fi_importeDolares,fi_importeSoles,fi_tipoDocumento,fi_numeroDocumento,fi_fechaDocumento,fi_fechaVencimiento,fi_codigoArea,fi_glosaDetalle,fi_anexoAuxiliar,fi_medioPago) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar_devengados($codigo) {
        $db_ext = \DB::connection('mysql');
        //DB::statement("SET NAMES utf8");	
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'
UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION

SELECT '' AS tamanio,fi_subDiario AS subdiario,CONCAT(LPAD(SUBSTRING(fi_fechaComprobante,4,2),2,'0'),LPAD(fi_numeroComprobante,4,'0')) as numeroCom ,
fi_fechaComprobante as fechaCompro,fi_tipoMoneda as moneda,fi_glosaPrincipal AS glosa,fi_tipoCambio AS tipoCambio,fi_tipoConversion AS tipoConversion,fi_moneda AS flagConMoneda,
fi_fechaTipoCambio AS fechaTipCambio,fi_cuentaContable AS cuentaContable,fi_anexo AS codAnexo,fi_centroCosto AS centroCosto,fi_debeHaber AS dh,fi_importeOriginal AS importe,
fi_importeDolares AS importeDolar,fi_importeSoles AS importeSoles,fi_tipoDocumento AS tipdoc,fi_numeroDocumento AS numDoc,fi_fechaDocumento AS fechaDoc,fi_fechaVencimiento AS fechaVen,
fi_codigoArea AS codArea,fi_glosaDetalle AS glosaDetalle,fi_anexoAuxiliar AS anexoAux,fi_medioPago AS medioPago,'' as tipoDocRef,'' AS numDocRef,'' AS fecDocRef,'' AS nroMaqReg,
'' AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_devengados_final_$codigo;";
        $data = DB::select(utf8_encode($query_select));
        //$data = DB::select($query_select);
        return collect($data);
    }

    public static function tmp_pagos_alexia_concar_notas_credito($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_notascredito_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_notascredito_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_observaciones varchar(70),
    pa_navisos_cobro varchar(15),
    pa_doc_referencia varchar(15),
    pa_fecha_emision_referencia DATE,
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia_concar_notas_credito($tabla, $data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table($tabla)->insert($data);
    }

    public static function genera_data_concar_notas_credito($fecha_inicio, $fecha_fin, $codigo) {
        $db_ext = \DB::connection('mysql');
        $query = "-- devengados de documentos D
SELECT * FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,pa_fecha_pago as fechaTipoCambio,
CASE WHEN LOCATE('Devolución',LOWER(pa_observaciones))>0 OR LOCATE('Devolucion',LOWER(pa_observaciones))>0  THEN '7032115' 
WHEN LOCATE('Anulación',LOWER(pa_observaciones))>0 OR LOCATE('Anulacion',LOWER(pa_observaciones))>0 OR LOCATE('Descuento',LOWER(pa_observaciones))>0 THEN '74102' END AS cuentaContable,
CASE sede
    WHEN 'COLONIAL' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'L01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'L02' ELSE
	'L04' END END 
    WHEN 'CARABAYLLO' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'C01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'C02' ELSE
	'C04' END END 
    WHEN 'SAN JUAN' THEN 
        CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'S01' ELSE 
        CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'S02' ELSE
	'S04' END END 
END AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_total*-1) as importeOriginal,'' as importeDolares,(pa_total*-1) as importeSoles,'NA' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,'' as codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede,
CONCAT('CLIENTES VR ',YEAR(pa_fecha_emi)) as detalle, CASE serie_sede WHEN 'COLONIAL' THEN '7C' WHEN 'CARABAYLLO' THEN '8C' WHEN 'SAN JUAN' THEN '11C' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notascredito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p1 
UNION 

-- devengados de documentos H
SELECT * FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,pa_fecha_pago as fechaTipoCambio,
CASE sede
	WHEN 'COLONIAL' THEN '12132'
	WHEN 'CARABAYLLO' THEN '12133'
	WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,pa_ruc_dni as codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_total*-1) as importeOriginal,'' as importeDolares,(pa_total*-1) as importeSoles,'NA' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,'' as codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,CASE SUBSTRING(pa_serie,1,1) WHEN 'B' THEN 'BV' WHEN 'F' THEN 'FT' ELSE '' END as tipoDocReferencia,REPLACE(doc_referencia,'/','-') as numeroReferencia,DATE_FORMAT(fecha_emision_referencia, '%d/%m/%Y')  as fechaReferencia,'' as nroMaqRegis, (pa_total*-1) as baseImponible
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_ruc_dni,pa_concepto,pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede,
CONCAT('CLIENTES VR ',YEAR(pa_fecha_emi)) as detalle, CASE serie_sede WHEN 'COLONIAL' THEN '7C' WHEN 'CARABAYLLO' THEN '8C' WHEN 'SAN JUAN' THEN '11C' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notascredito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede
) as p2 ORDER BY fechaComprobante,subDiario,numeroDocumento,debeHaber;";
        $data = $db_ext->select($query);
        return $data;
    }

    public static function crea_tmp_notas_credito_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_notas_credito_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_notas_credito_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_subDiario char(3), 
    fi_numeroComprobante varchar(10),
    fi_fechaComprobante varchar(10),
    fi_tipoMoneda char(2),
    fi_glosaPrincipal varchar(75),
    fi_tipoCambio char(5),
    fi_tipoConversion char(2),
    fi_moneda char(2),
    fi_fechaTipoCambio varchar(10),
    fi_cuentaContable varchar(10),
    fi_anexo varchar(20),
    fi_centroCosto char(3),
    fi_debeHaber char(1),
    fi_importeOriginal double(9,3),
    fi_importeDolares double(9,3),
    fi_importeSoles double(9,3),
    fi_tipoDocumento char(2),
    fi_numeroDocumento char(25),
    fi_fechaDocumento varchar(10),
    fi_fechaVencimiento varchar(10),
    fi_codigoArea char(4),
    fi_glosaDetalle varchar(30),
    fi_anexoAuxiliar char(3),
    fi_medioPago char(3),
    fi_tipoDocReferencia varchar(3),
    fi_numeroReferencia varchar(15),
    fi_fechaReferencia varchar(10),
    fi_nroMaqRegis char(4),
    fi_baseImpobible double(9,3),
    INDEX(fi_subDiario),
    INDEX(fi_fechaComprobante),
    INDEX(fi_glosaPrincipal),
    INDEX(fi_numeroDocumento)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_notas_credito_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_notas_credito_final_" . $codigo . "("
                . "fi_subDiario,fi_numeroComprobante,fi_fechaComprobante,fi_tipoMoneda,fi_glosaPrincipal,fi_tipoCambio,fi_tipoConversion,fi_moneda,fi_fechaTipoCambio,fi_cuentaContable,fi_anexo,fi_centroCosto,fi_debeHaber,fi_importeOriginal,
    fi_importeDolares,fi_importeSoles,fi_tipoDocumento,fi_numeroDocumento,fi_fechaDocumento,fi_fechaVencimiento,fi_codigoArea,fi_glosaDetalle,fi_anexoAuxiliar,fi_medioPago,fi_tipoDocReferencia,fi_numeroReferencia,
    fi_fechaReferencia,fi_nroMaqRegis,fi_baseImpobible) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar_notas_credito($codigo) {
        $db_ext = \DB::connection('mysql');
//DB::statement("SET NAMES utf8");
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'
UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION

SELECT '' AS tamanio,fi_subDiario AS subdiario,CONCAT(LPAD(SUBSTRING(fi_fechaComprobante,4,2),2,'0'),LPAD(fi_numeroComprobante,4,'0')) as numeroCom ,
fi_fechaComprobante as fechaCompro,fi_tipoMoneda as moneda,fi_glosaPrincipal AS glosa,fi_tipoCambio AS tipoCambio,fi_tipoConversion AS tipoConversion,fi_moneda AS flagConMoneda,
fi_fechaTipoCambio AS fechaTipCambio,fi_cuentaContable AS cuentaContable,fi_anexo AS codAnexo,fi_centroCosto AS centroCosto,fi_debeHaber AS dh,fi_importeOriginal AS importe,
fi_importeDolares AS importeDolar,fi_importeSoles AS importeSoles,fi_tipoDocumento AS tipdoc,fi_numeroDocumento AS numDoc,fi_fechaDocumento AS fechaDoc,fi_fechaVencimiento AS fechaVen,
fi_codigoArea AS codArea,fi_glosaDetalle AS glosaDetalle,fi_anexoAuxiliar AS anexoAux,fi_medioPago AS medioPago,fi_tipoDocReferencia as tipoDocRef,fi_numeroReferencia AS numDocRef,fi_fechaReferencia AS fecDocRef,fi_nroMaqRegis AS nroMaqReg,
IF(fi_baseImpobible=0,'',fi_baseImpobible) AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_notas_credito_final_$codigo;";
//$data = DB::select(utf8_encode($query_select));
        $data = DB::select(utf8_encode($query_select));
        return collect($data);
    }

    public static function tmp_pagos_alexia_concar_notas_debito($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_notasdebito_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_notasdebito_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_observaciones varchar(70),
    pa_navisos_cobro varchar(15),
    pa_doc_referencia varchar(15),
    pa_fecha_emision_referencia DATE,
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia_concar_notas_debito($tabla, $data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table($tabla)->insert($data);
    }

    public static function genera_data_concar_notas_debito($fecha_inicio, $fecha_fin, $codigo) {//Guadalupe
        $db_ext = \DB::connection('mysql');
        $query = "SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible FROM
(

SELECT * FROM
(
-- BANCOS ND
--  documentos Debe
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,
CASE pa_banco
    WHEN 'Scotiabank' THEN '106117'
    WHEN 'BBVA' THEN '106115'
    WHEN 'BCP' THEN '104109'
    WHEN 'INTERBANK' THEN '104110'
END AS cuentaContable,
CASE pa_banco
    WHEN 'Scotiabank' THEN '106117'
    WHEN 'BBVA' THEN '106115'
    WHEN 'BCP' THEN '104109'
    WHEN 'INTERBANK' THEN '104110'
END AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'EN' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
CASE sede
    WHEN 'COLONIAL' THEN 'L03'
    WHEN 'CARABAYLLO' THEN 'C03'
    WHEN 'SAN JUAN' THEN 'S03' END AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'001' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'CANC MORA' AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notasdebito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p1

UNION

-- BANCOS ND
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,
CASE sede
    WHEN 'COLONIAL' THEN '12132'
    WHEN 'CARABAYLLO' THEN '12133'
    WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,
pa_ruc_dni AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'ND' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'BV' as tipoDocReferencia,pa_doc_referencia as numeroReferencia,DATE_FORMAT(pa_fecha_emision_referencia, '%d/%m/%Y') as fechaReferencia,'' as nroMaqRegis, pa_cancelado as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'CANC MORA' AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notasdebito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p2

ORDER BY (REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) as c1

UNION

SELECT * FROM
(

-- PROVISION ND
--  documentos debe

SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,
CASE sede
    WHEN 'COLONIAL' THEN '12132'
    WHEN 'CARABAYLLO' THEN '12133'
    WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,
pa_ruc_dni AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'ND' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,case SUBSTRING(pa_doc_referencia,1,1) WHEN 'B' THEN 'BV' WHEN 'F' THEN 'FT' END as tipoDocReferencia,pa_doc_referencia as numeroReferencia,DATE_FORMAT(pa_fecha_emision_referencia, '%d/%m/%Y') as fechaReferencia,'' as nroMaqRegis, pa_cancelado as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'MORA' AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7B' WHEN 'CARABAYLLO' THEN '8B' WHEN 'SAN JUAN' THEN '11B' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notasdebito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p3
 
UNION

-- PROVISION ND
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,'772201' AS cuentaContable,
CASE sede
    WHEN 'COLONIAL' THEN 'L01'
    WHEN 'CARABAYLLO' THEN 'C01'
    WHEN 'SAN JUAN' THEN 'S01' 
END AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'ND' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'MORA' AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7B' WHEN 'CARABAYLLO' THEN '8B' WHEN 'SAN JUAN' THEN '11B' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_notasdebito_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('nota credito')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p4
) as c2

ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) AS t1 
ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber;";

        $data = $db_ext->select($query);
        return $data;
    }

    public static function crea_tmp_notas_debito_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_notas_debito_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_notas_debito_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_subDiario char(3), 
    fi_numeroComprobante varchar(10),
    fi_fechaComprobante varchar(10),
    fi_tipoMoneda char(2),
    fi_glosaPrincipal varchar(75),
    fi_tipoCambio char(5),
    fi_tipoConversion char(2),
    fi_moneda char(2),
    fi_fechaTipoCambio varchar(10),
    fi_cuentaContable varchar(10),
    fi_anexo varchar(20),
    fi_centroCosto char(3),
    fi_debeHaber char(1),
    fi_importeOriginal double(9,3),
    fi_importeDolares double(9,3),
    fi_importeSoles double(9,3),
    fi_tipoDocumento char(2),
    fi_numeroDocumento char(25),
    fi_fechaDocumento varchar(10),
    fi_fechaVencimiento varchar(10),
    fi_codigoArea char(4),
    fi_glosaDetalle varchar(30),
    fi_anexoAuxiliar char(3),
    fi_medioPago char(3),
    fi_tipoDocReferencia varchar(3),
    fi_numeroReferencia varchar(15),
    fi_fechaReferencia varchar(10),
    fi_nroMaqRegis char(4),
    fi_baseImpobible double(9,3),
    INDEX(fi_subDiario),
    INDEX(fi_fechaComprobante),
    INDEX(fi_glosaPrincipal),
    INDEX(fi_numeroDocumento)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_notas_debito_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_notas_debito_final_" . $codigo . "("
                . "fi_subDiario,fi_numeroComprobante,fi_fechaComprobante,fi_tipoMoneda,fi_glosaPrincipal,fi_tipoCambio,fi_tipoConversion,fi_moneda,fi_fechaTipoCambio,fi_cuentaContable,fi_anexo,fi_centroCosto,fi_debeHaber,fi_importeOriginal,
    fi_importeDolares,fi_importeSoles,fi_tipoDocumento,fi_numeroDocumento,fi_fechaDocumento,fi_fechaVencimiento,fi_codigoArea,fi_glosaDetalle,fi_anexoAuxiliar,fi_medioPago,fi_tipoDocReferencia,fi_numeroReferencia,
    fi_fechaReferencia,fi_nroMaqRegis,fi_baseImpobible) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar_notas_debito($codigo) {
        $db_ext = \DB::connection('mysql');
//DB::statement("SET NAMES utf8");
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'
UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION

SELECT '' AS tamanio,fi_subDiario AS subdiario,CONCAT(LPAD(SUBSTRING(fi_fechaComprobante,4,2),2,'0'),LPAD(fi_numeroComprobante,4,'0')) as numeroCom ,
fi_fechaComprobante as fechaCompro,fi_tipoMoneda as moneda,fi_glosaPrincipal AS glosa,fi_tipoCambio AS tipoCambio,fi_tipoConversion AS tipoConversion,fi_moneda AS flagConMoneda,
fi_fechaTipoCambio AS fechaTipCambio,fi_cuentaContable AS cuentaContable,fi_anexo AS codAnexo,fi_centroCosto AS centroCosto,fi_debeHaber AS dh,fi_importeOriginal AS importe,
fi_importeDolares AS importeDolar,fi_importeSoles AS importeSoles,fi_tipoDocumento AS tipdoc,fi_numeroDocumento AS numDoc,fi_fechaDocumento AS fechaDoc,fi_fechaVencimiento AS fechaVen,
fi_codigoArea AS codArea,fi_glosaDetalle AS glosaDetalle,fi_anexoAuxiliar AS anexoAux,fi_medioPago AS medioPago,fi_tipoDocReferencia as tipoDocRef,fi_numeroReferencia AS numDocRef,fi_fechaReferencia AS fecDocRef,fi_nroMaqRegis AS nroMaqReg,
IF(fi_baseImpobible=0,'',fi_baseImpobible) AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_notas_debito_final_$codigo;";
//$data = DB::select(utf8_encode($query_select));
        $data = DB::select(utf8_encode($query_select));
        return collect($data);
    }

    public static function tmp_pagos_alexia_concar_facturas($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_facturas_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_facturas_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_observaciones varchar(70),
    pa_navisos_cobro varchar(15),
    pa_doc_referencia varchar(15),
    pa_fecha_emision_referencia DATE,
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia_concar_facturas($tabla, $data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table($tabla)->insert($data);
    }

    public static function genera_data_concar_facturas($fecha_inicio, $fecha_fin, $codigo) {
        $db_ext = \DB::connection('mysql');
        $query = "SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible FROM
(

SELECT * FROM
(
-- BANCOS FACTURAS
--  documentos Debe
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaTipoCambio,
CASE pa_banco
		WHEN 'Scotiabank' THEN '106117'
		WHEN 'BBVA' THEN '106115'
		WHEN 'BCP' THEN '104109'
		WHEN 'INTERBANK' THEN '104110'
END AS cuentaContable,
CASE pa_banco
		WHEN 'Scotiabank' THEN '106117'
		WHEN 'BBVA' THEN '106115'
		WHEN 'BCP' THEN '104109'
		WHEN 'INTERBANK' THEN '104110'
END AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'EN' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaVencimiento,
CASE sede
    WHEN 'COLONIAL' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'L01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*L02*/ 'L01' ELSE
		/*L04*/ 'L01' END END 
    WHEN 'CARABAYLLO' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'C01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*C02*/ 'C01' ELSE
        /*C04*/ 'C01' END END 
    WHEN 'SAN JUAN' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'S01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*S02*/ 'S01' ELSE
		/*S04*/ 'S01' END END 
END AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'001' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'CANC PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'CANC MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'CANC MORA' ELSE 
'CANC TRAMITES VR' END END END AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia
FROM tmp_pagos_alexia_concar_facturas_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('factura')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' AND pa_cancelado>0)  as a ORDER BY pa_fecha_pago,sede)
as p1

UNION

-- BANCOS FACTURAS
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaTipoCambio,
CASE sede
	WHEN 'COLONIAL' THEN '12132'
	WHEN 'CARABAYLLO' THEN '12133'
	WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,
pa_ruc_dni AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_cancelado) as importeOriginal,'' as importeDolares,(pa_cancelado) as importeSoles,'FT' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'CANC PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'CANC MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'CANC MORA' ELSE 
'CANC TRAMITES VR' END END END AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7H' WHEN 'CARABAYLLO' THEN '8H' WHEN 'SAN JUAN' THEN '11H' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_facturas_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('factura')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' AND pa_cancelado>0)  as a ORDER BY pa_fecha_pago,sede)
as p2

ORDER BY (REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) as c1

UNION

SELECT * FROM
(

-- PROVISION FACTURAS
--  documentos debe

SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaTipoCambio,
CASE sede
	WHEN 'COLONIAL' THEN '12132'
	WHEN 'CARABAYLLO' THEN '12133'
	WHEN 'SAN JUAN' THEN '12134'
END AS cuentaContable,pa_ruc_dni AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_total) as importeOriginal,'' as importeDolares,(pa_total) as importeSoles,'FT' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'MORA' ELSE 
'TRAMITES VR' END END END AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7B' WHEN 'CARABAYLLO' THEN '8B' WHEN 'SAN JUAN' THEN '11B' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_facturas_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('factura')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p3
 
UNION

-- PROVISION FACTURAS
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaTipoCambio,
CASE sede
	WHEN 'COLONIAL' THEN IF(pa_observaciones='PENSION','7032102',IF(pa_observaciones='ANTICIPO PENSION', '122104', IF(pa_observaciones='ANTICIPO MATRICULA','122105',IF(pa_observaciones='MATRICULA','7032101',IF(pa_observaciones='TRAMITES VR','7032103','')))))
	WHEN 'CARABAYLLO' THEN IF(pa_observaciones='PENSION','7032102',IF(pa_observaciones='ANTICIPO PENSION', '122104', IF(pa_observaciones='ANTICIPO MATRICULA','122105',IF(pa_observaciones='MATRICULA','7032101',IF(pa_observaciones='TRAMITES VR','7032103','')))))
	WHEN 'SAN JUAN' THEN IF(pa_observaciones='PENSION','7032102',IF(pa_observaciones='ANTICIPO PENSION', '122104', IF(pa_observaciones='ANTICIPO MATRICULA','122105',IF(pa_observaciones='MATRICULA','7032101',IF(pa_observaciones='TRAMITES VR','7032103','')))))
END AS cuentaContable,
CASE sede
    WHEN 'COLONIAL' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'L01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*L02*/ 'L01' ELSE
		/*L04*/ 'L01' END END 
    WHEN 'CARABAYLLO' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'C01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*C02*/ 'C01' ELSE
		/*C04*/ 'C01' END END 
    WHEN 'SAN JUAN' THEN 
		CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'S01' ELSE 
		CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*S02*/ 'S01' ELSE
        /*S04*/ 'S01' END END 
END AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_total) as importeOriginal,'' as importeDolares,(pa_total) as importeSoles,'FT' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,CASE WHEN nroMes>mesActual THEN 'ANTICIPO PENSION' ELSE 
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 AND anioActual>year(pa_fecha_pago) THEN 'ANTICIPO MATRICULA' ELSE 
CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN 'PENSION' ELSE
CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'MATRICULA' ELSE 
CASE WHEN LOCATE('MORA',LOWER(pa_concepto))>0 THEN 'MORA' ELSE 
'TRAMITES VR' END END END END END AS pa_observaciones,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7B' WHEN 'CARABAYLLO' THEN '8B' WHEN 'SAN JUAN' THEN '11B' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM (
SELECT a0.*,
CASE WHEN LOCATE('enero',pa_concepto)>0 THEN 1 WHEN LOCATE('febrero',pa_concepto)>0 THEN 2 WHEN LOCATE('marzo',pa_concepto)>0 THEN 3
WHEN LOCATE('abril',pa_concepto)>0 THEN 4 WHEN LOCATE('mayo',pa_concepto)>0 THEN 5 WHEN LOCATE('junio',pa_concepto)>0 THEN 6 WHEN LOCATE('julio',pa_concepto)>0 THEN 7 
WHEN LOCATE('agosto',pa_concepto)>0 THEN 8 WHEN LOCATE('septiembre',pa_concepto)>0 THEN 9  WHEN LOCATE('octubre',pa_concepto)>0 THEN 10 
WHEN LOCATE('noviembre',pa_concepto)>0 THEN 11 WHEN LOCATE('diciembre',pa_concepto)>0 THEN 12 END nroMes,MONTH(pa_fecha_pago) as mesActual,
CASE WHEN LOCATE(YEAR(pa_fecha_pago),pa_concepto)>0 THEN YEAR(pa_fecha_pago) ELSE
CASE WHEN LOCATE(YEAR(DATE_ADD(pa_fecha_pago, INTERVAL 1 YEAR)),pa_concepto)>0 THEN YEAR(DATE_ADD(pa_fecha_pago, INTERVAL 1 YEAR)) END END AS anioActual 
FROM tmp_pagos_alexia_concar_facturas_$codigo as a0) AS a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('factura')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p4
) as c2

ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) AS t1 ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber;";

        $data = $db_ext->select($query);
        return $data;
    }

    public static function crea_tmp_facturas_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_facturas_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_facturas_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_subDiario char(3), 
    fi_numeroComprobante varchar(10),
    fi_fechaComprobante varchar(10),
    fi_tipoMoneda char(2),
    fi_glosaPrincipal varchar(75),
    fi_tipoCambio char(5),
    fi_tipoConversion char(2),
    fi_moneda char(2),
    fi_fechaTipoCambio varchar(10),
    fi_cuentaContable varchar(10),
    fi_anexo varchar(20),
    fi_centroCosto char(3),
    fi_debeHaber char(1),
    fi_importeOriginal double(9,3),
    fi_importeDolares double(9,3),
    fi_importeSoles double(9,3),
    fi_tipoDocumento char(2),
    fi_numeroDocumento char(25),
    fi_fechaDocumento varchar(10),
    fi_fechaVencimiento varchar(10),
    fi_codigoArea char(4),
    fi_glosaDetalle varchar(30),
    fi_anexoAuxiliar char(3),
    fi_medioPago char(3),
    fi_tipoDocReferencia varchar(3),
    fi_numeroReferencia varchar(15),
    fi_fechaReferencia varchar(10),
    fi_nroMaqRegis char(4),
    fi_baseImpobible double(9,3),
    INDEX(fi_subDiario),
    INDEX(fi_fechaComprobante),
    INDEX(fi_glosaPrincipal),
    INDEX(fi_numeroDocumento)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_facturas_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_facturas_final_" . $codigo . "("
                . "fi_subDiario,fi_numeroComprobante,fi_fechaComprobante,fi_tipoMoneda,fi_glosaPrincipal,fi_tipoCambio,fi_tipoConversion,fi_moneda,fi_fechaTipoCambio,fi_cuentaContable,fi_anexo,fi_centroCosto,fi_debeHaber,fi_importeOriginal,
    fi_importeDolares,fi_importeSoles,fi_tipoDocumento,fi_numeroDocumento,fi_fechaDocumento,fi_fechaVencimiento,fi_codigoArea,fi_glosaDetalle,fi_anexoAuxiliar,fi_medioPago,fi_tipoDocReferencia,fi_numeroReferencia,
    fi_fechaReferencia,fi_nroMaqRegis,fi_baseImpobible) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar_facturas($codigo) {
        $db_ext = \DB::connection('mysql');
//DB::statement("SET NAMES utf8");
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'

UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION

SELECT '' AS tamanio,fi_subDiario AS subdiario,CONCAT(LPAD(SUBSTRING(fi_fechaComprobante,4,2),2,'0'),LPAD(fi_numeroComprobante,4,'0')) as numeroCom ,
fi_fechaComprobante as fechaCompro,fi_tipoMoneda as moneda,fi_glosaPrincipal AS glosa,fi_tipoCambio AS tipoCambio,fi_tipoConversion AS tipoConversion,fi_moneda AS flagConMoneda,
fi_fechaTipoCambio AS fechaTipCambio,fi_cuentaContable AS cuentaContable,fi_anexo AS codAnexo,fi_centroCosto AS centroCosto,fi_debeHaber AS dh,fi_importeOriginal AS importe,
fi_importeDolares AS importeDolar,fi_importeSoles AS importeSoles,fi_tipoDocumento AS tipdoc,fi_numeroDocumento AS numDoc,fi_fechaDocumento AS fechaDoc,fi_fechaVencimiento AS fechaVen,
fi_codigoArea AS codArea,fi_glosaDetalle AS glosaDetalle,fi_anexoAuxiliar AS anexoAux,fi_medioPago AS medioPago,fi_tipoDocReferencia as tipoDocRef,fi_numeroReferencia AS numDocRef,fi_fechaReferencia AS fecDocRef,fi_nroMaqRegis AS nroMaqReg,
IF(fi_baseImpobible=0,'',fi_baseImpobible) AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_facturas_final_$codigo;";
//$data = DB::select(utf8_encode($query_select));
        $data = DB::select(utf8_encode($query_select));
        return collect($data);
    }

    public static function tmp_pagos_alexia_concar_becados($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_pagos_alexia_concar_becados_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_pagos_alexia_concar_becados_" . $codigo . "( 
    id_pa int (11) primary key not null AUTO_INCREMENT, 
    pa_fecha_cargo DATE, 
    pa_fecha_venc DATE, 
    pa_fecha_pago DATE,
    pa_fecha_emi DATE, 
    pa_matricula varchar(15), 
    pa_serie varchar(4), 
    pa_numero varchar(8),
    pa_ruc_dni varchar(12),
    pa_cliente varchar(60),
    pa_concepto varchar(120), 
    pa_serie_ticke varchar(6), 
    pa_descuento double(8,2),
    pa_base_imp double(8,2),
    pa_igv double(8,2),
    pa_total double(8,2),
    pa_cancelado double(8,2),
    pa_tc double(8,2),
    pa_tipo varchar(8),
    pa_centro varchar(60),
    pa_estado_compro varchar(20),
    pa_banco varchar(15),
    pa_observaciones varchar(70),
    pa_navisos_cobro varchar(15),
    pa_doc_referencia varchar(15),
    pa_fecha_emision_referencia DATE,
    pa_estado char(1),
    INDEX (pa_serie),
    INDEX (pa_numero),
    INDEX (pa_ruc_dni),
    INDEX (pa_fecha_pago),
    INDEX (pa_total)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci;";
        $db_ext->select($query_create);
    }

    public static function carga_pagos_alexia_concar_becados($tabla, $data) {
        $db_ext = \DB::connection('mysql');
        $db_ext->table($tabla)->insert($data);
    }

    public static function genera_data_concar_becados($fecha_inicio, $fecha_fin, $codigo) {
        $db_ext = \DB::connection('mysql');
        $query = "
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible FROM
(

SELECT * FROM
(
-- EXTORNO BECADOS
--  documentos Debe
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,'74102' AS cuentaContable,
CASE sede
    WHEN 'COLONIAL' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'L01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'L02'*/ 'L01' ELSE
	/*'L04'*/ 'L01' END END 
    WHEN 'CARABAYLLO' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'C01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'C02'*/'C01' ELSE
	/*'C04'*/ 'C01' END END 
    WHEN 'SAN JUAN' THEN 
        CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'S01' ELSE 
        CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'S02'*/ 'S01' ELSE
	/*'S04'*/ 'S01' END END 
END AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_descuento*-1) as importeOriginal,'' as importeDolares,(pa_descuento*-1) as importeSoles,'BV' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'EXTORNO BECADOS' AS pa_observaciones,pa_descuento,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7M' WHEN 'CARABAYLLO' THEN '8M' WHEN 'SAN JUAN' THEN '11M' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia
FROM tmp_pagos_alexia_concar_becados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('caja')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_pago>='$fecha_inicio' AND pa_fecha_pago<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p1

UNION

-- EXTORNO BECADOS
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,1 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,'12138' AS cuentaContable,'0000' AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_descuento*-1) as importeOriginal,'' as importeDolares,(pa_descuento*-1) as importeSoles,'BV' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,
DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'EXTORNO BECADOS' AS pa_observaciones,pa_descuento,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7M' WHEN 'CARABAYLLO' THEN '8M' WHEN 'SAN JUAN' THEN '11M' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_becados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('caja')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p2

ORDER BY (REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) as c1

UNION

SELECT * FROM
(

-- PROVISION BECADOS
--  documentos debe

SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaTipoCambio,'12138' AS cuentaContable,'0000' AS codigoAnexo,'' as centroCosto,'D' as debeHaber,(pa_descuento*-1) as importeOriginal,'' as importeDolares,(pa_descuento*-1) as importeSoles,'BV' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_pago, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'BECADOS' AS pa_observaciones,pa_total,pa_descuento,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7C' WHEN 'CARABAYLLO' THEN '8C' WHEN 'SAN JUAN' THEN '11C' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_becados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('caja')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p3
 
UNION

-- PROVISION BECADOS
--  documentos haber
SELECT subDiario,fechaComprobante,tipoMoneda,glosaPrincipal,tipoCambio,tipoConversion,moneda,fechaTipoCambio,cuentaContable,codigoAnexo,
centroCosto,debeHaber,importeOriginal,importeDolares,importeSoles,tipoDocumento,numeroDocumento,fechaDocumento,fechaVencimiento,codigoArea,
glosaDetalle,anexoAuxiliar,medioPago,tipoDocReferencia, numeroReferencia,fechaReferencia,nroMaqRegis,baseImponible,2 as orden
FROM (
SELECT subDiario,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaComprobante,'MN' as tipoMoneda,pa_observaciones as glosaPrincipal,'' as tipoCambio,'V' as tipoConversion,'S' as moneda,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaTipoCambio,'7032105' AS cuentaContable,
CASE sede
    WHEN 'COLONIAL' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('matricula',LOWER(pa_concepto))>0 THEN 'L01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'L02'*/ 'L01' ELSE
	/*'L04'*/ 'L01' END END 
    WHEN 'CARABAYLLO' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'C01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'C02'*/ 'C01' ELSE
	/*'C04'*/ 'C01' END END 
    WHEN 'SAN JUAN' THEN 
	CASE WHEN LOCATE('matrícula',LOWER(pa_concepto))>0 OR LOCATE('maricula',LOWER(pa_concepto))>0 THEN 'S01' ELSE 
	CASE WHEN LOCATE('pensión',LOWER(pa_concepto))>0 OR LOCATE('pension',LOWER(pa_concepto))>0 THEN /*'S02'*/ 'S01' ELSE
	/*'S04'*/ 'S01' END END 
END AS codigoAnexo,'' as centroCosto,'H' as debeHaber,(pa_descuento*-1) as importeOriginal,'' as importeDolares,(pa_descuento*-1) as importeSoles,'BV' as tipoDocumento,CONCAT(pa_serie,'-',pa_numero) as numeroDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaDocumento,DATE_FORMAT(pa_fecha_emi, '%d/%m/%Y') as fechaVencimiento,
'' AS codigoArea,pa_observaciones as glosaDetalle,'' as anexoAuxiliar,'' as medioPago,'' as tipoDocReferencia,'' as numeroReferencia,'' as fechaReferencia,'' as nroMaqRegis, '' as baseImponible,pa_serie as serie,pa_numero as numero
FROM (
SELECT id_pa,pa_fecha_cargo,pa_fecha_venc,pa_fecha_pago,pa_fecha_emi,pa_serie,pa_numero,pa_concepto,'BECADOS' AS pa_observaciones,pa_descuento,pa_total,pa_cancelado,pa_tipo,pa_estado_compro,pa_banco,
CASE WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 2 YEAR)) THEN '1' WHEN YEAR(pa_fecha_emi)<=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR)) THEN '2'
WHEN YEAR(pa_fecha_emi)<=YEAR(NOW()) THEN '3' END AS tipo, serie_sede as sede, CASE serie_sede WHEN 'COLONIAL' THEN '7C' WHEN 'CARABAYLLO' THEN '8C' WHEN 'SAN JUAN' THEN '11C' END as subDiario,
pa_observaciones as observaciones,pa_navisos_cobro as navisos_cobro,pa_doc_referencia as doc_referencia,pa_fecha_emision_referencia as fecha_emision_referencia,pa_ruc_dni,REPLACE(pa_doc_referencia,'/','-') as pa_doc_referencia,pa_fecha_emision_referencia
FROM tmp_pagos_alexia_concar_becados_$codigo a
INNER JOIN (
SELECT * FROM tb_serie WHERE serie_tipo IN ('caja')) as b ON a.pa_serie=b.serie_desc
WHERE pa_estado=1 AND pa_fecha_emi>='$fecha_inicio' AND pa_fecha_emi<='$fecha_fin' )  as a ORDER BY pa_fecha_pago,sede)
as p4
) as c2

ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber
) AS t1 ORDER BY orden,(REPLACE(subdiario,'H','')*1) ASC,fechaComprobante,subDiario,SUBSTRING_INDEX(numeroDocumento,'-',1),SUBSTRING_INDEX(numeroDocumento,'-',-1)*1,debeHaber;";

        //$data = $db_ext->select($query);
        $data = DB::select(utf8_encode($query));
        return $data;
    }

    public static function crea_tmp_becados_final($codigo) {
        $db_ext = \DB::connection('mysql');
        $query_drop = "DROP TABLE IF EXISTS tmp_cbb_becados_final_" . $codigo . ";";
        $db_ext->select($query_drop);
        $query_create = "create table tmp_cbb_becados_final_" . $codigo . "( 
    fi_id int(11) primary key not null AUTO_INCREMENT, 
    fi_subDiario char(3), 
    fi_numeroComprobante varchar(10),
    fi_fechaComprobante varchar(10),
    fi_tipoMoneda char(2),
    fi_glosaPrincipal varchar(75),
    fi_tipoCambio char(5),
    fi_tipoConversion char(2),
    fi_moneda char(2),
    fi_fechaTipoCambio varchar(10),
    fi_cuentaContable varchar(10),
    fi_anexo varchar(20),
    fi_centroCosto char(3),
    fi_debeHaber char(1),
    fi_importeOriginal double(9,3),
    fi_importeDolares double(9,3),
    fi_importeSoles double(9,3),
    fi_tipoDocumento char(2),
    fi_numeroDocumento char(25),
    fi_fechaDocumento varchar(10),
    fi_fechaVencimiento varchar(10),
    fi_codigoArea char(4),
    fi_glosaDetalle varchar(30),
    fi_anexoAuxiliar char(3),
    fi_medioPago char(3),
    fi_tipoDocReferencia varchar(3),
    fi_numeroReferencia varchar(15),
    fi_fechaReferencia varchar(10),
    fi_nroMaqRegis char(4),
    fi_baseImpobible double(9,3),
    INDEX(fi_subDiario),
    INDEX(fi_fechaComprobante),
    INDEX(fi_glosaPrincipal),
    INDEX(fi_numeroDocumento)
    ) ENGINE=InnoDB DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci;";
        $db_ext->select($query_create);
    }

    public static function insertar_tmp_becados_final($codigo, $cadena) {
        $db_ext = \DB::connection('mysql');
        $query_insert = "INSERT INTO tmp_cbb_becados_final_" . $codigo . "("
                . "fi_subDiario,fi_numeroComprobante,fi_fechaComprobante,fi_tipoMoneda,fi_glosaPrincipal,fi_tipoCambio,fi_tipoConversion,fi_moneda,fi_fechaTipoCambio,fi_cuentaContable,fi_anexo,fi_centroCosto,fi_debeHaber,fi_importeOriginal,
    fi_importeDolares,fi_importeSoles,fi_tipoDocumento,fi_numeroDocumento,fi_fechaDocumento,fi_fechaVencimiento,fi_codigoArea,fi_glosaDetalle,fi_anexoAuxiliar,fi_medioPago,fi_tipoDocReferencia,fi_numeroReferencia,
    fi_fechaReferencia,fi_nroMaqRegis,fi_baseImpobible) "
                . "VALUES $cadena";
        $db_ext->select($query_insert);
    }

    public static function lista_concar_becados($codigo) {
        $db_ext = \DB::connection('mysql');
//DB::statement("SET NAMES utf8");
        $query_select = "SELECT 'Campo', 'Sub Diario', 'Número de Comprobante', 'Fecha de Comprobante', 'Código de Moneda', 'Glosa Principal', 'Tipo de Cambio', 'Tipo de Conversión', 'Flag de Conversión de Moneda', 'Fecha Tipo de Cambio', 'Cuenta Contable',
            'Código de Anexo', 'Código de Centro de Costo', 'Debe / Haber' , 'Importe Original', 'Importe en Dólares', 'Importe en Soles', 'Tipo de Documento', 'Número de Documento', 'Fecha de Documento', 'Fecha de Vencimiento', 'Código de Area',
            'Glosa Detalle', 'Código de Anexo Auxiliar', 'Medio de Pago', 'Tipo de Documento de Referencia', 'Número de Documento Referencia', 'Fecha Documento Referencia', 'Nro Máq. Registradora Tipo Doc. Ref.', 'Base Imponible Documento Referencia',
            'IGV Documento Provisión', 'Tipo Referencia en estado MQ', 'Número Serie Caja Registradora', 'Fecha de Operación', 'Tipo de Tasa', 'Tasa Detracción/Percepción', 'Importe Base Detracción/Percepción Dólares', 'Importe Base Detracción/Percepción Soles',
            'Tipo Cambio para F', 'Importe de IGV sin derecho crédito fiscal','Tasa IGV'
            UNION
            SELECT 
'Restricciones',
'Ver T.G. 02',
'Los dos primeros digitos son el mes y los otros 4 siguientes un correlativo',
'',
'Ver T.G. 03',
'',
'Llenar  solo si Tipo de Conversion es " . '"C"' . ". Debe estar entre >=0 y <=9999.999999',
'Solo: " . '"C"' . "= Especial, " . '"M"' . "=Compra, " . '"V"' . "=Venta , " . '"F"' . " De acuerdo a fecha',
'Solo: " . '"S"' . " = Si se convierte, " . '"N"' . "= No se convierte',
'Si  Tipo de Conversion " . '"F"' . "',
'Debe existir en el Plan de Cuentas',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos',
'Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05',
' " . '"D"' . " o " . '"H"' . "',
'Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Dolares. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estar entre >=0 y <=99999999999.99',
'Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversion de Moneda esta en " . '"N"' . ", debe estra entre >=0 y <=99999999999.99',
'Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06',
'Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Numero',
'Si Cuenta Contable tiene habilitado el Documento Referencia',
'Si Cuenta Contable tiene habilitada la Fecha de Vencimiento',
'Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26',
'',
'Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia',
'Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. " . '"S1"' . "',
'Si Tipo de Documento es " . '"NA"' . " o " . '"ND"' . " Ver T.G. 06',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ", incluye Serie y Numero',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . ". Solo cuando el Tipo Documento de Referencia " . '"TK"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si Tipo de Documento es " . '"NC"' . ", " . '"NA"' . " o " . '"ND"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es " . '"TK"' . "',
'Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es " . '"TK"' . ", consignar la fecha de emision del ticket',
'Si la Cuenta Contable tiene configurada la Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29',
'Si la Cuenta Contable tiene conf. en Tasa:  Si es " . '"1"' . " ver T.G. 28 y " . '"2"' . " ver T.G. 29. Debe estar entre >=0 y <=999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99',
'Especificar solo si Tipo Conversion es " . '"F"' . ". Se permite " . '"M"' . " Compra y " . '"V"' . " Venta.',
'Especificar solo para comprobantes de compras con IGV sin derecho de credito Fiscal. Se detalle solo en la cuenta 42xxxx',
'Obligatorio para comprobantes de compras, valores validos 0,10,18'
UNION
SELECT
'Tamaño/Formato','4 Caracteres','6 Caracteres','dd/mm/aaaa','2 Caracteres','40 Caracteres','Numerico 11, 6','1 Caracteres','1 Caracteres','dd/mm/aaaa','12 Caracteres','18 Caracteres','6 Caracteres','1 Caracter','Numerico 14,2','Numerico 14,2','Numerico 14,2','2 Caracteres','20 Caracteres','dd/mm/aaaa','dd/mm/aaaa','3 Caracteres','30 Caracteres','18 Caracteres','8 Caracteres','2 Caracteres','20 Caracteres','dd/mm/aaaa','20 Caracteres','Numerico 14,2 ','Numerico 14,2',' " . '"MQ"' . "','15 caracteres','dd/mm/aaaa','5 Caracteres','Numerico 14,2','Numerico 14,2','Numerico 14,2','1 Caracter','Numerico 14,2','Numerico 14,2'
UNION

SELECT '' AS tamanio,fi_subDiario AS subdiario,CONCAT(LPAD(SUBSTRING(fi_fechaComprobante,4,2),2,'0'),LPAD(fi_numeroComprobante,4,'0')) as numeroCom ,
fi_fechaComprobante as fechaCompro,fi_tipoMoneda as moneda,fi_glosaPrincipal AS glosa,fi_tipoCambio AS tipoCambio,fi_tipoConversion AS tipoConversion,fi_moneda AS flagConMoneda,
fi_fechaTipoCambio AS fechaTipCambio,fi_cuentaContable AS cuentaContable,fi_anexo AS codAnexo,fi_centroCosto AS centroCosto,fi_debeHaber AS dh,fi_importeOriginal AS importe,
fi_importeDolares AS importeDolar,fi_importeSoles AS importeSoles,fi_tipoDocumento AS tipdoc,fi_numeroDocumento AS numDoc,fi_fechaDocumento AS fechaDoc,fi_fechaVencimiento AS fechaVen,
fi_codigoArea AS codArea,fi_glosaDetalle AS glosaDetalle,fi_anexoAuxiliar AS anexoAux,fi_medioPago AS medioPago,fi_tipoDocReferencia as tipoDocRef,fi_numeroReferencia AS numDocRef,fi_fechaReferencia AS fecDocRef,fi_nroMaqRegis AS nroMaqReg,
IF(fi_baseImpobible=0,'',fi_baseImpobible) AS baseImpo,'' AS igv,'' as tipRefMQ,'' AS numSerieCajReg, '' AS fechaOpera,'' AS tipTasa, '' AS tasaDetPer,'' AS importeBaseDol,
'' AS importeBaseSol,'' AS tipCambioF,'' AS importeIgv,'' AS tasaIGV
FROM tmp_cbb_becados_final_$codigo;";
//$data = DB::select(utf8_encode($query_select));
        $data = DB::select(utf8_encode($query_select));
        return collect($data);
    }

}
