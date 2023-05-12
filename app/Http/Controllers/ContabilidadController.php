<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300); //3 minutes

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContabilidadExport;
use App\Exports\ContabilidadExport2;
use App\Exports\ContabilidadExport3;
use App\Exports\EeccBancoExport;
use App\Imports\ScotiabankImport;
use App\Exports\DevengadosExportAnio;
use App\Exports\ConcarExport;
use App\Contabilidad;
use App\Exports\ConcarDevengadosExport;
use App\Exports\ConcarNotasCreditoExport;
use App\Exports\ConcarNotasDebitoExport; //Chinita
use App\Exports\ConcarFacturasExport; //Chinita
use App\Exports\ConcarBecadosExport; //Chinita

class ContabilidadController extends Controller {

    public function reporte_contabilidad(Request $request) {

        $tipo = $request->input('tipo');

        $contabilidadExport = new ContabilidadExport();
        $contabilidadExport2 = new ContabilidadExport2();
        $contabilidadExport3 = new ContabilidadExport3();

        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);

        switch ($tipo) {
            case "1":
                return $contabilidadExport->download('contabilidad_informe_' . $randon . '.xlsx');
                break;
            case "2":
                return $contabilidadExport2->download('detalle_comprobante_' . $randon . '.xlsx');
                break;
            case "3":
                return $contabilidadExport3->download('declaracion_sunat_' . $randon . '.xlsx');
                break;
            default:
                break;
        }
    }

    public function modalsubirArchivo() {
        $per_id = auth()->user()->id;
        Contabilidad::crear_temporal_archivos($per_id);
        $html = '<div class="row">
                            <input type="hidden" name="usuarioId" id="usuarioId" value="' . $per_id . '"/>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="">
                                        <input id="image-file" type="file" name="file" accept="*.*" data-min-file-count="1" multiple >
                                    </div>
                                </div>                
                            </div>
                        </div>';
        return $html;
    }

    //Jesus M
    public function subirArchivos() {
        $per_id = auth()->user()->id;
        $file_name = request()->file->getClientOriginalName();
        //request()->file->move(public_path('archivos'), $file_name);
        $archivo_extension = request()->file->getClientOriginalExtension();
        //dd(request()->file("file"));

        switch (trim($archivo_extension)) {
            case "xls" :
            case "xlsx":
                $import_ec = new ScotiabankImport($per_id);
                //Excel::import($import_ec, storage_path("../public/archivos/" . $file_name));
                Excel::import($import_ec, request()->file("file")->store('tmpContabilidad'));
                break;
            case "TXT" :
            case "txt":
                //jesus revisa
                request()->file->move('larabel2.cbb/public/archivos', $file_name);
                $file = fopen("larabel2.cbb/public/archivos/" . $file_name, "r");
                //$file = fopen("archivos/" . $file_name, "r");
                $contenido = fgets($file);
                $tipo_banco = substr($contenido, 0, 1);
                if ($tipo_banco === "H") {//SCOTIABANK
                    while (!feof($file)) {
                        $contenido = fgets($file);
                        $detalle = substr($contenido, 0, 1);
                        if ($detalle == "D") {
                            $tipo = substr($contenido, 0, 1);
                            $alumno = trim(substr($contenido, 48, 20));
                            $dni = trim(substr($contenido, 18, 8));
                            $concepto = trim(substr($contenido, 157, 20));
                            $codigo = trim(substr($contenido, 33, 15));
                            $importe_origen = (double) (substr($contenido, 72, 7) . "." . substr($contenido, 79, 2));
                            $importe_depositado = ((double) (substr($contenido, 72, 7) . "." . substr($contenido, 79, 2))) + ((double) (substr($contenido, 118, 7) . "." . substr($contenido, 125, 2)));
                            $importe_mora = substr($contenido, 117, 7) . "." . substr($contenido, 124, 2);
                            $oficina = '';
                            $nro_movimiento = " " . substr($contenido, 144, 13);
                            $fecha_pago = substr($contenido, 134, 4) . "-" . substr($contenido, 138, 2) . "-" . substr($contenido, 140, 2);
                            $estado = '1';

                            Contabilidad::carga_temporal_archivos("tmp_lista_archivo_" . $per_id, [
                                'arc_alumno' => $alumno,
                                'arc_dni' => trim($dni),
                                'arc_servicio' => trim($concepto),
                                'arc_documento' => $codigo,
                                'arc_vencimiento' => $fecha_pago,
                                'arc_moneda' => 'S/',
                                'arc_importe_origen' => $importe_origen,
                                'arc_importe_depositado' => $importe_depositado,
                                'arc_importe_mora' => $importe_mora,
                                'arc_fecha_proceso' => $fecha_pago,
                                'arc_hora_proceso' => '',
                                'arc_fecha_pago' => $fecha_pago,
                                'arc_forma_pago' => '',
                                'arc_oficina' => trim($oficina),
                                'arc_nro_operacion' => $nro_movimiento,
                                'arc_referencia' => trim($concepto),
                                'arc_banco' => 'SCOTIABANK',
                                'arc_estado' => $estado
                            ]);
                        }
                    }
                } else {
                    $banco_2 = substr($contenido, 0, 2);
                    if ($banco_2 === "01") {//BBVA
                        while (!feof($file)) {
                            $contenido = fgets($file);
                            $tipo = substr($contenido, 0, 2);
                            $alumno = trim(substr($contenido, 2, 30));
                            $dni = trim(substr($contenido, 32, 10));
                            $concepto = trim(substr($contenido, 42, 25));
                            $codigo = trim(substr($contenido, 67, 13));
                            $importe_origen = ((double) ( substr($contenido, 80, 13)) . "." . substr($contenido, 93, 2));
                            $importe_depositado = ((double) substr($contenido, 95, 13)) . "." . substr($contenido, 108, 2);
                            $importe_mora = ((double) substr($contenido, 110, 13)) . "." . substr($contenido, 123, 2);
                            $oficina = substr($contenido, 125, 4);
                            $nro_movimiento = substr($contenido, 129, 6);
                            $fecha_pago = substr($contenido, 135, 4) . "-" . substr($contenido, 139, 2) . "-" . substr($contenido, 141, 2);
                            $tipo_valor = substr($contenido, 143, 2);
                            $canal_entrada = substr($contenido, 145, 2);
                            $estado = '1';
                            if ($tipo == "02") {
                                Contabilidad::carga_temporal_archivos2("tmp_lista_archivo_" . $per_id, [
                                    'arc_alumno' => $alumno,
                                    'arc_dni' => trim($dni),
                                    'arc_servicio' => trim($concepto),
                                    'arc_documento' => $codigo,
                                    'arc_vencimiento' => $fecha_pago,
                                    'arc_moneda' => 'S/',
                                    'arc_importe_origen' => $importe_origen,
                                    'arc_importe_depositado' => $importe_depositado,
                                    'arc_importe_mora' => $importe_mora,
                                    'arc_fecha_proceso' => $fecha_pago,
                                    'arc_hora_proceso' => '',
                                    'arc_fecha_pago' => $fecha_pago,
                                    'arc_forma_pago' => '',
                                    'arc_oficina' => trim($oficina),
                                    'arc_nro_operacion' => $nro_movimiento,
                                    'arc_referencia' => trim($concepto),
                                    'arc_banco' => 'BBVA',
                                    'arc_estado' => $estado
                                ]);
                            }
                        }
                    } elseif ($banco_2 === "CC") {//BCP
                        while (!feof($file)) {
                            $contenido = fgets($file);
                            $tipo = substr($contenido, 0, 2);
                            $alumno = "";
                            $dni_cadena = trim(substr($contenido, 13, 14));
                            $dni = str_replace("000000", "", $dni_cadena);
                            $concepto = trim(substr($contenido, 27, 30));
                            $codigo = str_replace("000000", "", $dni_cadena);
                            $importe_origen = ((double) ( substr($contenido, 73, 13)) . "." . substr($contenido, 86, 2));
                            $importe_depositado = ((double) substr($contenido, 103, 13)) . "." . substr($contenido, 116, 2);
                            $importe_mora = ((double) substr($contenido, 88, 13)) . "." . substr($contenido, 101, 2);
                            $forma_pago = substr($contenido, 156, 12);
                            $oficina = substr($contenido, 118, 6);
                            $nro_movimiento = substr($contenido, 124, 6);
                            $fecha_pago = substr($contenido, 57, 4) . "-" . substr($contenido, 61, 2) . "-" . substr($contenido, 63, 2);
                            $fecha_venci = substr($contenido, 65, 4) . "-" . substr($contenido, 69, 2) . "-" . substr($contenido, 71, 2);
                            $hora = substr($contenido, 168, 2) . ":" . substr($contenido, 170, 2);
                            $tipo_valor = substr($contenido, 143, 2);
                            $canal_entrada = substr($contenido, 145, 2);
                            $referencia = trim(substr($contenido, 130, 22));
                            $estado = '1';
                            if ($tipo == "DD") {
                                Contabilidad::carga_temporal_archivos3("tmp_lista_archivo_" . $per_id, [
                                    'arc_alumno' => $alumno,
                                    'arc_dni' => trim($dni),
                                    'arc_servicio' => trim($concepto),
                                    'arc_documento' => $nro_movimiento,
                                    'arc_vencimiento' => $fecha_venci,
                                    'arc_moneda' => 'S/',
                                    'arc_importe_origen' => $importe_origen,
                                    'arc_importe_depositado' => $importe_depositado,
                                    'arc_importe_mora' => $importe_mora,
                                    'arc_fecha_proceso' => $fecha_pago,
                                    'arc_hora_proceso' => $hora,
                                    'arc_fecha_pago' => $fecha_pago,
                                    'arc_forma_pago' => $forma_pago,
                                    'arc_oficina' => trim($oficina),
                                    'arc_nro_operacion' => $nro_movimiento,
                                    'arc_referencia' => $referencia,
                                    'arc_banco' => 'BCP',
                                    'arc_estado' => $estado
                                ]);
                            }
                        }
                    } elseif ($banco_2 === "11") {//INTERBANK
                        while (!feof($file)) {
                            $contenido = fgets($file);
                            $tipo = substr($contenido, 0, 2);
                            $alumno = trim(substr($contenido, 22, 30));
                            $dni_cadena = trim(substr($contenido, 2, 20));
                            $dni = str_replace("000000", "", $dni_cadena);
                            $concepto = trim(substr($contenido, 52, 30));
                            $codigo = str_replace("000000", "", $dni_cadena);
                            $importe_origen = ((double) ( substr($contenido, 114, 7)) . "." . substr($contenido, 121, 2));
                            $importe_depositado = ((double) substr($contenido, 114, 7)) . "." . substr($contenido, 121, 2);
                            $importe_mora = (double) ("0.0");
                            $forma_pago = substr($contenido, 112, 2);
                            $oficina = trim(substr($contenido, 97, 15));
                            $nro_movimiento = " " . trim(substr($contenido, 97, 15)) . "";
                            $fecha_pago = substr($contenido, 81, 4) . "-" . substr($contenido, 85, 2) . "-" . substr($contenido, 87, 2);
                            $fecha_venci = substr($contenido, 89, 4) . "-" . substr($contenido, 93, 2) . "-" . substr($contenido, 95, 2);
                            $hora = "";
                            $tipo_valor = substr($contenido, 394, 2);
                            $canal_entrada = substr($contenido, 396, 4);
                            $referencia = trim(substr($contenido, 73, 8));
                            $estado = '1';
                            if ($tipo == "13") {
                                Contabilidad::carga_temporal_archivos4("tmp_lista_archivo_" . $per_id, [
                                    'arc_alumno' => $alumno,
                                    'arc_dni' => trim($dni),
                                    'arc_servicio' => trim($concepto),
                                    'arc_documento' => $nro_movimiento,
                                    'arc_vencimiento' => $fecha_venci,
                                    'arc_moneda' => 'S/',
                                    'arc_importe_origen' => $importe_origen,
                                    'arc_importe_depositado' => $importe_depositado,
                                    'arc_importe_mora' => $importe_mora,
                                    'arc_fecha_proceso' => $fecha_pago,
                                    'arc_hora_proceso' => $hora,
                                    'arc_fecha_pago' => $fecha_pago,
                                    'arc_forma_pago' => $forma_pago,
                                    'arc_oficina' => trim($oficina),
                                    'arc_nro_operacion' => $nro_movimiento,
                                    'arc_referencia' => $referencia,
                                    'arc_banco' => 'INTERBANK',
                                    'arc_estado' => $estado
                                ]);
                            }
                        }
                    }
                }
                break;
            default:
                break;
        }
        return response()->json(['uploaded' => '../../archivos/' . $file_name]);
    }

    public function reporte_eecc_banco() {
        $contabilidadExport = new EeccBancoExport();
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        return $contabilidadExport->download("eecc_bancos_" . $randon . ".xlsx");
    }

    public function modalinfoValidada(Request $request) {

        $contabilidad_controller = new ContabilidadController();
        $html = "";
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fecha_inicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fecha_fin'));

        $per_id = auth()->user()->id;

        $lista_ec_alexia = Contabilidad::listaEecc_pagos($per_id, $fecha_inicio, $fecha_fin);

        $lista_pfacturacion = Contabilidad::listaPagos_facturacion($per_id, $fecha_inicio, $fecha_fin);

        $html = '<input type="hidden" name="usuarioId" id="usuarioId" value="' . $per_id . '"/>
          <div class="row">
          <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
          <div class="card">
          <h5 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
          Informaci&oacute;n del Estado de Cuenta que no se encuentran en Alexia
          </h5>
          <div class="card-body table-responsive" style="max-height: 500px;overflow: auto;font-size:12px;">';
        $html .= '<div class="table-responsive">'
                . '<table class="table table-hover table-sm" >'
                . '<thead>'
                . '<tr><th style="white-space:nowrap">Nro.</th><th style="white-space:nowrap">FECHA DE PAGO</th><th style="white-space:nowrap">BANCO</th><th style="white-space:nowrap">DNI / RUC</th><th style="white-space:nowrap">APELLIDOS Y NOMBRES</th><th style="white-space:nowrap">MONTO</th></tr>'
                . '</thead>'
                . '<tbody>';
        $i = 1;
        if (count($lista_ec_alexia) > 0) {
            foreach ($lista_ec_alexia as $lista) {
                $html .= '<tr style="color:' . $lista->color . '"><td>' . $i . '</td><td>' . $lista->fecha_pago . '</td><td>' . $lista->banco . '</td><td>' . $lista->dni . '</td><td>' . $lista->nombre . '</td><td>' . $lista->total . '</td></tr>';
                $i++;
            }
        } else {
            $html .= '<tr><td colspan="6">Sin registros.</td>';
        }

        $html .= '</tbody></table></div>';

        $html .= '</div>
          </div>
          </div>
          <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
          <div class="card">
          <h5 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
          Informaci&oacute;n de Alexia que no se encuentran en Facturaci&oacute;n Electr&oacute;nica
          </h5>
          <div class="card-body table-responsive" style="max-height: 500px;overflow: auto;font-size:12px;">';

        $html .= '<div class="table-responsive">'
                . '<table class="table table-hover table-sm" >'
                . '<thead>'
                . '<tr><th style="white-space:nowrap">Nro.</th><th style="white-space:nowrap">FECHA DE PAGO</th><th style="white-space:nowrap">BOLETA</th><th style="white-space:nowrap">DNI / RUC</th><th style="white-space:nowrap">APELLIDOS Y NOMBRES</th><th style="white-space:nowrap">MONTO</th><th style="white-space:nowrap">TIPO</th></tr>'
                . '</thead>'
                . '<tbody>';
        $j = 1;
        if (count($lista_pfacturacion) > 0) {
            foreach ($lista_pfacturacion as $lista2) {
                $html .= '<tr style="color:' . $lista2->color . '"><td>' . $j . '</td><td>' . $lista2->fecha_pago . '</td><td>' . $lista2->boleta . '</td><td>' . $lista2->documento . '</td><td>' . $lista2->cliente . '</td><td>' . $lista2->total . '</td><td>' . $lista2->respuesta . '</td></tr>';
                $j++;
            }
        } else {
            $html .= '<tr><td colspan="6">Sin registros.</td>';
        }

        $html .= '</tbody></table></div>';

        $html .= "<input type='hidden' id='txtCantEA' value='" . count($lista_ec_alexia) . "'/>";
        $html .= "<input type='hidden' id='txtCantPF' value='" . count($lista_pfacturacion) . "'/>";

        $html .= '</div>
          </div>
          </div>';
        $html .= "*****" . count($lista_ec_alexia) . "*****" . count($lista_pfacturacion);

        return $html;
    }

    public function subirInformacion(Request $request) {

        $contabilidad_controller = new ContabilidadController();
        $resp = "";
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fecha_inicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fecha_fin'));

        $cantEA = $request->input('cantEA');
        $cantPF = $request->input('cantPF');
        $resp1 = "";
        $resp2 = "";
        $per_id = auth()->user()->id;

        $id_grupo = Contabilidad::insertarGrupo([
                    'id_per' => $per_id,
                    'cont_estado' => '1'
        ]);

        if ($cantEA > 0) {
            $resp1 = Contabilidad::insertarEecc_pagos($per_id, $fecha_inicio, $fecha_fin, $id_grupo);
        } else {
            $resp1 = 1;
        }
        if ($cantPF > 0) {
            $resp2 = Contabilidad::insertarpagos_facturacion($per_id, $fecha_inicio, $fecha_fin, $id_grupo);
        } else {
            $resp2 = 1;
        }

        if ($resp1 == 1 && $resp2 == 1) {
            $resp = "Información registrada correctamente.";
        } else {
            $resp = "Error al registrar la información";
        }
        return $resp;
    }

    public function ddmmyyyy_a_yyyymmaa($fecha) {
        $fechita = explode("/", $fecha);
        $nueva_fecha = $fechita[2] . "-" . $fechita[1] . "-" . $fechita[0];
        return $nueva_fecha;
    }

    public function verRegistrosea(Request $request) {
        $cod_grupo = $request->input('grupo');

        $lista_eecc_pagos = Contabilidad::lista_tabla_eecc_pagos_grupos($cod_grupo);
        $html = '<div class="row">
          <div class="col-md-12 col-sm-12" style="margin-bottom: 1.5rem;">
          <div class="card">
        <div class="card-body table-responsive" style="max-height:500px;overflow: auto;font-size:12px;">
        <div class="table-responsive">
                <table class="table table-hover table-sm" >
                <thead>
                <tr>
                <th style="white-space:nowrap">Nro.</th>
                <th style="white-space:nowrap">Fecha de vencimiento</th>
                <th style="white-space:nowrap">Fecha de pago</th>
                <th style="white-space:nowrap">DNI</th>
                <th style="white-space:nowrap">Apellidos y nombres</th>
                <th style="white-space:nowrap">Documento</th>
                <th style="white-space:nowrap">Moneda</th>
                <th style="white-space:nowrap">Total</th>
                <th style="white-space:nowrap">Importe</th>
                <th style="white-space:nowrap">Mora</th>
                <th style="white-space:nowrap">Forma de pago</th>
                <th style="white-space:nowrap">Oficina</th>
                <th style="white-space:nowrap">Operaci&oacute;n</th>
                <th style="white-space:nowrap">Rerefencia</th>
                <th style="white-space:nowrap">Banco</th>
                <th style="white-space:nowrap">Estado</th>
                </tr>
                </thead>
                <tbody>';
        $i = 1;
        if (count($lista_eecc_pagos) > 0) {
            foreach ($lista_eecc_pagos as $lista) {
                $html .= '<tr>'
                        . '<td>' . $i . '</td>'
                        . '<td>' . $lista->fecha_venci . '</td>'
                        . '<td>' . $lista->fecha_pago . '</td>'
                        . '<td>' . $lista->dni . '</td>'
                        . '<td>' . $lista->nombre . '</td>'
                        . '<td>' . $lista->documento . '</td>'
                        . '<td>' . $lista->moneda . '</td>'
                        . '<td>' . $lista->total . '</td>'
                        . '<td>' . $lista->importe . '</td>'
                        . '<td>' . $lista->mora . '</td>'
                        . '<td>' . $lista->forma_pago . '</td>'
                        . '<td>' . $lista->oficina . '</td>'
                        . '<td>' . $lista->operacion . '</td>'
                        . '<td>' . $lista->referencia . '</td>'
                        . '<td>' . $lista->banco . '</td>'
                        . '<td>' . $lista->estado . '</td>'
                        . '</tr>';
                $i++;
            }
        } else {
            $html .= "<tr><td colspan='16'>No se encontraron registros.</td></tr>";
        }

        $html .= '</tbody></table></div>';
        $html .= '</div>
          </div>
          </div>';
        return $html;
    }

    public function verRegistropf(Request $request) {
        $cod_grupo = $request->input('grupo');


        $lista_eecc_pagos = Contabilidad::listaPagos_facturacion_grupos($cod_grupo);
        $html = '<div class="row">
          <div class="col-md-12 col-sm-12" style="margin-bottom: 1.5rem;">
          <div class="card">
        <div class="card-body table-responsive" style="max-height:500px;overflow: auto;font-size:12px;">
        <div class="table-responsive">
                <table class="table table-hover table-sm" >
                <thead>
                <tr>
                <th style="white-space:nowrap">Nro.</th>
                <th style="white-space:nowrap">Fecha de emisi&oacute;n</th>
                <th style="white-space:nowrap">Fecha de pago</th>
                <th style="white-space:nowrap">Boleta</th>
                <th style="white-space:nowrap">DNI</th>
                <th style="white-space:nowrap">Apellidos y nombres</th>
                <th style="white-space:nowrap">Concepto</th>
                <th style="white-space:nowrap">Descuento</th>
                <th style="white-space:nowrap">Base de impuesto</th>
                <th style="white-space:nowrap">IGV</th>
                <th style="white-space:nowrap">Total</th>
                <th style="white-space:nowrap">Cancelado</th>
                <th style="white-space:nowrap">Tipo</th>
                <th style="white-space:nowrap">Estado del comprobante</th>
                <th style="white-space:nowrap">Banco</th>
                <th style="white-space:nowrap">Estado</th>
                </tr>
                </thead>
                <tbody>';
        $j = 1;
        if (count($lista_eecc_pagos) > 0) {
            foreach ($lista_eecc_pagos as $lista) {
                $html .= '<tr>'
                        . '<td>' . $j . '</td>'
                        . '<td>' . $lista->fecha_emi . '</td>'
                        . '<td>' . $lista->fecha_pago . '</td>'
                        . '<td style="white-space:nowrap">' . $lista->boleta . '</td>'
                        . '<td>' . $lista->documento . '</td>'
                        . '<td style="white-space:nowrap">' . $lista->cliente . '</td>'
                        . '<td>' . $lista->concepto . '</td>'
                        . '<td>' . $lista->descuento . '</td>'
                        . '<td>' . $lista->base_imp . '</td>'
                        . '<td>' . $lista->igv . '</td>'
                        . '<td>' . $lista->total . '</td>'
                        . '<td>' . $lista->cancelado . '</td>'
                        . '<td>' . $lista->tipo . '</td>'
                        . '<td>' . $lista->estaCompro . '</td>'
                        . '<td>' . $lista->banco . '</td>'
                        . '<td>' . $lista->estado . '</td>'
                        . '</tr>';
                $j++;
            }
        } else {
            $html .= "<tr><td colspan='16'>No se encontraron registros.</td></tr>";
        }

        $html .= '</tbody></table></div>';
        $html .= '</div>
          </div>
          </div>';
        return $html;
    }

    function reporte_contabilidad_devengado(Request $request) {
        $anio = $request->input('anioDeve');
        $fecha_fin = $request->input('fecha_fin2');
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);

        $contabilidadExportAnio = new DevengadosExportAnio();

        return $contabilidadExportAnio->download('reporte_devengados' . $randon . '.xlsx');
    }

    //JESUS
    function reporte_concar(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $resp = "";
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        $anio_ar1 = explode("-", $fecha_inicio);
        //$anio_ar2= explode("-", $fecha_fin);

        $anio_1 = $anio_ar1[0];
        //$anio_2=$anio_ar2[0];
        //subdiario
        $v7h = $request->input('v7h') - 1;
        $v8h = $request->input('v8h') - 1;
        $v11h = $request->input('v11h') - 1;
        $v7b = $request->input('v7b') - 1;
        $v8b = $request->input('v8b') - 1;
        $v11b = $request->input('v11b') - 1;

        $lista = Contabilidad::genera_data_concar($fecha_inicio, $fecha_fin);
        //chinitos
        if (count($lista) == 0) {
            $lista = Contabilidad::genera_data_concar_ose($fecha_inicio, $fecha_fin, $per_id);
        }

        if (count($lista) > 0) {//chinitos
            Contabilidad::crea_tmp_boleta_colegio($per_id);

            foreach ($lista as $list) {
                $lista1 = Contabilidad::buscar_banco_x_boleta($list->boleta, $list->tipoDocu, $per_id);

                if (count($lista1) > 0) {
                    $resp .= "('" . $list->boleta . "','" . $list->serie . "','" . $list->numero . "','" . $list->fecha . "','" .
                            $list->descripcion . "','" . $list->tipo . "','" . $list->venta . "','" . $lista1[0]->pa_banco . "'),";
                } else {
                    $resp .= "('" . $list->boleta . "','" . $list->serie . "','" . $list->numero . "','" . $list->fecha . "','" .
                            $list->descripcion . "','" . $list->tipo . "','" . $list->venta . "',''),";
                }
            }

            $resp2 = substr($resp, 0, -1);
            Contabilidad::insertar_boleta_colegio($per_id, $resp2);
            $lista_cobranza = Contabilidad::lista_cobranza_canti($per_id);

            $lista_provicion = Contabilidad::lista_cobranza_canti_provision($per_id);

            $cadena = "";
            $cadena3 = "";
            $correlativo = 0;
            $cuenta_contable = "";
            $codigo_anexo = "";
            Contabilidad::crea_tmp_final($per_id);

            //COBRANZAS
            $str_variable = 0;
            $aux_int = 0;
            foreach ($lista_cobranza as $lista) {
                if ($lista->banco != '') {

                    $lista_detalle = Contabilidad::lista_cobranza_detalle($per_id, $lista->fecha, $lista->serie, $lista->orden, $lista->banco);

                    $subdiario = $contabilidad_controller->fnc_obtener_sub_diario($lista->serie);
                    //$lista_cobranza[$aux_int]->serie;
                    if ($aux_int == 0) {
                        switch ($subdiario) {
                            case "7H":
                                $v7h++;
                                $correlativo = $v7h;
                                break;
                            case "8H":
                                $v8h++;
                                $correlativo = $v8h;
                                break;
                            case "11H":
                                $v11h++;
                                $correlativo = $v11h;
                                break;
                            default :
                                $correlativo = "";
                                break;
                        }
                    } elseif ($aux_int > 0) {
                        $subdiario_ant = $contabilidad_controller->fnc_obtener_sub_diario($lista_cobranza[$aux_int - 1]->serie);

                        $glosa_ant = $subdiario_ant . " COBRANZA " . $lista_cobranza[$aux_int - 1]->banco . " " . substr($lista_cobranza[$aux_int - 1]->fecha, 8, 2) . "-" . substr($lista_cobranza[$aux_int - 1]->fecha, 5, 2) . "-" . substr($lista_cobranza[$aux_int - 1]->fecha, 0, 4);
                        $glosa_act = $subdiario . " COBRANZA " . $lista->banco . " " . substr($lista->fecha, 8, 2) . "-" . substr($lista->fecha, 5, 2) . "-" . substr($lista->fecha, 0, 4);

                        switch ($subdiario) {
                            case "7H":
                                if ($glosa_ant !== $glosa_act) {
                                    $v7h++;
                                    $correlativo = $v7h;
                                }
                                break;
                            case "8H":
                                if ($glosa_ant !== $glosa_act) {
                                    $v8h++;
                                    $correlativo = $v8h;
                                }
                                break;
                            case "11H":
                                if ($glosa_ant !== $glosa_act) {
                                    $v11h++;
                                    $correlativo = $v11h;
                                }
                                break;
                            default :
                                $correlativo = "";
                                break;
                        }
                    }

                    foreach ($lista_detalle as $lista2) {

                        if ($lista2->banco == "SCOTIABANK") {
                            if ($anio_1 <= 2019) {
                                $cuenta_contable = "106114";
                                $codigo_anexo = "106114";
                            } elseif ($anio_1 >= 2020) {
                                $cuenta_contable = "106117";
                                $codigo_anexo = "106117";
                            }
                        } else if ($lista2->banco == "BBVA") {
                            if ($anio_1 <= 2019) {
                                $cuenta_contable = "106119";
                                $codigo_anexo = "106119";
                            } elseif ($anio_1 >= 2020) {
                                $cuenta_contable = "106115";
                                $codigo_anexo = "106115";
                            }
                        }
                        $area_sede = $contabilidad_controller->fnc_obtener_area($lista2->area, $lista2->serie);

                        $cadena .= "('" . $lista2->serie . "','" . $lista2->fecha . "','" . $lista2->tipo . "','" . $lista2->monto . "','" . $lista2->orden . "'," . "'D'" . "," . "'$cuenta_contable'," . "'$codigo_anexo'," .
                                "'COBRANZA " . $lista2->banco . " " . substr($lista2->fecha, 8, 2) . "-" . substr($lista2->fecha, 5, 2) . "-" . substr($lista2->fecha, 0, 4) . "','MN','$correlativo'," .
                                "'$subdiario'," . "'EN'," . "'" . $lista2->serie . "-" . $lista->numero . "'," . "''" . ",'" . $area_sede . "'," . "'001')" . ",";
                    }


                    if ($lista->tipo == "MORA") {//COBRANZA MORA
                        $cuenta = $contabilidad_controller->fnc_obtener_cuenta1("COBRANZA MORA", "H", $lista->serie, $anio_1);
                        $cadena .= "('" . $lista->serie . "','" . $lista->fecha . "','" . "COBRANZA MORA" . "'," . $lista->monto . ",'" . 4 . "'," . "'H'" . "," . "'$cuenta'," . "'0000'," .
                                "'COBRANZA " . $lista->banco . " " . substr($lista->fecha, 8, 2) . "-" . substr($lista->fecha, 5, 2) . "-" . substr($lista->fecha, 0, 4) . "','MN','$correlativo'," .
                                "'$subdiario'," . "'BV'," . "'" . $lista->serie . "-" . $lista->numero . "'," . "''" . ",'" . "'," . "'')" . ",";
                    } else {//COBRANZA BANCOS
                        $cuenta = $contabilidad_controller->fnc_obtener_cuenta1("COBRANZA BANCOS", "H", $lista->serie, $anio_1);
                        $cadena .= "('" . $lista->serie . "','" . $lista->fecha . "','" . "COBRANZA BANCOS" . "'," . $lista->monto . ",'" . 2 . "'," . "'H'" . "," . "'$cuenta'," . "'0000'," .
                                "'COBRANZA " . $lista->banco . " " . substr($lista->fecha, 8, 2) . "-" . substr($lista->fecha, 5, 2) . "-" . substr($lista->fecha, 0, 4) . "','MN','$correlativo'," .
                                "'$subdiario'," . "'BV'," . "'" . $lista->serie . "-" . $lista->numero . "'," . "''" . ",'" . "'," . "'')" . ",";
                    }

                    $aux_int++;
                }
            }
            $cadena2 = substr($cadena, 0, -1);

            Contabilidad::insertar_tmp_final($per_id, $cadena2);


            //PROVISIONES
            $dh = "";
            $aux_int2 = 0;
            $detalle_provi = "";
            $detalle_provi2 = "";
            foreach ($lista_provicion as $lista3) {
                $lista_detalle2 = Contabilidad::lista_cobranza_detalle_provision($per_id, $lista3->fecha, $lista3->serie, $lista3->orden, $lista3->tip);
                $dh = "D";
                $subdiario = $contabilidad_controller->fnc_obtener_sub_diario2($lista3->serie);

                if ($aux_int2 == 0) {
                    switch ($subdiario) {
                        case "7B":
                            $v7b++;
                            $correlativo = $v7b;
                            break;
                        case "8B":
                            $v8b++;
                            $correlativo = $v8b;
                            break;
                        case "11B":
                            $v11b++;
                            $correlativo = $v11b;
                            break;
                        default :
                            $correlativo = "";
                            break;
                    }
                } elseif ($aux_int2 > 0) {
                    $subdiario_ant2 = $contabilidad_controller->fnc_obtener_sub_diario2($lista_provicion[$aux_int2 - 1]->serie);

                    $glosa_ant2 = $subdiario_ant2 . " VENTAS " . substr($lista_provicion[$aux_int2 - 1]->fecha, 8, 2) . "-" . substr($lista_provicion[$aux_int2 - 1]->fecha, 5, 2) . "-" . substr($lista_provicion[$aux_int2 - 1]->fecha, 0, 4);
                    $glosa_act2 = $subdiario . " VENTAS " . substr($lista3->fecha, 8, 2) . "-" . substr($lista3->fecha, 5, 2) . "-" . substr($lista3->fecha, 0, 4);

                    switch ($subdiario) {
                        case "7B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v7b++;
                                $correlativo = $v7b;
                            }
                            break;
                        case "8B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v8b++;
                                $correlativo = $v8b;
                            }
                            break;
                        case "11B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v11b++;
                                $correlativo = $v11b;
                            }
                            break;
                        default :
                            $correlativo = "";
                            break;
                    }
                }

                $aux_int2++;
                if ($lista3->tipo == "MORA") {//COBRANZA MORA
                    $area_sede = $contabilidad_controller->fnc_obtener_area_provicion($lista3->serie);

                    $cuenta = $contabilidad_controller->fnc_obtener_cuenta2("COBRANZA MORA", $subdiario, $dh, $lista3->serie, $anio_1);
                    $cadena3 .= "('" . $lista3->serie . "','" . $lista3->fecha . "','" . "MORA" . "'," . $lista3->monto . ",'" . $lista3->orden . "'," . "'$dh'" . "," . "'$cuenta'," . "'0000'," .
                            "'VENTAS " . substr($lista3->fecha, 8, 2) . "-" . substr($lista3->fecha, 5, 2) . "-" . substr($lista3->fecha, 0, 4) . "','MN','$correlativo'," .
                            "'$subdiario'," . "'BV'," . "'" . $lista3->serie . "-" . $lista3->numero . "'," . "''" . ",'" . $area_sede . "'," . "'')" . ",";
                } else {//COBRANZA BANCOS
                    $area_sede = $contabilidad_controller->fnc_obtener_area_provicion($lista3->serie);

                    $cuenta = $contabilidad_controller->fnc_obtener_cuenta2("COBRANZA BANCOS", $subdiario, $dh, $lista3->serie, $anio_1);

                    if ($lista3->tip == 1) {
                        $detalle_provi = "VENTAS " . substr($lista3->fecha, 8, 2) . "-" . substr($lista3->fecha, 5, 2) . "-" . substr($lista3->fecha, 0, 4);
                        //$detalle_provi2 = "PENSION";
                        $detalle_provi2 = $lista3->tipo;
                    } else {
                        $detalle_provi = "DEVENGADO PENSION";
                        $detalle_provi2 = "DEVENGADO PENSION";
                        switch ($subdiario) {
                            case "7B":
                                $v7b++;
                                $correlativo = $v7b;
                                break;
                            case "8B":
                                $v8b++;
                                $correlativo = $v8b;
                                break;
                            case "11B":
                                $v11b++;
                                $correlativo = $v11b;
                                break;
                            default :
                                $correlativo = "";
                                break;
                        }
                    }
                    $cadena3 .= "('" . $lista3->serie . "','" . $lista3->fecha . "','$detalle_provi2'," . $lista3->monto . ",'" . $lista3->orden . "'," . "'$dh'" . "," . "'$cuenta'," . "'0000'," .
                            "'$detalle_provi','MN','$correlativo'," .
                            "'$subdiario'," . "'BV'," . "'" . $lista3->serie . "-" . $lista3->numero . "'," . "''" . ",'" . $area_sede . "'," . "'')" . ",";
                }

                foreach ($lista_detalle2 as $lista4) {
                    $dh = "H";
                    $subdiario = $contabilidad_controller->fnc_obtener_sub_diario2($lista4->serie);
                    $cuenta = $contabilidad_controller->fnc_obtener_cuenta2($lista4->tipo, $subdiario, $dh, $lista4->serie, $anio_1);
                    $anexo = $contabilidad_controller->fnc_obtener_anexo($cuenta, $dh, $subdiario, $anio_1);


                    $area_sede = $contabilidad_controller->fnc_obtener_area_provicion($lista4->serie);

                    if ($lista4->tip == 1) {
                        $detalle_provi = "VENTAS " . substr($lista4->fecha, 8, 2) . "-" . substr($lista4->fecha, 5, 2) . "-" . substr($lista4->fecha, 0, 4);
                        $detalle_provi2 = $lista4->tipo;
                    } /* else {
                      $detalle_provi = "DEVENGADO PENSION";
                      $detalle_provi2 = "DEVENGADO PENSION";
                      switch ($subdiario) {
                      case "7B":
                      //$v7b++;
                      $correlativo = $v7b;
                      break;
                      case "8B":
                      //$v8b++;
                      $correlativo = $v8b;
                      break;
                      case "11B":
                      //$v11b++;
                      $correlativo = $v11b;
                      break;
                      default :
                      $correlativo = "";
                      break;
                      }
                      } */

                    $cadena3 .= "('" . $lista4->serie . "','" . $lista4->fecha . "','" . $detalle_provi2 . "'," . $lista4->monto . ",'" . $lista4->orden . "'," . "'$dh'" . "," . "'$cuenta'," . "'$anexo'," .
                            "'$detalle_provi','MN','$correlativo'," .
                            "'$subdiario'," . "'BV'," . "'" . $lista4->serie . "-" . $lista3->numero . "'," . "''" . ",'" . $area_sede . "'," . "'')" . ",";
                }
            }

            //DEVENGADO PENSION
            $lista_devengado_pensiones = Contabilidad::lista_devengados_pensiones_cantidades($per_id, $fecha_inicio, $fecha_fin);
            $dh3 = "";
            $aux_int3 = 0;
            $detalle_provi = "";
            $detalle_provi2 = "";
            foreach ($lista_devengado_pensiones as $lista5) {
                $lista_detalle2 = Contabilidad::lista_cobranza_detalle_provision($per_id, $lista3->fecha, $lista3->serie, $lista3->orden, $lista3->tip);
                $dh3 = "D";
                $subdiario = $contabilidad_controller->fnc_obtener_sub_diario2($lista5->serie);

                if ($aux_int3 == 0) {
                    switch ($subdiario) {
                        case "7B":
                            $v7b++;
                            $correlativo = $v7b;
                            break;
                        case "8B":
                            $v8b++;
                            $correlativo = $v8b;
                            break;
                        case "11B":
                            $v11b++;
                            $correlativo = $v11b;
                            break;
                        default :
                            $correlativo = "";
                            break;
                    }
                } elseif ($aux_int3 > 0) {
                    $subdiario_ant2 = $contabilidad_controller->fnc_obtener_sub_diario2($lista_provicion[$aux_int3 - 1]->serie);

                    $glosa_ant2 = $subdiario_ant2 . " VENTAS " . substr($lista_provicion[$aux_int3 - 1]->fecha, 8, 2) . "-" . substr($lista_provicion[$aux_int3 - 1]->fecha, 5, 2) . "-" . substr($lista_provicion[$aux_int3 - 1]->fecha, 0, 4);
                    $glosa_act2 = $subdiario . " VENTAS " . substr($lista5->fecha, 8, 2) . "-" . substr($lista5->fecha, 5, 2) . "-" . substr($lista5->fecha, 0, 4);

                    switch ($subdiario) {
                        case "7B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v7b++;
                                $correlativo = $v7b;
                            }
                            break;
                        case "8B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v8b++;
                                $correlativo = $v8b;
                            }
                            break;
                        case "11B":
                            if ($glosa_ant2 !== $glosa_act2) {
                                $v11b++;
                                $correlativo = $v11b;
                            }
                            break;
                        default :
                            $correlativo = "";
                            break;
                    }
                }

                $aux_int3++;
                if ($lista5->tipo === "DEVENGADO PENSION") {
                    $area_sede = $contabilidad_controller->fnc_obtener_area_provicion($lista5->serie);
                    $cuenta = $contabilidad_controller->fnc_obtener_cuenta2("COBRANZA BANCOS", $subdiario, $dh3, $lista5->serie, $anio_1);
                    if ($lista5->tip == 2) {
                        switch ($subdiario) {
                            case "7B":
                                //$v7b++;
                                $correlativo = $v7b;
                                break;
                            case "8B":
                                //$v8b++;
                                $correlativo = $v8b;
                                break;
                            case "11B":
                                //$v11b++;
                                $correlativo = $v11b;
                                break;
                            default :
                                $correlativo = "";
                                break;
                        }
                    }
                    $cadena3 .= "('" . $lista5->serie . "','" . $lista5->fecha . "','" . $lista5->tipo . "'," . $lista5->monto . ",'" . $lista5->orden . "'," . "'$dh3'" . "," . "'$cuenta'," . "'0000'," . "'" . $lista5->tipo . "','MN','$correlativo'," .
                            "'$subdiario'," . "'BV'," . "'" . $lista5->serie . "-" . $lista5->numero . "'," . "''" . ",'" . $area_sede . "'," . "'')" . ",";

                    $dh2 = "H";
                    $subdiario2 = $contabilidad_controller->fnc_obtener_sub_diario2($lista5->serie);
                    $cuenta2 = $contabilidad_controller->fnc_obtener_cuenta2("PENSION", $subdiario2, $dh2, $lista5->serie, $anio_1);
                    $anexo2 = $contabilidad_controller->fnc_obtener_anexo($cuenta2, $dh2, $subdiario2, $anio_1);

                    $area_sede2 = $contabilidad_controller->fnc_obtener_area_provicion($lista5->serie);

                    if ($lista5->tipo === "DEVENGADO PENSION") {
                        switch ($subdiario) {
                            case "7B":
                                //$v7b++;
                                $correlativo = $v7b;
                                break;
                            case "8B":
                                //$v8b++;
                                $correlativo = $v8b;
                                break;
                            case "11B":
                                //$v11b++;
                                $correlativo = $v11b;
                                break;
                            default :
                                $correlativo = "";
                                break;
                        }
                    }

                    $cadena3 .= "('" . $lista5->serie . "','" . $lista5->fecha . "','" . $lista5->tipo . "'," . $lista5->monto . ",'" . $lista5->orden . "'," . "'$dh2'" . "," . "'$cuenta2'," . "'$anexo2'," . "'" . $lista5->tipo . "','MN','$correlativo'," .
                            "'$subdiario2'," . "'BV'," . "'" . $lista5->serie . "-" . $lista5->numero . "'," . "''" . ",'" . $area_sede2 . "'," . "'')" . ",";
                }
            }
            //DEVENGADO PENSION

            $cadena4 = substr($cadena3, 0, -1);
            Contabilidad::insertar_tmp_final($per_id, $cadena4);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarExport();
        return $concarExport->download('concar_' . $randon . '.xlsx');
    }

    function fnc_obtener_area_provicion($serie) {
        $str_serie = "";
        switch ($serie) {
            case "B017"://carabayllo
                //$str_serie = "C1";
                $str_serie = "";
                break;
            case "B020"://sjl
                //$str_serie = "S1";
                $str_serie = "";
                break;
            case "B044"://colonial
                //$str_serie = "L1";
                $str_serie = "";
                break;
            default:
                break;
        }
        return $str_serie;
    }

    function fnc_obtener_area($area, $serie) {
        $str_serie = "";
        switch ($serie) {
            case "B017"://carabayllo
                $str_serie = "C" . $area;
                break;
            case "B020"://sjl
                $str_serie = "S" . $area;
                break;
            case "B044"://colonial
                $str_serie = "L" . $area;
                break;
            default:
                break;
        }
        return $str_serie;
    }

    function fnc_obtener_sub_diario($serie) {
        $subdiario = "";
        switch ($serie) {
            case "B017"://carabayllo
                $subdiario = "8H";
                break;
            case "B020"://sjl
                $subdiario = "11H";
                break;
            case "B044"://colonial
                $subdiario = "7H";
                break;
            default:
                $subdiario = "";
                break;
        }
        return $subdiario;
    }

    function fnc_obtener_sub_diario2($serie) {
        $subdiario = "";
        switch ($serie) {
            case "B017"://carabayllo
                $subdiario = "8B";
                break;
            case "B020"://sjl
                $subdiario = "11B";
                break;
            case "B044"://colonial
                $subdiario = "7B";
                break;
            default:
                $subdiario = "";
                break;
        }
        return $subdiario;
    }

    function fnc_obtener_cuenta1($tipo, $dh, $serie, $anio) {
        $cuenta = "";
        switch (trim($serie)) {
            case "B044":
                if ($tipo == "COBRANZA BANCOS" && $dh == "H") {
                    $cuenta = "12132";
                } else if ($tipo == "COBRANZA MORA" && $dh == "H") {
                    $cuenta = "12136";
                } else {
                    $cuenta = "";
                }
                break;
            case "B017":
                if ($tipo == "COBRANZA BANCOS" && $dh == "H") {
                    $cuenta = "12133";
                } else if ($tipo == "COBRANZA MORA" && $dh == "H") {
                    $cuenta = "12136";
                } else {
                    $cuenta = "";
                }
                break;
            case "B020":
                if ($tipo == "COBRANZA BANCOS" && $dh == "H") {
                    $cuenta = "12134";
                } else if ($tipo == "COBRANZA MORA" && $dh == "H") {
                    $cuenta = "12136";
                } else {
                    $cuenta = "";
                }
                break;
            default:
                $cuenta = "";
                break;
        }
        return $cuenta;
    }

    function fnc_obtener_cuenta2($tipo, $subdiario, $dh, $serie, $anio) {
        $cuenta = "";
        switch (trim($tipo)) {
            case "ANTICIPO PENSION":
                if ($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") {
                    $cuenta = "122104";
                } else {
                    $cuenta = "";
                }
                break;
            case "ANTICIPO MATRICULA":
                if ($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") {
                    $cuenta = "122105";
                } else {
                    $cuenta = "";
                }
                break;
            case "ANTICIPO TALLER":
                if ($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") {
                    $cuenta = "122107";
                } else {
                    $cuenta = "";
                }
                break;
            case "MORA":
                if (($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") && $dh == "H") {
                    $cuenta = "772201";
                } else {
                    $cuenta = "";
                }
                break;
            case "PENSION":
                if (($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") && $dh == "H") {
                    if ($anio <= 2019) {
                        $cuenta = "704102";
                    } elseif ($anio >= 2020) {
                        $cuenta = "7032102";
                    }
                } else {
                    $cuenta = "";
                }
                break;
            case "MATRICULA":
                if (($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") && $dh == "H") {
                    if ($anio <= 2019) {
                        $cuenta = "704101";
                    } elseif ($anio >= 2020) {
                        $cuenta = "7032101";
                    }
                } else {
                    $cuenta = "";
                }
                break;
            case "TRAMITES VR":
                if (($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") && $dh == "H") {
                    if ($anio <= 2019) {
                        $cuenta = "704104";
                    } elseif ($anio >= 2020) {
                        $cuenta = "7032103";
                    }
                } else {
                    if ($anio <= 2019) {
                        $cuenta = "106114";
                    } elseif ($anio >= 2020) {
                        $cuenta = "106117";
                    }
                }
                break;
            case "TALLER":
                if (($subdiario == "7B" || $subdiario == "8B" || $subdiario == "11B") && $dh == "H") {
                    $cuenta = "704105";
                    if ($anio <= 2019) {
                        $cuenta = "704105";
                    } elseif ($anio >= 2020) {
                        $cuenta = "7032104";
                    }
                } else {
                    $cuenta = "";
                }
                break;
            // otros
            case "COBRANZA BANCOS":
                if ($dh == "D" && $serie == "B044") {
                    $cuenta = "12132";
                } else if ($dh == "D" && $serie == "B017") {
                    $cuenta = "12133";
                } else if ($dh == "D" && $serie == "B020") {
                    $cuenta = "12134";
                } else {
                    $cuenta = "";
                }
                break;
            case "COBRANZA MORA":
                if ($dh == "D") {
                    $cuenta = "12136";
                } else {
                    $cuenta = "";
                }
                break;
            default:
                $cuenta = "";
                break;
        }
        return $cuenta;
    }

    function fnc_obtener_anexo($cuenta, $dh, $subdiario, $anio) {
        $anexo = "";
        switch (trim($subdiario)) {
            case "7B":
                if (($cuenta == "704105" || $cuenta == "704102" || $cuenta == "704101" || $cuenta == "704104" || $cuenta == "772201" /* se agrego-> */ || $cuenta == "7032101" || $cuenta == "7032102" || $cuenta == "7032103" || $cuenta == "7032104") && $dh == "H") {
                    $anexo = "L01";
                } else if ($cuenta == "122105" || $cuenta == "122104") {
                    $anexo = "0000";
                } else {
                    if ($anio <= 2019) {
                        $anexo = "106114";
                    } elseif ($anio >= 2020) {
                        $cuenta = "106117";
                    }
                }
                break;
            case "8B":
                if (($cuenta == "704105" || $cuenta == "704102" || $cuenta == "704101" || $cuenta == "704104" || $cuenta == "772201" || $cuenta == "7032101" || $cuenta == "7032102" || $cuenta == "7032103" || $cuenta == "7032104") && $dh == "H") {
                    $anexo = "C01";
                } else if ($cuenta == "122105" || $cuenta == "122104") {
                    $anexo = "0000";
                } else {
                    if ($anio <= 2019) {
                        $anexo = "106114";
                    } elseif ($anio >= 2020) {
                        $cuenta = "106117";
                    }
                }
                break;
            case "11B":
                if (($cuenta == "704105" || $cuenta == "704102" || $cuenta == "704101" || $cuenta == "704104" || $cuenta == "772201" || $cuenta == "7032101" || $cuenta == "7032102" || $cuenta == "7032103" || $cuenta == "7032104") && $dh == "H") {
                    $anexo = "S01";
                } else if ($cuenta == "122105" || $cuenta == "122104") {
                    $anexo = "0000";
                } else {
                    if ($anio <= 2019) {
                        $anexo = "106114";
                    } elseif ($anio >= 2020) {
                        $cuenta = "106117";
                    }
                }
                break;
            default:
                if ($anio <= 2019) {
                    $anexo = "106114";
                } elseif ($anio >= 2020) {
                    $cuenta = "106117";
                }
                break;
        }
        return $anexo;
    }

    function reporte_concar_devengados(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        //subdiario
        $v7h = $request->input('v7h');
        $v8h = $request->input('v8h');
        $v11h = $request->input('v11h');
        $cant7h = 0;
        $cant8h = 0;
        $cant11h = 0;
        $cadena = "";
        Contabilidad::crea_tmp_devengados_final($per_id);
        $lista = Contabilidad::genera_data_concar_devengados($fecha_inicio, $fecha_fin, $per_id); //listar a devengados

        if (count($lista) > 0) {
            for ($i = 0; $i < count($lista); $i++) {
                $subdiario = $lista[$i]->subDiario;
                $debeOhaber = $lista[$i]->debeHaber;
                switch ($subdiario) {
                    case "7H":
                        if ($cant7h == 0) {
                            $correlativo = $v7h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7h++;
                                $correlativo = $v7h - 1;
                            } else {
                                $correlativo = $v7h;
                            }
                        }
                        $cant7h++;
                        break;
                    case "8H":
                        if ($cant8h == 0) {
                            $correlativo = $v8h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8h++;
                                $correlativo = $v8h - 1;
                            } else {
                                $correlativo = $v8h;
                            }
                        }
                        $cant8h++;
                        break;
                    case "11H":
                        if ($cant11h == 0) {
                            $correlativo = $v11h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11h++;
                                $correlativo = $v11h - 1;
                            } else {
                                $correlativo = $v11h;
                            }
                        }
                        $cant11h++;
                        break;
                    default :
                        $correlativo = "";
                        break;
                }

                $cadena .= "('" . $subdiario . "','" . $correlativo . "','" . $lista[$i]->fechaComprobante . "','" . $lista[$i]->tipoMoneda . "','" . $lista[$i]->glosaPrincipal . "','" . $lista[$i]->tipoCambio . "','" . $lista[$i]->tipoConversion . "','" .
                        $lista[$i]->moneda . "','" . $lista[$i]->fechaTipoCambio . "','" . $lista[$i]->cuentaContable . "','" . $lista[$i]->anexo . "','" . $lista[$i]->centroCosto . "','" . $lista[$i]->debeHaber . "','" . $lista[$i]->importeOriginal . "','" . $lista[$i]->importeDolares .
                        "','" . $lista[$i]->importeSoles . "','" . $lista[$i]->tipoDocumento . "','" . $lista[$i]->numeroDocumento . "','" . $lista[$i]->fechaDocumento . "','" . $lista[$i]->fechaVencimiento . "','" . $lista[$i]->codigoArea . "','" . $lista[$i]->glosaDetalle .
                        "','" . $lista[$i]->anexoAuxiliar . "','" . $lista[$i]->medioPago . "'),";
            }

            $cadena2 = substr($cadena, 0, -1);
            Contabilidad::insertar_tmp_devengados_final($per_id, $cadena2);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarDevengadosExport();
        return $concarExport->download('concar_devengados_' . $randon . '.xlsx');
    }

    function convertirAnumero($fecha) {
        $arreglo = explode("/", $fecha);
        $numero = 0;
        if (count($arreglo) > 0) {
            $numero = $arreglo[2] * 10000 + $arreglo[1] * 100 + $arreglo[0];
        }
        return $numero;
    }

    function reporte_concar_notas_credito(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        //subdiario
        $v7c = $request->input('v7c');
        $v8c = $request->input('v8c');
        $v11c = $request->input('v11c');
        $cant7c = 0;
        $cant8c = 0;
        $cant11c = 0;
        $cadena = "";
        Contabilidad::crea_tmp_notas_credito_final($per_id);
        $lista = Contabilidad::genera_data_concar_notas_credito($fecha_inicio, $fecha_fin, $per_id); //listar a devengados

        if (count($lista) > 0) {
            for ($i = 0; $i < count($lista); $i++) {
                $subdiario = $lista[$i]->subDiario;
                $debeOhaber = $lista[$i]->debeHaber;
                switch ($subdiario) {
                    case "7C":
                        if ($cant7c == 0) {
                            $correlativo = $v7c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7c++;
                                $correlativo = $v7c - 1;
                            } else {
                                $correlativo = $v7c;
                            }
                        }
                        $cant7c++;
                        break;
                    case "8C":
                        if ($cant8c == 0) {
                            $correlativo = $v8c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8c++;
                                $correlativo = $v8c - 1;
                            } else {
                                $correlativo = $v8c;
                            }
                        }
                        $cant8c++;
                        break;
                    case "11C":
                        if ($cant11c == 0) {
                            $correlativo = $v11c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11c++;
                                $correlativo = $v11c - 1;
                            } else {
                                $correlativo = $v11c;
                            }
                        }
                        $cant11c++;
                        break;
                    default :
                        $correlativo = "";
                        break;
                }

                $cadena .= "('" . $subdiario . "','" . $correlativo . "','" . $lista[$i]->fechaComprobante . "','" . $lista[$i]->tipoMoneda . "','" . $lista[$i]->glosaPrincipal . "','" . $lista[$i]->tipoCambio . "','" . $lista[$i]->tipoConversion . "','" .
                        $lista[$i]->moneda . "','" . $lista[$i]->fechaTipoCambio . "','" . $lista[$i]->cuentaContable . "','" . $lista[$i]->codigoAnexo . "','" . $lista[$i]->centroCosto . "','" . $lista[$i]->debeHaber . "','" . $lista[$i]->importeOriginal . "','" . $lista[$i]->importeDolares .
                        "','" . $lista[$i]->importeSoles . "','" . $lista[$i]->tipoDocumento . "','" . $lista[$i]->numeroDocumento . "','" . $lista[$i]->fechaDocumento . "','" . $lista[$i]->fechaVencimiento . "','" . $lista[$i]->codigoArea . "','" . $lista[$i]->glosaDetalle .
                        "','" . $lista[$i]->anexoAuxiliar . "','" . $lista[$i]->medioPago . "','" . $lista[$i]->tipoDocReferencia . "','" . $lista[$i]->numeroReferencia . "','" . $lista[$i]->fechaReferencia . "','" . $lista[$i]->nroMaqRegis . "','" . $lista[$i]->baseImponible . "'),";
            }

            $cadena2 = substr($cadena, 0, -1);
            Contabilidad::insertar_tmp_notas_credito_final($per_id, $cadena2);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarNotasCreditoExport();
        return $concarExport->download('concar_notas_credito_' . $randon . '.xlsx');
    }

    //Chinita
    function reporte_concar_notas_debito(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        //subdiario
        $v7h = $request->input('v7h');
        $v8h = $request->input('v8h');
        $v11h = $request->input('v11h');
        $v7b = $request->input('v7b');
        $v8b = $request->input('v8b');
        $v11b = $request->input('v11b');

        $cant7h = 0;
        $cant8h = 0;
        $cant11h = 0;
        $cant7b = 0;
        $cant8b = 0;
        $cant11b = 0;

        $cadena = "";
        Contabilidad::crea_tmp_notas_debito_final($per_id);
        $lista = Contabilidad::genera_data_concar_notas_debito($fecha_inicio, $fecha_fin, $per_id); //listar las notas de debitos

        if (count($lista) > 0) {
            for ($i = 0; $i < count($lista); $i++) {
                $subdiario = $lista[$i]->subDiario;
                $debeOhaber = $lista[$i]->debeHaber;
                switch ($subdiario) {
                    case "7H":
                        if ($cant7h == 0) {
                            $correlativo = $v7h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7h++;
                                $correlativo = $v7h - 1;
                            } else {
                                $correlativo = $v7h;
                            }
                        }
                        $cant7h++;
                        break;
                    case "8H":
                        if ($cant8h == 0) {
                            $correlativo = $v8h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8h++;
                                $correlativo = $v8h - 1;
                            } else {
                                $correlativo = $v8h;
                            }
                        }
                        $cant8h++;
                        break;
                    case "11H":
                        if ($cant11h == 0) {
                            $correlativo = $v11h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11h++;
                                $correlativo = $v11h - 1;
                            } else {
                                $correlativo = $v11h;
                            }
                        }
                        $cant11h++;
                        break;
                    case "7B":
                        if ($cant7b == 0) {
                            $correlativo = $v7b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7b++;
                                $correlativo = $v7b - 1;
                            } else {
                                $correlativo = $v7b;
                            }
                        }
                        $cant7b++;
                        break;
                    case "8B":
                        if ($cant8b == 0) {
                            $correlativo = $v8b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8b++;
                                $correlativo = $v8b - 1;
                            } else {
                                $correlativo = $v8b;
                            }
                        }
                        $cant8b++;
                        break;
                    case "11B":
                        if ($cant11b == 0) {
                            $correlativo = $v11b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11b++;
                                $correlativo = $v11b - 1;
                            } else {
                                $correlativo = $v11b;
                            }
                        }
                        $cant11b++;
                        break;
                    default :
                        $correlativo = "";
                        break;
                }

                $cadena .= "('" . $subdiario . "','" . $correlativo . "','" . $lista[$i]->fechaComprobante . "','" . $lista[$i]->tipoMoneda . "','" . $lista[$i]->glosaPrincipal . "','" . $lista[$i]->tipoCambio . "','" . $lista[$i]->tipoConversion . "','" .
                        $lista[$i]->moneda . "','" . $lista[$i]->fechaTipoCambio . "','" . $lista[$i]->cuentaContable . "','" . $lista[$i]->codigoAnexo . "','" . $lista[$i]->centroCosto . "','" . $lista[$i]->debeHaber . "','" . $lista[$i]->importeOriginal . "','" . $lista[$i]->importeDolares .
                        "','" . $lista[$i]->importeSoles . "','" . $lista[$i]->tipoDocumento . "','" . $lista[$i]->numeroDocumento . "','" . $lista[$i]->fechaDocumento . "','" . $lista[$i]->fechaVencimiento . "','" . $lista[$i]->codigoArea . "','" . $lista[$i]->glosaDetalle .
                        "','" . $lista[$i]->anexoAuxiliar . "','" . $lista[$i]->medioPago . "','" . $lista[$i]->tipoDocReferencia . "','" . $lista[$i]->numeroReferencia . "','" . $lista[$i]->fechaReferencia . "','" . $lista[$i]->nroMaqRegis . "','" . $lista[$i]->baseImponible . "'),";
            }

            $cadena2 = substr($cadena, 0, -1);
            Contabilidad::insertar_tmp_notas_debito_final($per_id, $cadena2);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarNotasDebitoExport();
        return $concarExport->download('concar_notas_debito_' . $randon . '.xlsx');
    }

    function reporte_concar_facturas(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        //subdiario
        $v7h = $request->input('v7h');
        $v8h = $request->input('v8h');
        $v11h = $request->input('v11h');
        $v7b = $request->input('v7b');
        $v8b = $request->input('v8b');
        $v11b = $request->input('v11b');

        $cant7h = 0;
        $cant8h = 0;
        $cant11h = 0;
        $cant7b = 0;
        $cant8b = 0;
        $cant11b = 0;

        $cadena = "";
        Contabilidad::crea_tmp_facturas_final($per_id);
        $lista = Contabilidad::genera_data_concar_facturas($fecha_inicio, $fecha_fin, $per_id); //listar las facturas

        if (count($lista) > 0) {
            for ($i = 0; $i < count($lista); $i++) {
                $subdiario = $lista[$i]->subDiario;
                $debeOhaber = $lista[$i]->debeHaber;
                switch ($subdiario) {
                    case "7H":
                        if ($cant7h == 0) {
                            $correlativo = $v7h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7h++;
                                $correlativo = $v7h - 1;
                            } else {
                                $correlativo = $v7h;
                            }
                        }
                        $cant7h++;
                        break;
                    case "8H":
                        if ($cant8h == 0) {
                            $correlativo = $v8h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8h++;
                                $correlativo = $v8h - 1;
                            } else {
                                $correlativo = $v8h;
                            }
                        }
                        $cant8h++;
                        break;
                    case "11H":
                        if ($cant11h == 0) {
                            $correlativo = $v11h;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11h++;
                                $correlativo = $v11h - 1;
                            } else {
                                $correlativo = $v11h;
                            }
                        }
                        $cant11h++;
                        break;
                    case "7B":
                        if ($cant7b == 0) {
                            $correlativo = $v7b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7b++;
                                $correlativo = $v7b - 1;
                            } else {
                                $correlativo = $v7b;
                            }
                        }
                        $cant7b++;
                        break;
                    case "8B":
                        if ($cant8b == 0) {
                            $correlativo = $v8b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8b++;
                                $correlativo = $v8b - 1;
                            } else {
                                $correlativo = $v8b;
                            }
                        }
                        $cant8b++;
                        break;
                    case "11B":
                        if ($cant11b == 0) {
                            $correlativo = $v11b;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11b++;
                                $correlativo = $v11b - 1;
                            } else {
                                $correlativo = $v11b;
                            }
                        }
                        $cant11b++;
                        break;
                    default :
                        $correlativo = "";
                        break;
                }

                $cadena .= "('" . $subdiario . "','" . $correlativo . "','" . $lista[$i]->fechaComprobante . "','" . $lista[$i]->tipoMoneda . "','" . $lista[$i]->glosaPrincipal . "','" . $lista[$i]->tipoCambio . "','" . $lista[$i]->tipoConversion . "','" .
                        $lista[$i]->moneda . "','" . $lista[$i]->fechaTipoCambio . "','" . $lista[$i]->cuentaContable . "','" . $lista[$i]->codigoAnexo . "','" . $lista[$i]->centroCosto . "','" . $lista[$i]->debeHaber . "','" . $lista[$i]->importeOriginal . "','" . $lista[$i]->importeDolares .
                        "','" . $lista[$i]->importeSoles . "','" . $lista[$i]->tipoDocumento . "','" . $lista[$i]->numeroDocumento . "','" . $lista[$i]->fechaDocumento . "','" . $lista[$i]->fechaVencimiento . "','" . $lista[$i]->codigoArea . "','" . $lista[$i]->glosaDetalle .
                        "','" . $lista[$i]->anexoAuxiliar . "','" . $lista[$i]->medioPago . "','" . $lista[$i]->tipoDocReferencia . "','" . $lista[$i]->numeroReferencia . "','" . $lista[$i]->fechaReferencia . "','" . $lista[$i]->nroMaqRegis . "','" . $lista[$i]->baseImponible . "'),";
            }

            $cadena2 = substr($cadena, 0, -1);
            Contabilidad::insertar_tmp_facturas_final($per_id, $cadena2);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarFacturasExport();
        return $concarExport->download('concar_facturas_' . $randon . '.xlsx');
    }

    function reporte_concar_becados(Request $request) {
        $per_id = auth()->user()->id;
        $contabilidad_controller = new ContabilidadController();
        $fecha_inicio = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaInicio'));
        $fecha_fin = $contabilidad_controller->ddmmyyyy_a_yyyymmaa($request->input('fechaFin'));

        //subdiario
        $v7m = $request->input('v7m');
        $v8m = $request->input('v8m');
        $v11m = $request->input('v11m');
        $v7c = $request->input('v7c');
        $v8c = $request->input('v8c');
        $v11c = $request->input('v11c');

        $cant7m = 0;
        $cant8m = 0;
        $cant11m = 0;
        $cant7c = 0;
        $cant8c = 0;
        $cant11c = 0;

        $cadena = "";
        Contabilidad::crea_tmp_becados_final($per_id);
        $lista = Contabilidad::genera_data_concar_becados($fecha_inicio, $fecha_fin, $per_id); //listar las facturas

        if (count($lista) > 0) {
            for ($i = 0; $i < count($lista); $i++) {
                $subdiario = $lista[$i]->subDiario;
                $debeOhaber = $lista[$i]->debeHaber;
                switch ($subdiario) {
                    case "7M":
                        if ($cant7m == 0) {
                            $correlativo = $v7m;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7m++;
                                $correlativo = $v7m - 1;
                            } else {
                                $correlativo = $v7m;
                            }
                        }
                        $cant7m++;
                        break;
                    case "8M":
                        if ($cant8m == 0) {
                            $correlativo = $v8m;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8m++;
                                $correlativo = $v8m - 1;
                            } else {
                                $correlativo = $v8m;
                            }
                        }
                        $cant8m++;
                        break;
                    case "11M":
                        if ($cant11m == 0) {
                            $correlativo = $v11m;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11m++;
                                $correlativo = $v11m - 1;
                            } else {
                                $correlativo = $v11m;
                            }
                        }
                        $cant11m++;
                        break;
                    case "7C":
                        if ($cant7c == 0) {
                            $correlativo = $v7c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v7c++;
                                $correlativo = $v7c - 1;
                            } else {
                                $correlativo = $v7c;
                            }
                        }
                        $cant7c++;
                        break;
                    case "8C":
                        if ($cant8c == 0) {
                            $correlativo = $v8c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v8c++;
                                $correlativo = $v8c - 1;
                            } else {
                                $correlativo = $v8c;
                            }
                        }
                        $cant8c++;
                        break;
                    case "11C":
                        if ($cant11c == 0) {
                            $correlativo = $v11c;
                        } else {
                            if ($debeOhaber == "H") {
                                $v11c++;
                                $correlativo = $v11c - 1;
                            } else {
                                $correlativo = $v11c;
                            }
                        }
                        $cant11c++;
                        break;
                    default :
                        $correlativo = "";
                        break;
                }

                $cadena .= "('" . $subdiario . "','" . $correlativo . "','" . $lista[$i]->fechaComprobante . "','" . $lista[$i]->tipoMoneda . "','" . $lista[$i]->glosaPrincipal . "','" . $lista[$i]->tipoCambio . "','" . $lista[$i]->tipoConversion . "','" .
                        $lista[$i]->moneda . "','" . $lista[$i]->fechaTipoCambio . "','" . $lista[$i]->cuentaContable . "','" . $lista[$i]->codigoAnexo . "','" . $lista[$i]->centroCosto . "','" . $lista[$i]->debeHaber . "','" . $lista[$i]->importeOriginal . "','" . $lista[$i]->importeDolares .
                        "','" . $lista[$i]->importeSoles . "','" . $lista[$i]->tipoDocumento . "','" . $lista[$i]->numeroDocumento . "','" . $lista[$i]->fechaDocumento . "','" . $lista[$i]->fechaVencimiento . "','" . $lista[$i]->codigoArea . "','" . $lista[$i]->glosaDetalle .
                        "','" . $lista[$i]->anexoAuxiliar . "','" . $lista[$i]->medioPago . "','" . $lista[$i]->tipoDocReferencia . "','" . $lista[$i]->numeroReferencia . "','" . $lista[$i]->fechaReferencia . "','" . $lista[$i]->nroMaqRegis . "','" . $lista[$i]->baseImponible . "'),";
            }

            $cadena2 = substr($cadena, 0, -1);
            Contabilidad::insertar_tmp_becados_final($per_id, $cadena2);
        }
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $concarExport = new ConcarBecadosExport();
        return $concarExport->download('concar_becados_' . $randon . '.xlsx');
    }

}
