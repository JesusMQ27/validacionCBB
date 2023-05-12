<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300); //3 minutess
//use App\AdminPD;

use App\Exports\DevengadosExport;
use App\Imports\DevengadosImport;
use App\Imports\AlexiaDevengados_import;
use App\Imports\PagosImport;
use App\Imports\AlexiaPagos_import;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;
use App\Devengados;
use App\Imports\NotasCreditos_import;
//chinitos
use App\Imports\ComprobantesOse_import;
use App\Imports\PagosAllImport;

//use Excel;

class DevengadosController extends Controller {

    public function upload(Request $request) {
        $path = $request->file('image')->store('upload');
        echo ($path);
    }

    /*     * ***************** REPORTES *********************** */

    public function deve_reporte_excel(Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'devepfini' => 'required|date',
                    'devepffin' => 'required|date',
        ]);
        if ($validator->fails()) {
            $data['mensaje'] = 'por favor,complete todos los campos correctamente';
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $excel = new DevengadosExport();
        $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $desordenada = str_shuffle($caracteres);
        $randon = substr($desordenada, 1, 6);
        $excel_name = "reporte_devengado_";
        if ($request->input('deverptipo') == 2) {
            $excel_name = "reporte_pago_";
        }
        //chinitos
        if ($request->input('deverptipo') == 5) {
            $excel_name = "reporte_comp_ose_";
        }
        //chinitos
        return $excel->download($excel_name . $randon . '.xlsx');
    }

    public function deve_reporte(Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'devepfini' => 'required|date',
                    'devepfini' => 'required|date',
                    'devepfini' => 'required',
                    'devepfini' => 'required',
        ]);
        if ($validator->fails()) {
            $data['mensaje'] = 'por favor,complete todos los campos correctamente';
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $fini = $request->input('devepfini');
        $ffin = $request->input('devepffin');
        $serie = $request->input('deverpserie');
        $tipo = $request->input('deverptipo');
        $datos = Devengados::deve_reporte([
                    "fini" => "$fini",
                    "ffin" => "$ffin",
                    "serie" => "$serie",
                    "tipo" => "$tipo",
        ]);
        $data['tipo'] = 1;
        //Jesus M
        $data['html'] = "<div class='row'><label class='col-sm-7 col-12 text-sm-right text-left'>Buscar:</label>"
                . "<div class=' col-sm-5 col-12'>"
                . "<input type='text' id='repo_text' class='form-control' onkeyup=doSearch('repo_table','repo_text') />"
                . "</div></div>"
                . "<div class='row' style='margin-top:15px;'>"
                . "<div class='col-12'>"
                . "<div class='table-responsive text-nowrap' style='max-height:500px;overflow:auto;'>"
                . "<table class='table table-hover' id='repo_table'>"
                . "<thead>"
                . "<tr>"
                . "<th id='trp1' class='sort'>Nro.</th>";
        if ($tipo < 5) { //chinitos inicia
            $data['html'] .= "<th id='trp2' class='sort'>Fecha de Emisi&oacute;n</th>";
            if ($tipo == 2) {//pago
                $data['html'] .= "<th id='trp3' class='sort'>Fecha de Pago</th>"
                        . "<th id='trp4' class='sort'>Banco</th>"
                        . "<th id='trp5' class='sort'>Detalle</th>";
            }
            $data['html'] .= "<th id='trp6' class='sort'>Matricula</th>"
                    . "<th id='trp7' class='sort'>DNI</th>"
                    . "<th id='trp8' class='sort'>Boleta</th>"
                    . "<th id='trp9' class='sort'>Alumno</th>"
                    . "<th id='trp10' class='sort'>Cuota</th>"
                    . "<th id='trp11' class='sort'>Monto</th>"
                    . "<th id='trp12' class='sort'>Estado</th>";
            if ($tipo == 3) {//anulado
                $data['html'] .= "<th id='trp13' class='sort'>Detalle</th>"
                        . "<th id='trp14' class='sort'>Boleta afecta</th>";
            }
        } else {//chinitos inicia
            $data['html'] .= "<th id='trp2' class='sort'>Fecha Envio</th>"
                    . "<th id='trp3' class='sort'>Fecha Sistema</th>"
                    . "<th id='trp4' class='sort'>Tipo Comprobante</th>"
                    . "<th id='trp5' class='sort'>Nro. Comprobante</th>"
                    . "<th id='trp6' class='sort'>Nombres</th>"
                    . "<th id='trp7' class='sort'>Monto Neto</th>"
                    . "<th id='trp8' class='sort'>Monto Total</th>"
                    . "<th id='trp9' class='sort'>Tipo Moneda</th>"
                    . "<th id='trp10' class='sort'>IGV</th>"
                    . "<th id='trp11' class='sort'>ISC</th>"
                    . "<th id='trp12' class='sort'>Recargo</th>"
                    . "<th id='trp13' class='sort'>Gratuito</th>"
                    . "<th id='trp14' class='sort'>Estado</th>";
        }
        //chinitos termina
        $data['html'] .= "</tr>"
                . "</thead>"
                . "<tbody>";
        $cd = 0;
        $cp = 0;
        $ca = 0;
        $cose = 0;
        $i = 1;
        $estado = "";

        foreach ($datos as $rs) {
            $color = "";
            if ($tipo == 1) {//devengado
                if ($rs->estado == 1) {
                    $cd++;
                    $estado = "Devengado";
                    $color = "";
                } else if ($rs->estado == 2) {
                    $cp++;
                    $estado = "Pagado";
                    $color = "color:blue";
                } else {
                    $ca++;
                    $estado = "Anulado";
                }
            } else if ($tipo == 2) {//pago
                if ($rs->estado_tipo == 0) {
                    $ca++;
                    $estado = "ANULADO";
                } else {
                    $cp++;
                    $estado = $rs->pago_estado_tipo;
                }
            } else if ($tipo == 4) {//todos los devengados
                if ($rs->estado == 1) {
                    $cd++;
                    $estado = "Devengado";
                    $color = "";
                } else if ($rs->estado == 2) {
                    $cp++;
                    $estado = "Pagado";
                    $color = "color:blue";
                } else {
                    $ca++;
                    $estado = "Anulado";
                    $color = "color:black";
                }
            } else if ($tipo == 3) {//Anulados - notas de creditos
                if ($rs->dev_esta == 0) {
                    $estado = "No hay Doc. Afectado encontrado";
                    $color = "color:red";
                } else {
                    $estado = "Activo";
                    $color = "color:black";
                }
            } else if ($tipo == 5) { //chinitos
                if ($rs->com_estado == 0) {
                    $estado = "Inactivo";
                    $color = "color:red";
                } else {
                    $estado = "Activo";
                    $color = "color:black";
                }
                $cose++;
            }

            $data['html'] .= "<tr style='$color'>";
            if ($tipo < 5) {//chinitos inicio
                $data['html'] .= "<td>$i</td>"
                        . "<td>" . $rs->fecha . "</td>";
                if ($tipo == 2) {//pago
                    $data['html'] .= "<td>$rs->fecha_pago</td>"
                            . "<td>$rs->pago_banco</td>"
                            . "<td>$rs->pago_estado_tipo</td>";
                }
                $data['html'] .= "<td>" . $rs->grado . "</td>"
                        . "<td>" . $rs->dni . "</td>"
                        . "<td>" . $rs->boleta . "</td>"
                        . "<td>" . $rs->alumno . "</td>"
                        . "<td>" . $rs->cuota . "</td>"
                        . "<td>" . $rs->monto . "</td>";
                $data['html'] .= "<td>$estado</td>";
                if ($tipo == 3) {//anulado
                    $ca++;
                    $data['html'] .= "<td>" . $rs->comentario_anulacion . "</td>"
                            . "<td>" . $rs->doc_afecta . "</td>";
                }
            } else {
                $data['html'] .= "<td>$i</td>"
                        . "<td>" . $rs->com_fecha_envio . "</td>"
                        . "<td>" . $rs->com_fecha_sistema . "</td>"
                        . "<td>" . $rs->com_tipo_documento . "</td>"
                        . "<td>" . $rs->com_serie . "-" . $rs->com_numero . "</td>"
                        . "<td>" . $rs->com_doc_iden . "</td>"
                        . "<td>" . $rs->com_neto . "</td>"
                        . "<td>" . $rs->com_total . "</td>"
                        . "<td>" . $rs->com_tip_moneda . "</td>"
                        . "<td>" . $rs->com_igv . "</td>"
                        . "<td>" . $rs->com_isc . "</td>"
                        . "<td>" . $rs->com_recargo . "</td>"
                        . "<td>" . $rs->com_gratuito . "</td>"
                        . "<td>" . $rs->estado . "</td>";
            }
            $data['html'] .= "</tr>";
            $i++;
        }
        $data['html'] .= "</tbody>"
                . "</table>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<div class='row'>"
                . "<div class='col-12 text-right'>";
        if ($tipo == 1) {//devengado
            $data['html'] .= "<label class='col-12'>Cantidad Total Devengados : $cd</label>";
        } else if ($tipo == 2) {//pago
            $data['html'] .= "<label class='col-12' >Cantidad Total Pagantes : $cp</label>";
        } else if ($tipo == 4) {
            $data['html'] .= "<label class='col-12'>Cantidad Total Devengados : $cd</label>"
                    . "<label class='col-12' >Cantidad Total Pagantes : $cp</label>"
                    . "<label class='col-12' >Cantidad Total Anulados : $ca</label>";
        } else if ($tipo == 5) { //chinitos inicio
            $data['html'] .= "<label class='col-12' >Cantidad Total Comprobantes OSE : $cose</label>";
        } else {//anulado
            $data['html'] .= "<label class='col-12' >Cantidad Total Anulados - Notas de créditos : $ca</label>";
        }
        $data['html'] .= "</div>"
                . "</div>";
        return json_encode($data);
    }

    /*     * ***************** CARGA DEVENGADOS *********************** */

    public function upload_devengados(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel' => 'required|mimes:xlsx,xls|max:50000'
        ]);
        if ($validacion->passes()) {
//capturo id de sesion
            $per_id = auth()->user()->id;
//capturo id grupo
            $id_grupo = Devengados::insertGrupo([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
//traigo todas las series
            $serie = Devengados::loadSerie();
//leo excel
            $excel = $request->file('excel')->store('tmp');
//-envio parametro para insertar dentro del excel
            $import = new DevengadosImport($id_grupo, $serie);
            Excel::import($import, $request->file('excel'));
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        'html' => Devengados::selectDevengados_error($id_grupo)
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    public function load_devengado_modal(Request $request) {
        $data = Devengados::load_devengado_modal($request->input('id_devengado'));
        $html['head'] = "Actualizacion Devengado";
        $html['body'] = "
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Grado</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_grado' value='" . $data->deve_grado . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Fecha de Emisi&oacute;n</label>
    <div class='col-sm-7 col-12'>
        <input type='date' class='form-control' id='deve_emision' value='" . $data->deve_fecha . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>DNI</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_dni' value='" . $data->deve_dni . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>N. Boleta</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_boleta' value='" . $data->deve_boleta . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Apellidos y Nombres</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_alumno' value='" . $data->deve_alumno . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Cuota</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_cuota' value='" . $data->deve_cuota . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Monto</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='deve_monto' value='" . $data->deve_monto . "'>
    </div>
</div>
<div class = 'form-group row'>
    <div class='col-12'>
        <button id='update_devengado' class='btn btn-primary'  style='float:right' onclick='update_devengado(" . $data->id_devengado . ")'>Actualizar</button>
    </div>
</div>    
            ";
        $html['footer'] = "";
        return json_encode($html);
    }

    public function update_devengado(Request $request) {
        $data = array();
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'id_devengado' => 'required',
                    'deve_emision' => 'required|date',
                    'deve_dni' => 'required',
                    'deve_boleta' => 'required',
                    'deve_alumno' => 'required',
                    'deve_cuota' => 'required',
                    'deve_monto' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $data['mensaje'] = 'por favor,complete todos los campos correctamente';
            $data['tipo'] = 2;
            return json_encode($data);
        }

        $deve_boleta = explode("-", $request->input('deve_boleta'));
        $id_serie = Devengados::loadSerie_desc($deve_boleta[0]);
        if ($id_serie == null) {
            $data['mensaje'] = "la serie que se ha digitado, no se ha podido encontrar";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $valida_deve_boleta = Devengados::load_deve_boleta($request->input('deve_boleta'));
        if (count((array) $valida_deve_boleta) > 0) {
            $data['mensaje'] = "se ha encontrado un registro con el mismo codigo";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $deve_grado = $request->input("deve_grado");
        $deve_emision = $request->input("deve_emision");
        $deve_dni = $request->input("deve_dni");
        $id_serie = $id_serie->id_serie;
        $deve_num = (int) $deve_boleta[1];
        $deve_boleta = $request->input("deve_boleta");
        $deve_alumno = $request->input("deve_alumno");
        $deve_cuota = $request->input("deve_cuota");
        $deve_monto = $request->input("deve_monto");
        /* Devengados::update_devengado([
          "deve_grado" => "$deve_grado",
          "deve_fecha" => "$deve_emision",
          "deve_dni" => "$deve_dni",
          "id_serie" => "$id_serie",
          "deve_num" => "$deve_num",
          "deve_boleta" => "$deve_boleta",
          "deve_alumno" => "$deve_alumno",
          "deve_cuota" => "$deve_cuota",
          "deve_monto" => "$deve_monto"
          ], $request->input("id_devengado"));
          $data['mensaje'] = "Registro actualizado correctamente";
          $data['tipo'] = 1; *
          Devengados::update_devengado([
          "deve_grado" => "$deve_grado",
          "deve_fecha" => "$deve_emision",
          "deve_dni" => "$deve_dni",
          "id_serie" => "$id_serie",
          "deve_num" => "$deve_num",
          "deve_boleta" => "$deve_boleta",
          "deve_alumno" => "$deve_alumno",
          "deve_cuota" => "$deve_cuota",
          "deve_monto" => "$deve_monto"
          ], $request->input("id_devengado")); */
        $data['mensaje'] = "Registro actualizado correctamente";
        $data['tipo'] = Devengados::update_devengado([
                    "deve_grado" => "$deve_grado",
                    "deve_fecha" => "$deve_emision",
                    "deve_dni" => "$deve_dni",
                    "id_serie" => "$id_serie",
                    "deve_num" => "$deve_num",
                    "deve_boleta" => "$deve_boleta",
                    "deve_alumno" => "$deve_alumno",
                    "deve_cuota" => "$deve_cuota",
                    "deve_monto" => "$deve_monto"
                        ], $request->input("id_devengado"));
        ;
        return json_encode($data);
    }

    /*     * **************** CARGA DEVENGADOS ALEXIA ********************* */

    //Jesus M
    public function upload_devengadosAlexia(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel' => 'required|mimes:xlsx,xls|max:50000'
        ]);
        if ($validacion->passes()) {
//capturo id de sesion
            $per_id = auth()->user()->id;
//capturo id grupo
            $id_grupo = Devengados::insertGrupo([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
//traigo todas las series
            $serie = Devengados::loadSerie();
//leo excel
            $excel = $request->file('excel')->store('tmp');
//-envio parametro de tabla para insertar dentro del excel
            $table_name = "tmp_tb_devengado_" . $per_id;
//creo tabla temporal
            Devengados::uploadDevengadosAlexia_tmp_create($table_name);
//inserto data en la temporal
            $import = new AlexiaDevengados_import($id_grupo, $serie, $table_name);
            Excel::import($import, $excel);
//cargo data de la temporal
            $data_modal = Devengados::selectDevengados_tmp($table_name);
//cargo data erronea de la temporal              
            $data = Devengados::selectDevengados_tmp_error($table_name, $id_grupo);
            $html = "";
            $modal = "<div class='container-fluid'><div class='row'>"
                    . "<div class='col-xs-12 text-right' style='padding-bottom:10px;width:100%;'>"
                    . "<button onclick='reload_modaltmpdeve()' class='btn'><i class='fa fa-sync'></i></button>"
                    . "</div></div></div>"
                    . "<div class='row'><div class='table-responsive text-nowrap' id='modal_devengados_tmp' style='max-height:500px;overflow:auto;'>";
            if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
                $modal .= "<table class='table table-striped'><thead>"
                        . "<th>Nro.</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>CUOTA</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "<th>DETALLE</th>"
                        . "</thead><tbody>";
                $id_serie = 0;
                $deve_num = 0;
                $i = 1;
                $aux = 1;
                $cant_detalle = "";
                if (count($data_modal) == 1) {
                    $cant_detalle = "registro";
                } else {
                    $cant_detalle = "registros";
                }
                $button = "<div class='row'>"
                        . "<div class='col-3'>"
                        . "<button class='btn btn-primary' id='subir_devengados_tmp' onclick='subir_devengados_tmp()'>Subir Devengados</button>"
                        . "</div>"
                        . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
                $error = 0;
                $error_mensaje = "";
                $indice = 1;
                foreach ($data_modal as $rs) {//lista de carga excel
                    $color = "";
                    if ($rs->id_serie != $id_serie) {//cambio de serie
                        $id_serie = $rs->id_serie;
                        $deve_num = 0;
                    } else {//misma serie, empieza correlativo
                        if (($rs->deve_num - $deve_num) != 1) {//error en correlativo
                            $error = 1;
                            $correlativo = $deve_num + 1;
                            $color = "color:red;";
                            $index = $rs->deve_num - $correlativo;
                            for ($i = 0; $i < $index; $i++) {
                                $modal .= "<tr style='$color'><td colspan='23'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $rs->serie_desc-$correlativo</td></tr>";
                                //$deve_num = $deve_num + 1;
                                $correlativo++;
                            }
                        }
                        $color = "";
                    }
                    if ($rs->deve_detalle == "ENCONTRADO") {
                        $color = "color:red";
                    }
                    $modal .= "<tr style='$color' id='$rs->id_devengado'>"
                            . "<td>$aux</td>"
                            . "<td>$rs->deve_fecha_emicar</td>"
                            . "<td>$rs->deve_fecha_venc</td>"
                            . "<td>$rs->deve_fecha_pag</td>"
                            . "<td>$rs->deve_fecha</td>"
                            . "<td>$rs->deve_grado</td>"
                            . "<td>$rs->deve_boleta</td>"
                            . "<td>$rs->deve_dni</td>"
                            . "<td>$rs->deve_alumno</td>"
                            . "<td>$rs->deve_concepto</td>"
                            . "<td>$rs->deve_serie_ticke</td>"
                            . "<td>$rs->deve_dscto</td>"
                            . "<td>$rs->deve_base_imp</td>"
                            . "<td>$rs->deve_igv</td>"
                            . "<td>$rs->deve_cuota</td>"
                            . "<td>$rs->deve_monto</td>"
                            . "<td>$rs->deve_monto_cancelado</td>"
                            . "<td>$rs->deve_tc</td>"
                            . "<td>$rs->deve_tipo</td>"
                            . "<td>$rs->deve_centro</td>"
                            . "<td>$rs->deve_estado_tipo</td>"
                            . "<td>$rs->deve_banco</td>"
                            . "<td>$rs->deve_detalle</td>"
                            . "</tr>";
                    $aux++;
                    $deve_num = $rs->deve_num;
                }

                $modal .= "</tbody></table>";
            }
            if (count($data) != 0) {
                $button = "<b>Se presentan errores en uno o mas registros, corrijalos antes de proceder con la carga</b>";
            }
            if ($error == 1) {
                $error_mensaje = "<b>Se han encontrado fallas en el correlativo, corrija el excel antes de proceder con la inserci&oacute;n de carga.</b>";
            }

            $modal .= "</div></div>"
                    . "<div class='row' >"
                    . "<div class='col-12'style='margin-top:15px;'>$error_mensaje</div>"
                    . "<div class='col-12'style='margin-top:15px;'id='modal_subir_devengados'>"
                    . "$button"
                    . "</div>"
                    . "</div>";
            if (count($data) != 0) {//si encontro errores respecto al id
                $html = "<thead>"
                        . "<th>ACCION</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "</thead><tbody>";
                foreach ($data as $rs) {//lista de errores si serie erronea
                    $html .= "<tr id='$rs->id_devengado'>"
                            . "<td><button class='btn btn-primary' onclick='modal_devengadoAlexia($rs->id_devengado)'><i class='fas fa-search-plus'></i></button></td>"
                            . "<td>$rs->deve_fecha_emicar</td>"
                            . "<td>$rs->deve_fecha_venc</td>"
                            . "<td>$rs->deve_fecha_pag</td>"
                            . "<td>$rs->deve_fecha</td>"
                            . "<td>$rs->deve_grado</td>"
                            . "<td>$rs->deve_boleta</td>"
                            . "<td>$rs->deve_dni</td>"
                            . "<td>$rs->deve_alumno</td>"
                            . "<td>$rs->deve_concepto</td>"
                            . "<td>$rs->deve_serie_ticke</td>"
                            . "<td>$rs->deve_dscto</td>"
                            . "<td>$rs->deve_base_imp</td>"
                            . "<td>$rs->deve_igv</td>"
                            . "<td>$rs->deve_cuota</td>"
                            . "<td>$rs->deve_monto</td>"
                            . "<td>$rs->deve_monto_cancelado</td>"
                            . "<td>$rs->deve_tc</td>"
                            . "<td>$rs->deve_tipo</td>"
                            . "<td>$rs->deve_centro</td>"
                            . "<td>$rs->deve_estado_tipo</td>"
                            . "<td>$rs->deve_banco</td>"
                            . "</tr>";
                }
                $html .= "</tbody>";
            }
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        //'html' => Devengados::selectDevengados($id_grupo)
                        'html' => $html,
                        'modal' => $modal
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function refresh_modal_devengadosAlexia() {
        $per_id = auth()->user()->id;
        $table_name = "tmp_tb_devengado_" . $per_id;
        $data_modal = Devengados::selectDevengados_tmp($table_name);
        $modal = "<table class='table table-hover table-sm'>";
        $modal .= "<table class='table table-hover table-sm'><thead>"
                . "<th>F. EMISION CARGO</th>"
                . "<th>F. VENC.</th>"
                . "<th>F. PAGO</th>"
                . "<th>F. EMISION DOCUMENTO</th>"
                . "<th>MATRICULA</th>"
                . "<th>BOLETA</th>"
                . "<th>RUC/DNI</th>"
                . "<th>CLIENTE</th>"
                . "<th>CONCEPTO</th>"
                . "<th>SERIE TICKETER</th>"
                . "<th>DSCTO</th>"
                . "<th>BASE IMP.</th>"
                . "<th>IGV</th>"
                . "<th>CUOTA</th>"
                . "<th>TOTAL</th>"
                . "<th>CANCELADO</th>"
                . "<th>T.C</th>"
                . "<th>TIPO</th>"
                . "<th>CENTRO</th>"
                . "<th>ESTADO</th>"
                . "<th>BANCO</th>"
                . "<th>DETALLE</th>"
                . "</thead><tbody>";
        $indice = 1;
        $id_serie = 0;
        $deve_num = 0;
        $i = 1;
        $color = "";
        foreach ($data_modal as $rs) {
            $color = "";
            if ($rs->id_serie != $id_serie) {//cambio de serie
                $id_serie = $rs->id_serie;
                $deve_num = 0;
            } else {//misma serie, empieza correlativo
                if (($rs->deve_num - $deve_num) != 1) {//error en correlativo
                    $error = 1;
                    $correlativo = $deve_num + 1;
                    $color = "color:red;";
                    $index = $rs->deve_num - $correlativo;
                    for ($i = 0; $i < $index; $i++) {
                        $modal .= "<tr style='$color'><td colspan='22'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $rs->serie_desc-$correlativo</td></tr>";
                        //$deve_num = $deve_num + 1;
                        $correlativo++;
                    }
                }
                $color = "";
            }
            if ($rs->deve_detalle == "ENCONTRADO") {
                $color = "color:red";
            }
            $modal .= "<tr style='$color' id='$rs->id_devengado'>"
                    . "<td>$rs->deve_fecha_emicar</td>"
                    . "<td>$rs->deve_fecha_venc</td>"
                    . "<td>$rs->deve_fecha_pag</td>"
                    . "<td>$rs->deve_fecha</td>"
                    . "<td>$rs->deve_grado</td>"
                    . "<td>$rs->deve_boleta</td>"
                    . "<td>$rs->deve_dni</td>"
                    . "<td>$rs->deve_alumno</td>"
                    . "<td>$rs->deve_concepto</td>"
                    . "<td>$rs->deve_serie_ticke</td>"
                    . "<td>$rs->deve_dscto</td>"
                    . "<td>$rs->deve_base_imp</td>"
                    . "<td>$rs->deve_igv</td>"
                    . "<td>$rs->deve_cuota</td>"
                    . "<td>$rs->deve_monto</td>"
                    . "<td>$rs->deve_monto_cancelado</td>"
                    . "<td>$rs->deve_tc</td>"
                    . "<td>$rs->deve_tipo</td>"
                    . "<td>$rs->deve_centro</td>"
                    . "<td>$rs->deve_estado_tipo</td>"
                    . "<td>$rs->deve_banco</td>"
                    . "<td>$rs->deve_detalle</td>"
                    . "</tr>";
            $deve_num = $rs->deve_num; //importante muere sino
        }
        $modal .= "</tbody></table>";
        return response()->json([
                    'modal' => $modal
        ]);
    }

    public function load_devengadoAlexia_modal(Request $request) {
        $per_id = auth()->user()->id;
        $table_name = "tmp_tb_devengado_" . $per_id;
        $data = Devengados::load_devengado_tmp_modal($table_name, $request->input('id_devengado'));
        $html['head'] = "Actualizacion Devengado";
        $html['body'] = "
<div class = 'form-group row'>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Emision Cargo</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='deve_fecha_emicar' value='" . $data->deve_fecha_emicar . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Venc.</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='deve_fecha_venc' value='" . $data->deve_fecha_venc . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Pago</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='deve_fecha' value='" . $data->deve_fecha_pag . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Emision Documento</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='deve_emision' value='" . $data->deve_fecha . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Matricula</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_grado' value='" . $data->deve_grado . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Boleta</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_boleta' value='" . $data->deve_boleta . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>RUC / DNI</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_dni' value='" . $data->deve_dni . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Cliente</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_alumno' value='" . $data->deve_alumno . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Concepto</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_concepto' value='" . $data->deve_concepto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Serie Ticketera</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_serie_ticke' value='" . $data->deve_serie_ticke . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Dscto</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_dscto' value='" . $data->deve_dscto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Base Imp.</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_base_imp' value='" . $data->deve_base_imp . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>IGV</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_igv' value='" . $data->deve_igv . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Total</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_monto' value='" . $data->deve_monto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Cancelado</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_monto_cancelado' value='" . $data->deve_monto_cancelado . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>T. C.</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_tc' value='" . $data->deve_tc . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Tipo</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_tipo' value='" . $data->deve_tipo . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Centro</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_centro' value='" . $data->deve_centro . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Estado</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_estado_tipo' value='" . $data->deve_estado_tipo . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Banco</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='deve_banco' value='" . $data->deve_banco . "'>
        </div>
    </div>
</div>

<div class = 'form-group row'>
    <div class='col-12'>
        <button id='update_pago' class='btn btn-primary'  style='float:right' onclick='update_devengadoAlexia(" . $data->id_devengado . ")'>Actualizar</button>
    </div>
</div>    
            ";
        $html['footer'] = "";
        return json_encode($html);
    }

    public function update_devengadoAlexia(Request $request) {
        $data = array();
        /* $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
          'id_devengado' => 'required',
          'deve_emision' => 'required|date',
          'deve_dni' => 'required',
          'deve_boleta' => 'required',
          'deve_alumno' => 'required',
          'deve_cuota' => 'required',
          'deve_monto' => 'required|numeric',
          ]);

          if ($validator->fails()) {
          $data['mensaje'] = 'por favor,complete todos los campos correctamente';
          $data['tipo'] = 2;
          return json_encode($data);
          } */

        $deve_boleta_arr = explode("-", $request->input('deve_boleta'));

        $id_serie = Devengados::loadSerie_desc($deve_boleta_arr[0]);
        if ($id_serie == null) {
            $data['mensaje'] = "la serie que se ha digitado, no se ha podido encontrar";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $valida_deve_boleta = Devengados::load_deve_boleta($request->input('deve_boleta'));
        if (count((array) $valida_deve_boleta) > 0) {
            $data['mensaje'] = "se ha encontrado un registro con el mismo codigo";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $deve_fecha_emicar = $request->input("deve_fecha_emicar");
        $deve_fecha_venc = $request->input("deve_fecha_venc");
        $deve_fecha_pag = $request->input("deve_fecha_pag");
        $deve_fecha = $request->input("deve_fecha");
        $deve_grado = $request->input("deve_grado");
        $deve_boleta = $request->input("deve_boleta");
        $deve_dni = $request->input("deve_dni");
        $deve_alumno = $request->input("deve_alumno");
        $deve_concepto = $request->input("deve_concepto");
        $deve_serie_ticke = $request->input("deve_serie_ticke");
        $deve_dscto = $request->input("deve_dscto");
        $deve_base_imp = $request->input("deve_base_imp");
        $deve_igv = $request->input("deve_igv");
        $deve_monto = $request->input("deve_monto");
        $deve_monto_cancelado = $request->input("deve_monto_cancelado");
        $deve_tc = $request->input("deve_tc");
        $deve_tipo = $request->input("deve_tipo");
        $deve_centro = $request->input("deve_centro");
        $deve_estado_tipo = $request->input("deve_estado_tipo");
        $deve_banco = $request->input("deve_banco");
        $id_serie = $id_serie->id_serie;
        $deve_num = (int) $deve_boleta_arr[1];
        $tmp_table = "tmp_tb_devengado_" . auth()->user()->id;
        $data['mensaje'] = "Registro actualizado correctamente";
        $data['tipo'] = Devengados::update_devengado_tmp([
                    "deve_fecha_emicar" => "$deve_fecha_emicar",
                    "deve_fecha_venc" => "$deve_fecha_venc",
                    "deve_fecha_pag" => "$deve_fecha_pag",
                    "deve_fecha" => "$deve_fecha",
                    "deve_grado" => "$deve_grado",
                    "id_serie" => $id_serie,
                    "deve_num" => "$deve_num",
                    "deve_boleta" => "$deve_boleta",
                    "deve_dni" => "$deve_dni",
                    "deve_alumno" => "$deve_alumno",
                    "deve_concepto" => "$deve_concepto",
                    "deve_serie_ticke" => "$deve_serie_ticke",
                    "deve_dscto" => "$deve_dscto",
                    "deve_base_imp" => "$deve_base_imp",
                    "deve_igv" => "$deve_igv",
                    "deve_monto" => "$deve_monto",
                    "deve_monto_cancelado" => "$deve_monto_cancelado",
                    "deve_tc" => "$deve_tc",
                    "deve_tipo" => "$deve_tipo",
                    "deve_centro" => "$deve_centro",
                    "deve_estado_tipo" => "$deve_estado_tipo",
                    "deve_banco" => "$deve_banco",
                        ], $request->input("id_devengado"), $tmp_table);

        return json_encode($data);
    }

    function upload_devengadoAlexia() {
        $table_name = "tmp_tb_devengado_" . auth()->user()->id;
        Devengados::upload_devengadoAlexia($table_name);
        return 1;
    }

    /*     * ************************ CARGA PAGOS ************************ */

    function upload_pagos(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel_pagos' => 'required|mimes:xlsx,xls|max:50000'
        ]);
        if ($validacion->passes()) {
//traigo id sesion
            $per_id = auth()->user()->id;
//creo y retorno un grupo
            $id_grupo = Devengados::insertGrupoPago([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
//traigo todas las series
            $serie = Devengados::loadSerie();
//importo el excel
            $excel = $request->file('excel_pagos')->store('tmp');
            $import = new PagosImport($id_grupo, $serie);
            Excel::import($import, $excel);
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        'html' => Devengados::selectPagos($id_grupo),
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function load_pago_modal(Request $request) {
        $data = Devengados::load_pago_modal($request->input('id_pago'));
        $html['head'] = "Actualizacion Pago";
        $html['body'] = "
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Grado</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_grado' value='" . $data->pago_grado . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Fecha de Emisi&oacute;n</label>
    <div class='col-sm-7 col-12'>
        <input type='date' class='form-control' id='pago_emision' value='" . $data->pago_emision . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>DNI</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_dni' value='" . $data->pago_dni . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>N. Boleta</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_boleta' value='" . $data->pago_boleta . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Apellidos y Nombres</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_alumno' value='" . $data->pago_alumno . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Cuota</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_cuota' value='" . $data->pago_cuota . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Monto</label>
    <div class='col-sm-7 col-12'>
        <input type='text' class='form-control' id='pago_monto' value='" . $data->pago_monto . "'>
    </div>
</div>
<div class = 'form-group row'>
    <label class='col-form-label col-sm-5 col-12'>Fecha de Pago</label>
    <div class='col-sm-7 col-12'>
        <input type='date' class='form-control' id='pago_fecha' value='" . $data->pago_fecha . "'>
    </div>
</div> 
<div class = 'form-group row'>
    <div class='col-12'>
        <button id='update_pago' class='btn btn-primary'  style='float:right' onclick='update_pago(" . $data->id_pago . ")'>Actualizar</button>
    </div>
</div>    
            ";
        $html['footer'] = "";
        return json_encode($html);
    }

    function update_pago(Request $request) {
        $data = array();
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'id_pago' => 'required',
                    'pago_emision' => 'required|date',
                    'pago_dni' => 'required',
                    'pago_boleta' => 'required',
                    'pago_alumno' => 'required',
                    'pago_cuota' => 'required',
                    'pago_monto' => 'required|numeric',
                    'pago_fecha' => 'required|date',
        ]);
        if ($validator->fails()) {
            //return $validator->messages();
            $data['mensaje'] = "por favor, complete todos los campos correctamente";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $pago_boleta = explode("-", $request->input('pago_boleta'));
        $id_serie = Devengados::loadSerie_desc($pago_boleta[0]);
        if ($id_serie == null) {
            $data['mensaje'] = "la serie que se ha digitado, no se ha podido encontrar";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $valida_pago_boleta = Devengados::load_pago_boleta($request->input('pago_boleta'));
        if (count((array) $valida_pago_boleta) > 0) {
            $data['mensaje'] = "se ha encontrado un registro con el mismo codigo";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $pago_grado = $request->input("pago_grado");
        $pago_emision = $request->input("pago_emision");
        $pago_dni = $request->input("pago_dni");
        $id_serie = $id_serie->id_serie;
        $pago_num = (int) $pago_boleta[1];
        $pago_boleta = $request->input("pago_boleta");
        $pago_alumno = $request->input("pago_alumno");
        $pago_cuota = $request->input("pago_cuota");
        $pago_monto = $request->input("pago_monto");
        $pago_fecha = $request->input("pago_fecha");
        Devengados::update_pago([
            "pago_grado" => "$pago_grado",
            "pago_emision" => "$pago_emision",
            "pago_dni" => "$pago_dni",
            "id_serie" => "$id_serie",
            "pago_num" => "$pago_num",
            "pago_boleta" => "$pago_boleta",
            "pago_alumno" => "$pago_alumno",
            "pago_cuota" => "$pago_cuota",
            "pago_monto" => "$pago_monto",
            "pago_fecha" => "$pago_fecha"
                ], $request->input("id_pago"));
        Devengados::update_pago_devengado($pago_boleta);
        $data['tipo'] = 1;
        $data['mensaje'] = "Registro actualizado correctamente";
        return json_encode($data);
    }

    /*     * ***************** CARGA PAGOS ALEXIA *********************** */

    function upload_pagosAlexia(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel_pagos' => 'required|mimes:xlsx,xls|max:50000000'
        ]);
        if ($validacion->passes()) {
//traigo id sesion
            $per_id = auth()->user()->id;
//nombre de la tabla temporal
            $table_name = "tmp_tb_pagos_" . $per_id;
//creo y retorno un grupo
            $id_grupo = Devengados::insertGrupoPago([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
//traigo todas las series
            $serie = Devengados::loadSerie();
//importo el excel
            $excel = $request->file('excel_pagos')->store('tmp_pago');
//creo tabla temporal
            Devengados::uploadPagosAlexia_tmp_create($table_name);
//importo data a la temporal
            $import = new AlexiaPagos_import($id_grupo, $serie, $table_name);
            Excel::import($import, $excel);
            $import_all_pagos = new PagosAllImport($serie, "tb_lista_pago");
            Excel::import($import_all_pagos, $excel);
            //$data = Devengados::selectPagos($id_grupo);
//cargo data de la temporal            
            $data_modal = Devengados::selectPagos_tmp($table_name);
//cargo data erronea de la temporal              
            $data = Devengados::selectPagos_tmp_error($table_name);
            $html = "";
            //Jesus M;
            $modal = "<div class='container-fluid'><div class='row'>"
                    . "<div class='col-xs-12 text-right' style='padding-bottom:10px;width:100%;'>"
                    . "<button onclick='reload_modaltmppago()' class='btn'><i class='fa fa-sync'></i></button>"
                    . "</div></div></div>"
                    . "<div class='row'><div class='table-responsive text-nowrap' id='modal_pagos_tmp' style='max-height:500px;overflow:auto;'>";
            if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
                $modal .= "<table class='table table-striped'><thead>"
                        . "<th>Nro.</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>CUOTA</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "<th>DETALLE</th>"
                        . "</thead><tbody>";
                $id_serie = 0;
                $deve_num = 0;
                $i = 1;
                //Jesus M
                $cant_detalle = "";
                if (count($data_modal) == 1) {
                    $cant_detalle = "registro";
                } else {
                    $cant_detalle = "registros";
                }
                $button = "<div class='row'>"
                        . "<div class='col-3'>"
                        . "<button class='btn btn-primary' id='subir_pagos_tmp' onclick='subir_pagos_tmp()'>Subir Pagos</button>"
                        . "</div>"
                        . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
                //Jesus M;
                $error = 0;
                $error_mensaje = "";
                $indice = 1;
                $id_serie = 0;
                $pago_num = 0;
                foreach ($data_modal as $rs) {//lista de carga excel
                    $color = "";
                    /*if ($rs->id_serie != $id_serie) {//cambio de serie
                        $id_serie = $rs->id_serie;
                        $pago_num = 0;
                    } else {//misma serie, empieza correlativo
                        if (($rs->pago_num - $pago_num) != 1) {//error en correlativo
                            $error = 1;
                            $correlativo = $pago_num + 1;
                            $color = "color:red;font-weight: bold;";
                            $index = $rs->pago_num - $correlativo;
                            for ($i = 0; $i < $index; $i++) {
                                $modal .= "<tr style='$color'><td colspan='22'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $rs->serie_desc-$correlativo</td></tr>";
                                $correlativo++;
                            }
                        }
                        $color = "";
                    }*/

                    if ($rs->pago_detalle == "ENCONTRADO") {//pago ya insertado
                        $color = "color:red;";
                    } else if ($rs->detalle == "ENCONTRADO") {//encontro devengado
                        $color = "color:blue;";
                    }
                    $modal .= "<tr style='$color' id='$rs->id_pago'>"
                            . "<td>$i</td>"
                            . "<td>$rs->pago_fecha_emicar</td>"
                            . "<td>$rs->pago_fecha_venc</td>"
                            . "<td>$rs->pago_fecha</td>"
                            . "<td>$rs->pago_emision</td>"
                            . "<td>$rs->pago_grado</td>"
                            . "<td>$rs->pago_boleta</td>"
                            . "<td>$rs->pago_dni</td>"
                            . "<td>$rs->pago_alumno</td>"
                            . "<td>$rs->pago_concepto</td>"
                            . "<td>$rs->pago_serie_ticke</td>"
                            . "<td>$rs->pago_dscto</td>"
                            . "<td>$rs->pago_base_imp</td>"
                            . "<td>$rs->pago_igv</td>"
                            . "<td>$rs->pago_cuota</td>"
                            . "<td>$rs->pago_monto</td>"
                            . "<td>$rs->pago_monto_cancelado</td>"
                            . "<td>$rs->pago_tc</td>"
                            . "<td>$rs->pago_tipo</td>"
                            . "<td>$rs->pago_centro</td>"
                            . "<td>$rs->pago_estado_tipo</td>"
                            . "<td>$rs->pago_banco</td>"
                            . "<td>$rs->pago_detalle</td>"
                            . "</tr>";
                    $i++;
                    //$deve_num = $rs->deve_num;
                }

                $modal .= "</tbody></table>";
            }
            if (count($data) != 0) {
                $button = "<b>Se presentan errores en uno o mas registros, corrijalos antes de proceder con la carga</b>";
            }
            if ($error == 1) {
                $error_mensaje = "<b>Se han encontrado fallas en el correlativo, corrija el excel antes de proceder con la inserci&oacute;n de carga.</b>";
            }
            $modal .= "</div></div>"
                    . "<div class='row' >"
                    . "<div class='col-12'style='margin-top:15px;'>$error_mensaje</div>"
                    . "<div class='col-12'style='margin-top:15px;'id='modal_subir_pagos'>"
                    . "$button"
                    . "</div>"
                    . "</div>";
            if (count($data) != 0) {
                $html = "<thead>"
                        . "<th>ACCION</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "</thead><tbody>";
                foreach ($data as $rs) {
                    $html .= "<tr id='$rs->id_pago'>"
                            . "<td><button class='btn btn-primary' onclick='modal_pagoAlexia($rs->id_pago)'><i class='fas fa-search-plus'></i></button></td>"
                            . "<td>$rs->pago_fecha_emicar</td>"
                            . "<td>$rs->pago_fecha_venc</td>"
                            . "<td>$rs->pago_fecha</td>"
                            . "<td>$rs->pago_emision</td>"
                            . "<td>$rs->pago_grado</td>"
                            . "<td>$rs->pago_boleta</td>"
                            . "<td>$rs->pago_dni</td>"
                            . "<td>$rs->pago_alumno</td>"
                            . "<td>$rs->pago_concepto</td>"
                            . "<td>$rs->pago_serie_ticke</td>"
                            . "<td>$rs->pago_dscto</td>"
                            . "<td>$rs->pago_base_imp</td>"
                            . "<td>$rs->pago_igv</td>"
                            . "<td>$rs->pago_cuota</td>"
                            . "<td>$rs->pago_monto</td>"
                            . "<td>$rs->pago_monto_cancelado</td>"
                            . "<td>$rs->pago_tc</td>"
                            . "<td>$rs->pago_tipo</td>"
                            . "<td>$rs->pago_centro</td>"
                            . "<td>$rs->pago_estado_tipo</td>"
                            . "<td>$rs->pago_banco</td>"
                            . "</tr>";
                }
                $html .= "</tbody>";
            }
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        //'html' => Devengados::selectPagos($id_grupo)
                        'html' => $html,
                        'modal' => $modal
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function load_pago_modalAlexia(Request $request) {
        //$data = Devengados::load_pago_modal($request->input('id_pago'));
        $table_name = "tmp_tb_pagos_" . auth()->user()->id;
        $data = Devengados::load_pago_tmp_modal($request->input('id_pago'), $table_name);
        $html['head'] = "Actualizacion Pago";
        $html['body'] = "
<div class = 'form-group row'>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Emision Cargo</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='pago_fecha_emicar' value='" . $data->pago_fecha_emicar . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Venc.</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='pago_fecha_venc' value='" . $data->pago_fecha_venc . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Pago</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='pago_fecha' value='" . $data->pago_fecha . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>F. Emision Documento</label>
        <div class='col-12'>
            <input type='date' class='form-control' id='pago_emision' value='" . $data->pago_emision . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Matricula</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_grado' value='" . $data->pago_grado . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Boleta</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_boleta' value='" . $data->pago_boleta . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>RUC / DNI</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_dni' value='" . $data->pago_dni . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Cliente</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_alumno' value='" . $data->pago_alumno . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Concepto</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_concepto' value='" . $data->pago_concepto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Serie Ticketera</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_serie_ticke' value='" . $data->pago_serie_ticke . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Dscto</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_dscto' value='" . $data->pago_dscto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Base Imp.</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_base_imp' value='" . $data->pago_base_imp . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>IGV</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_igv' value='" . $data->pago_igv . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Total</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_monto' value='" . $data->pago_monto . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Cancelado</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_monto_cancelado' value='" . $data->pago_monto_cancelado . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>T. C.</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_tc' value='" . $data->pago_tc . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Tipo</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_tipo' value='" . $data->pago_tipo . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Centro</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_centro' value='" . $data->pago_centro . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Estado</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_estado_tipo' value='" . $data->pago_estado_tipo . "'>
        </div>
    </div>
    <div class = 'col-md-4 col-sm-6 col-12'>
        <label class='col-form-label col-12'>Banco</label>
        <div class='col-12'>
            <input type='text' class='form-control' id='pago_banco' value='" . $data->pago_banco . "'>
        </div>
    </div>
</div>

<div class = 'form-group row'>
    <div class='col-12'>
        <button id='update_pago' class='btn btn-primary'  style='float:right' onclick='update_pagoAlexia(" . $data->id_pago . ")'>Actualizar</button>
    </div>
</div>    
            ";
        $html['footer'] = "";
        return json_encode($html);
    }

    function update_pagoAlexia(Request $request) {
        //dd($request->input());
        $data = array();
        /* $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
          'id_pago' => 'required',
          'pago_emision' => 'required|date',
          'pago_dni' => 'required',
          'pago_boleta' => 'required',
          'pago_alumno' => 'required',
          'pago_cuota' => 'required',
          'pago_monto' => 'required|numeric',
          'pago_fecha' => 'required|date',
          ]);
          if ($validator->fails()) {
          //return $validator->messages();
          $data['mensaje'] = "por favor, complete todos los campos correctamente";
          $data['tipo'] = 2;
          return json_encode($data);
          } */
        $pago_boleta_arr = explode("-", $request->input('pago_boleta'));
        $id_serie = Devengados::loadSerie_desc($pago_boleta_arr[0]);
        if ($id_serie == null) {
            $data['mensaje'] = "la serie que se ha digitado, no se ha podido encontrar";
            $data['tipo'] = 2;
            return json_encode($data);
        }
        $valida_pago_boleta = Devengados::load_pago_boleta($request->input('pago_boleta'));
        if (count((array) $valida_pago_boleta) > 0) {
            $data['mensaje'] = "se ha encontrado un registro con el mismo codigo";
            $data['tipo'] = 2;
            return json_encode($data);
        }

        $pago_fecha_emicar = $request->input("pago_fecha_emicar");
        $pago_fecha_venc = $request->input("pago_fecha_venc");
        $pago_fecha = $request->input("pago_fecha");
        $pago_emision = $request->input("pago_emision");
        $pago_grado = $request->input("pago_grado");
        $pago_boleta = $request->input("pago_boleta");
        $pago_dni = $request->input("pago_dni");
        $pago_alumno = $request->input("pago_alumno");
        $pago_concepto = $request->input("pago_concepto");
        $pago_serie_ticke = $request->input("pago_serie_ticke");
        $pago_dscto = $request->input("pago_dscto");
        $pago_base_imp = $request->input("pago_base_imp");
        $pago_igv = $request->input("pago_igv");
        $pago_monto = $request->input("pago_monto");
        $pago_monto_cancelado = $request->input("pago_monto_cancelado");
        $pago_tc = $request->input("pago_tc");
        $pago_tipo = $request->input("pago_tipo");
        $pago_centro = $request->input("pago_centro");
        $pago_estado_tipo = $request->input("pago_estado_tipo");
        $pago_banco = $request->input("pago_banco");
        $pago_num = (int) $pago_boleta_arr[1];
        //
        /* $pago_grado = $request->input("pago_grado");
          $pago_emision = $request->input("pago_emision");
          $pago_dni = $request->input("pago_dni");
          $id_serie = $id_serie->id_serie;
          $pago_num = (int) $pago_boleta_arr[1];
          $pago_boleta = $request->input("pago_boleta");
          $pago_alumno = $request->input("pago_alumno");
          $pago_cuota = $request->input("pago_cuota");
          $pago_monto = $request->input("pago_monto");
          $pago_fecha = $request->input("pago_fecha"); */
        $tmp_table = "tmp_tb_pagos_" . auth()->user()->id;
        //Devengados::update_pago([
        Devengados::update_pago_tmp([
            "pago_fecha_emicar" => "$pago_fecha_emicar",
            "pago_fecha_venc" => "$pago_fecha_venc",
            "pago_fecha" => "$pago_fecha",
            "pago_emision" => "$pago_emision",
            "pago_grado" => "$pago_grado",
            "id_serie" => $id_serie->id_serie,
            "pago_num" => "$pago_num",
            "pago_boleta" => "$pago_boleta",
            "pago_dni" => "$pago_dni",
            "pago_alumno" => "$pago_alumno",
            "pago_concepto" => "$pago_concepto",
            "pago_serie_ticke" => "$pago_serie_ticke",
            "pago_dscto" => "$pago_dscto",
            "pago_base_imp" => "$pago_base_imp",
            "pago_igv" => "$pago_igv",
            "pago_monto" => "$pago_monto",
            "pago_monto_cancelado" => "$pago_monto_cancelado",
            "pago_tc" => "$pago_tc",
            "pago_tipo" => "$pago_tipo",
            "pago_centro" => "$pago_centro",
            "pago_estado_tipo" => "$pago_estado_tipo",
            "pago_banco" => "$pago_banco",
                ], $request->input("id_pago"), $tmp_table);
        //Devengados::update_pago_devengado($pago_boleta);
        $data['tipo'] = 1;
        $data['mensaje'] = "Registro actualizado correctamente";
        return json_encode($data);
    }

    function refresh_modal_pagosAlexia() {
        $per_id = auth()->user()->id;
        $table_name = "tmp_tb_pagos_" . $per_id;
        $data_modal = Devengados::selectPagos_tmp($table_name);
        $modal = "";
        //Jesus M
        if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
            $modal .= "<table class='table table-striped'><thead>"
                    . "<th>Nro.</th>"
                    . "<th>F. EMISION CARGO</th>"
                    . "<th>F. VENC.</th>"
                    . "<th>F. PAGO</th>"
                    . "<th>F. EMISION DOCUMENTO</th>"
                    . "<th>MATRICULA</th>"
                    . "<th>BOLETA</th>"
                    . "<th>RUC/DNI</th>"
                    . "<th>CLIENTE</th>"
                    . "<th>CONCEPTO</th>"
                    . "<th>SERIE TICKETER</th>"
                    . "<th>DSCTO</th>"
                    . "<th>BASE IMP.</th>"
                    . "<th>IGV</th>"
                    . "<th>CUOTA</th>"
                    . "<th>TOTAL</th>"
                    . "<th>CANCELADO</th>"
                    . "<th>T.C</th>"
                    . "<th>TIPO</th>"
                    . "<th>CENTRO</th>"
                    . "<th>ESTADO</th>"
                    . "<th>BANCO</th>"
                    . "<th>DETALLE</th>"
                    . "</thead><tbody>";
            $id_serie = 0;
            $deve_num = 0;
            $i = 1;
            //Jesus M
            $cant_detalle = "";
            if (count($data_modal) == 1) {
                $cant_detalle = "registro";
            } else {
                $cant_detalle = "registros";
            }
            $button = "<div class='row'>"
                    . "<div class='col-3'>"
                    . "<button class='btn btn-primary' id='subir_pagos_tmp' onclick='subir_pagos_tmp()'>Subir Pagos</button>"
                    . "</div>"
                    . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
            $error = 0;
            $error_mensaje = "";
            $indice = 1;
            foreach ($data_modal as $rs) {//lista de carga excel
                $color = "";
                if ($rs->detalle == "ENCONTRADO") {//encontro devengado
                    $color = "color:blue;";
                } else if ($rs->pago_detalle == "ENCONTRADO") {
                    $color = "color:red";
                }
                $modal .= "<tr style='$color' id='$rs->id_pago'>"
                        . "<td>$i</td>"
                        . "<td>$rs->pago_fecha_emicar</td>"
                        . "<td>$rs->pago_fecha_venc</td>"
                        . "<td>$rs->pago_fecha</td>"
                        . "<td>$rs->pago_emision</td>"
                        . "<td>$rs->pago_grado</td>"
                        . "<td>$rs->pago_boleta</td>"
                        . "<td>$rs->pago_dni</td>"
                        . "<td>$rs->pago_alumno</td>"
                        . "<td>$rs->pago_concepto</td>"
                        . "<td>$rs->pago_serie_ticke</td>"
                        . "<td>$rs->pago_dscto</td>"
                        . "<td>$rs->pago_base_imp</td>"
                        . "<td>$rs->pago_igv</td>"
                        . "<td>$rs->pago_cuota</td>"
                        . "<td>$rs->pago_monto</td>"
                        . "<td>$rs->pago_monto_cancelado</td>"
                        . "<td>$rs->pago_tc</td>"
                        . "<td>$rs->pago_tipo</td>"
                        . "<td>$rs->pago_centro</td>"
                        . "<td>$rs->pago_estado_tipo</td>"
                        . "<td>$rs->pago_banco</td>"
                        . "<td>$rs->pago_detalle</td>"
                        . "</tr>";
                $i++;
                //$deve_num = $rs->deve_num;
            }

            $modal .= "</tbody></table>";
        }
        return response()->json([
                    'modal' => $modal
        ]);
    }

    function upload_pagoAlexia() {
        $table_name = "tmp_tb_pagos_" . auth()->user()->id;
        Devengados::upload_pagoAlexia($table_name);
        return 1;
    }

    /*     * ***************** ADMINISTRACION ******************* */

    public function lista_grupo_devengado() {
        $data = Devengados::lista_grupo_devengado();
        if (count($data) > 0) {
            $html = "<div class='table-responsive'>"
                    . "<table class='table table-hover table-sm' >"
                    . "<thead>"
                    . "<tr>"
                    . "<th>Nro.</th>"
                    . "<th>Fecha Carga</th>"
                    . "<th>Empleado Carga</th>"
                    . "<th>Estado</th>"
                    . "<th>Accion</th>"
                    . "</tr>"
                    . "</thead>"
                    . "<tbody>";
            $i = 1;
            foreach ($data as $row) {
                $detalle = "carga correcta";
                $color = "";
                $button = "";
                if (($row->cant_total) > ($row->cant_sin_serie)) {
                    $we = ($row->cant_total) - ($row->cant_sin_serie);
                    $detalle = "$we errores detectados";
                    $color = "color:red";
                    $button = "<button class='btn btn-danger' onclick=delete_devengados($row->id_grupo)><i class='far fa-trash-alt'></i></button>";
                } else if ($row->cant_total == 0) {
                    $detalle = "no hay registros";
                    $color = "color:red";
                    $button = "<button class='btn btn-danger' onclick=delete_devengados($row->id_grupo)><i class='far fa-trash-alt'></i></button>";
                }
                $html .= "<tr style='$color'>"
                        . "<td>$i</td>"
                        . "<td>$row->grupo_fecha</td>"
                        . "<td>$row->nombre</td>"
                        . "<td>$detalle</td>"
                        . "<td>"
                        . "<button class='btn btn-primary' onclick=load_devengados_modal($row->id_grupo)><i class='fas fa-search-plus'></i></button>"
                        . $button
                        . "</td>"
                        . "</tr>";
                $i++;
            }
            $html .= "</tbody>"
                    . "</table>"
                    . "</div>";
        } else {
            $html = "<b>Sin registros...</b>";
        }

        return $html;
    }

    public function load_modal_devengado(Request $request) {
        $id_grupo = $request->input("id_grupo");
        $data = Devengados::lista_devengado($id_grupo);
        $html['head'] = "Lista de Alumnos Devengados";
        //Jesus M
        $html['body'] = "<div class='row'><label class='col-sm-6 col-12 text-sm-right text-left'>Buscar:</label>"
                . "<div class=' col-sm-6 col-12'>"
                . "<input type='text' id='modal_text' class='form-control' onkeyup=doSearch('modal_table','modal_text') />"
                . "</div></div>"
                . "<div class='row'style='margin-top:15px;'><div class='table-responsive text-nowrap' style = 'max-height:500px;overflow:auto;'>"
                . " <table class = 'table table-hover table-sm' id='modal_table'>"
                . " <thead>"
                . " <tr>"
                . " <th id='tlmd1' class='sort'>Nro.</th>"
                . " <th id='tlmd2' class='sort'>GRADO</th>"
                . " <th id='tlmd3' class='sort'>FECHA DE EMISI&Oacute;N</th>"
                . " <th id='tlmd4' class='sort'>DNI</th>"
                . " <th id='tlmd5' class='sort'>Nro. BOLETA</th>"
                . " <th id='tlmd6' class='sort'>APELLIDOS Y NOMBRES</th>"
                . " <th id='tlmd7' class='sort'>CUOTA</th>"
                . " <th id='tlmd8' class='sort'>MONTO TOTAL</th>"
                . " <th id='tlmd9' class='sort'>MONTO RESTANTE</th>"
                . " <th id='tlmd10' class='sort'>ESTADO</th>"
                . " <th id='tlmd11' class='sort'>ACCION</th>"
                . " </tr>"
                . " </thead>"
                . " <tbody>";
        $i = 1;
        $aux = 1;
        $ca = 0;
        $cp = 0;
        $deve_num = 0;
        $id_serie = "";
        foreach ($data as $rs) {
            //$monto_total = ($rs->deve_monto - $rs->deve_pago) - $rs->deve_pago_anulado;
            $monto_total = '';
            $button = "<button class='btn btn-danger' onclick='mostrarModalDetalleAnulacion($rs->id_devengado,$rs->deve_monto," . '"' . $rs->deve_boleta . '"' . ",$id_grupo)' title='Anular Pago'><i class='far fa-trash-alt'></i></button>";
            $color = "";
            $mensaje = "";

            if ($id_grupo != 1) {
                if ($rs->id_serie != $id_serie) {//cambio de serie
                    $id_serie = $rs->id_serie;
                    $deve_num = 0;
                } else {//misma serie, empieza correlativo
                    if (($rs->deve_num - $deve_num) != 1) {//error en correlativo
                        $error = 1;
                        $correlativo = $deve_num + 1;
                        $color = "color:red;";
                        $index = $rs->deve_num - $correlativo;
                        for ($i = 0; $i < $index; $i++) {
                            $html['body'] .= "<tr style='$color'><td colspan='10'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $rs->serie_desc-$correlativo</td></tr>";
                            //$deve_num = $deve_num + 1;
                            $correlativo++;
                        }
                    }
                    $color = "";
                }
            }
            if ($rs->deve_estado == "0") {//anulado
                $mensaje = $rs->mensaje;
                $monto_total = $rs->pago_anulado;
                /* if ($monto_total - $rs->deve_pago_anulado == 0) {
                  $mensaje = "Anulacion Total";
                  } else if ($monto_total - $rs->deve_pago_anulado >= 0) {
                  $mensaje = "Anulacion Parcial";
                  } else {
                  $mensaje = "Montos no cuadran";
                  } */
                $color = "color:red";
                //$deve_estado = "$mensaje <span class='pagoanulado'><i>i</i><p>$rs->comentario_anulacion Monto: $rs->deve_pago_anulado</p></span>";
                $deve_estado = $mensaje;
                $button = "<button class='btn btn-info' onclick='mostrarDetalleAnulacion($rs->nota)' title='Detalle Anulacion'><i class='fa fa-list-alt'></i></button>&nbsp;";
                $button .= "<button class='btn btn-primary' onclick='mostrarEditarAnulacion($rs->nota,$id_grupo,$rs->deve_monto)' title='Modificar'><i class='fa fa-edit'></i></button>&nbsp;";
                $button .= "<button class='btn btn-warning' onclick='mostrarEliminarAnulacion($rs->nota,$id_grupo)' title='Eliminar'><i class='fas fa-folder-minus'></i></button>";
            } else if (($rs->deve_estado == "1" && $rs->id_serie != null) || ($rs->deve_anio < 2017)) {//insertado
                $deve_estado = "Activo";
                $ca++;
            } else if ($rs->deve_estado == "2" && $rs->id_serie != null) {//pagado
                $monto_total = $rs->deve_monto - $rs->deve_pago;
                if ($monto_total == 0) {
                    $mensaje = "Cancelado";
                } else if ($monto_total > 0) {
                    $mensaje = "Montos no cuadran";
                } else {
                    $mensaje = "Monto de pago mayor a monto Devengado";
                }
                $color = "color:blue";
                $deve_estado = "$mensaje";
                $cp++;
            } else {
                $color = "color:red";
                $deve_estado = "Error";
            }

            $html['body'] .= "<tr style = '$color'>"
                    . " <td>$aux</td>"
                    . " <td>" . $rs->deve_grado . "</td>"
                    . " <td style = 'white-space:nowrap'>" . $rs->deve_fecha . "</td>"
                    . " <td>" . $rs->deve_dni . "</td>"
                    . " <td style = 'white-space:nowrap'>" . $rs->deve_boleta . "</td>"
                    . " <td>" . $rs->deve_alumno . "</td>"
                    . " <td>" . $rs->deve_cuota . "</td>"
                    . " <td align='center'>" . $rs->deve_monto . "</td>"
                    . " <td align='center'>" . $monto_total . "</td>"
                    . " <td>$deve_estado</td>"
                    . " <td align='center'>$button</td>"
                    . " </tr>";
            $deve_num = $rs->deve_num; //importante muere sino
            $aux++;
        }
        $html['body'] .= "</tbody>"
                . " </table>"
                . " </div></div>";
        $html['footer'] = "<label>Cantidad Activo: $ca</label><br>"
                . " <label>Cantidad Pagado: $cp</label>";
        return json_encode($html);
    }

    //Jesus M
    public function detalle_anulacion(Request $request) {
        $id_devengado = $request->input("idDevengado");
        $monto = $request->input("montoTotal");
        $boleta_deve = $request->input("boleta");
        $cod_grupo = $request->input("grupo");
        $data = Devengados::load_devengado($id_devengado);
        $tipo = Devengados::tipo_sustento();
        $fecha_hoy = Devengados::fecha_hoy();
        $html['head'] = "Nota de Cr&eacute;dito - Anulaci&oacute;n de pago";
        $html['body'] = "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6 input-group mb-3'><div class='input-group-prepend'>"
                . "<span class='input-group-text'>" . $data[0]->serie_desc . "-</span></div>"
                . "<input type='text' class='form-control' id='numeroNota' onkeypress='return validaNumericos(event)'/></div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Tipo de Anulación:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<select class='form-control' id='tipoNota'>";
        foreach ($tipo as $datos) {
            $html['body'] .= "<option value='" . $datos->id . "'>" . $datos->nombre . "</option>";
        }
        $html['body'] .= "</select>"
                . "</div>"
                . "</div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Monto de la Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6 input-group spinner'>"
                . "<input type='number' id='montoNota' value='0' min='0' max='99999.99' class='form-control'/>"
                . "<input type='hidden' id='montoMax' value='" . $monto . "'/>"
                . "<input type='hidden' id='idDevengado' value='" . $id_devengado . "'/>"
                . "<input type='hidden' id='deveBoleta' value='" . $boleta_deve . "'/>"
                . "<input type='hidden' id='grupoCod' value='" . $cod_grupo . "'/>"
                . "<div class='input-group-btn-vertical'>"
                . "<button class='btn btn-default' type='button'><i class='fa fa-plus'></i></button>"
                . "<button class='btn btn-default' type='button'><i class='fa fa-minus'></i></button>"
                . "</div>"
                . "</div>"
                . "</div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Fecha de la Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<div class='form-group' style='margin-bottom: 0px;'></div>"
                . "<div class='input-group'><div class='input-group-addon'><i class='fa fa-calendar'></i></div>&nbsp;"
                . "<input type='text' class='form-control pull-right' id='fechaNota' value='" . $fecha_hoy[0]->fecha . "' readonly >"
                . "</div></div></div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Observaci&oacute;n:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<textarea class='form-control' rows='3' id='obsNota' placeholder='Observaci&oacute;n'></textarea>"
                . "</div>"
                . "</div>";
        return json_encode($html);
    }

    public function delete_grupo_devengado(Request $request) {
        Devengados::deleteGrupoDevengados($request->input('id_grupo'));
        return 1;
    }

    public function anulacion_load_serie(Request $request) {
        $data = Devengados::load_devengado($request->input('id_devengado'));
        return $data[0]->serie_desc;
    }

    //Jesus M
    public function anulacion_devengado(Request $request) {
        $id_devengado = $request->input('id');
        $nota = $request->input('nota');
        $monto = $request->input('monto');
        $fecha = $request->input('fecha');
        $observacion = $request->input('observacion');
        $boleta = $request->input("boleta");
        $monto_ori = $request->input("monto_ori");
        $tipo_nota = $request->input("tipo");
        $serie = Devengados::load_devengado($id_devengado);
        $fecha_array = explode("/", $fecha);
        $anio = trim($fecha_array[2]);
        $fecha_s = trim($fecha_array[2]) . "-" . trim($fecha_array[1]) . "-" . trim($fecha_array[0]);
        Devengados::anulacion_devengado([
            'not_anio' => $anio,
            'not_documento' => $serie[0]->serie_desc . "-" . $nota,
            'not_serie' => $serie[0]->id_serie,
            'not_numero' => $nota,
            'not_fecha' => $fecha_s,
            'not_fechor' => DB::raw('now()'),
            'not_doc_afecta' => $boleta,
            'not_tsus_id' => $tipo_nota,
            'not_descripcion' => trim($observacion),
            'not_monto' => $monto,
            'not_estado' => '1'
                ], $id_devengado);
        return 1;
    }

    public function anulacion_pago(Request $request) {
        $id_pago = $request->input('id_pago');
        $comentario = $request->input('comentario');
        $data = Devengados::load_pago_modal($id_pago);
        Devengados::anulacion_devengado($id_pago, $comentario);
        Devengados::anulacion_pago($id_pago, $comentario);
        return 1;
    }

    public function lista_grupo_pago() {
        $data = Devengados::lista_grupo_pago();
        if (count($data) > 0) {
            $html = "<table class='table table-hover table-sm'>"
                    . "<thead>"
                    . "<tr>"
                    . "<th>Nro.</th>"
                    . "<th>Fecha Carga</th>"
                    . "<th>Empleado Carga</th>"
                    . "<th>Estado</th>"
                    . "<th>Accion</th></tr>"
                    . "</thead>"
                    . "<tbody>";
            $i = 1;
            foreach ($data as $row) {
                $detalle = "carga correcta";
                $color = "";
                $button = "";
                if (($row->cant_total) > ($row->cant_sin_serie)) {
                    $we = ($row->cant_total) - ($row->cant_sin_serie);
                    $detalle = "$we errores detectados";
                    $color = "color:red";
                    $button = "<button class='btn btn-danger' onclick=delete_pagos($row->id_grupo_pago)><i class='far fa-trash-alt'></i></button>";
                } else if ($row->cant_total == 0) {
                    $detalle = "no hay registros";
                    $color = "color:red";
                    $button = "<button class='btn btn-danger' onclick=delete_pagos($row->id_grupo_pago)><i class='far fa-trash-alt'></i></button>";
                }
                $html .= "<tr style='$color'>"
                        . "<td>$i</td>"
                        . "<td>$row->grupo_fecha</td>"
                        . "<td>$row->nombre</td>"
                        . "<td>$detalle</td>"
                        . "<td>"
                        . "<button class = 'btn btn-primary' onclick = load_pagos_modal($row->id_grupo_pago)><i class = 'fas fa-search-plus'></i></button>"
                        . $button
                        . "</td>"
                        . " </tr>";
                $i++;
            }
            $html .= "</tbody>"
                    . " </table>";
        } else {
            $html = "<b>Sin registros...</b>";
        }
        return $html;
    }

    public function load_modal_pago(Request $request) {
        $data = Devengados::lista_pago($request->input('id_grupo'));
        $html['head'] = "Lista de Alumnos Pago";
        //Jesus M
        $html['body'] = "<div class='row'><label class='col-sm-6 col-12 text-sm-right text-left'>Buscar:</label>"
                . "<div class=' col-sm-6 col-12'>"
                . "<input type='text' id='modal_text' class='form-control' onkeyup=doSearch('modal_table','modal_text') />"
                . "</div></div>"
                . "<div class='row'style='margin-top:15px;'><div class='table-responsive text-nowrap' style='max-height:500px;overflow:auto;'>"
                . "<table class='table table-striped' id='modal_table'>"
                . "<thead>"
                . "<tr>"
                . "<th id='tlmp1' class='sort' >Nro.</th>"
                . "<th id='tlmp2' class='sort'>GRADO</th>"
                . "<th id='tlmp3' class='sort'>FECHA EMISI&Oacute;N</th>"
                . "<th id='tlmp4' class='sort'>DNI</th>"
                . "<th id='tlmp5' class='sort'>Nro. BOLETA</th>"
                . "<th id='tlmp6' class='sort'>APELLIDOS Y NOMBRES</th>"
                . "<th id='tlmp7' class='sort'>CONCEPTO</th>"
                . "<th id='tlmp8' class='sort'>CUOTA</th>"
                . "<th id='tlmp9' class='sort'>MONTO</th>"
                . "<th id='tlmp10' class='sort'>TIPO</th>"
                . "<th id='tlmp11' class='sort'>ESTADO</th>"
                . "<th id='tlmp12' class='sort'>BANCO</th>"
                . "<th id='tlmp13' class='sort'>FECHA DE PAGO</th>"
                . "<th id='tlmp14' class='sort'>ESTADO DE CARGA</th>"
                //. "<th id='tlmp14' class='sort'>DETALLE</th>"
                . "</tr>"
                . "</thead>"
                . "<tbody>";
        $i = 1;
        $ca = 0;
        $cp = 0;
        foreach ($data as $rs) {
            $anio = substr($rs->pago_fecha, 0, 4);
            $color = "";
            $boton = "<button class='btn btn-danger' onclick='anulacion_pago($rs->id_pago)'><i class='far fa-trash-alt'></i></button>";
            if ($rs->pago_estado == "1" && $rs->id_serie != null) {
                $pago_estado = "Activo";
                $ca++;
            } else if ($rs->pago_estado == "2" && $rs->id_serie != null) {
                $color = "color:blue";
                $pago_estado = "Pagado";
                $cp++;
            } else if ($rs->pago_estado == "0") {
                //$color = "color:red";
                $pago_estado = "Anulado <span class='pagoanulado'><i>i</i><p>$rs->comentario_anulacion</p></span>";
                $boton = "";
            } else {
                $color = "color:red";
                $pago_estado = "Error";
                $boton = "";
            }
            //Jesus M
            $html['body'] .= "<tr style='$color'>"
                    . "<td>$i</td>"
                    . "<td>" . $rs->pago_grado . "</td>"
                    . "<td style = 'white-space:nowrap'>" . $rs->pago_emision . "</td>"
                    . "<td>" . $rs->pago_dni . "</td>"
                    . "<td style = 'white-space:nowrap'>" . $rs->pago_boleta . "</td>"
                    . "<td>" . $rs->pago_alumno . "</td>"
                    . "<td>" . $rs->pago_concepto . "</td>"
                    . "<td>" . $rs->pago_cuota . "</td>"
                    . "<td>" . $rs->pago_monto . "</td>"
                    . "<td>" . $rs->pago_tipo . "</td>"
                    . "<td>" . $rs->pago_estado_tipo . "</td>"
                    . "<td>" . $rs->pago_banco . "</td>"
                    . "<td>" . $rs->pago_fecha . "</td>"
                    . "<td>$pago_estado   </td>"
                    //. "<td>$boton</td>"
                    //. "<td>$rs->pago_detalle</td>"
                    . "</tr>";
            //Jesus M
            $i++;
        }
        $html['body'] .= "</tbody>"
                . "</table>"
                . "</div></div>";
        //Jesus M
        $cant_detalle = "";
        if (count($data) == 1) {
            $cant_detalle = "registro";
        } else {
            $cant_detalle = "registros";
        }
        $html['body'] .= "<div class='row'>" .
                "<div class='col-12 text-right'>Cantidad: " . count($data) . " " . $cant_detalle . "&nbsp&nbsp</div>"
                . "</div>";

        return json_encode($html);
    }

    //Jesus M
    public function delete_grupo_pago(Request $request) {
        Devengados::deleteGrupopagos($request->input('id_grupo'));
        return 1;
    }

    public function info_anulacion(Request $request) {
        $nota_id = $request->input('idNota');
        $data = Devengados::load_nota_credito($nota_id);
        $html['head'] = "Nota de Crédito - Detalle de anulación";
        $html['body'] = "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Serie y Comprobante de la nota de cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_documento . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Fecha de la nota de cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_fecha . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Monto de la nota de cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_monto . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Tipo de anulaci&oacute;n:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->tipo . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Documento que afecta:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_doc_afecta . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Fecha y hora de registro:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_fechor . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Observaci&oacute;n de la nota de cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->not_descripcion . "</span>"
                . "</div></div>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-6'><label class='col-12'>Estado de la nota de cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<span>" . $data[0]->estado . "</span>"
                . "</div></div>";
        return json_encode($html);
    }

    public function detalle_edita_anulacion(Request $request) {
        $id_nota = $request->input("idNota");
        $cod_grupo = $request->input("grupo");
        $monto_max = $request->input("monto_max");
        $data = Devengados::load_nota_credito($id_nota);
        $tipo = Devengados::tipo_sustento();
        $not_documento = explode("-", $data[0]->not_documento);
        $selected = "";
        $html['head'] = "Nota de Cr&eacute;dito - Modificaci&oacute;n";
        $html['body'] = "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6 input-group mb-3'><div class='input-group-prepend'>"
                . "<span class='input-group-text'>" . $not_documento[0] . "-</span></div>"
                . "<input type='text' class='form-control' id='notNumero' value='" . $data[0]->not_numero . "' onkeypress='return validaNumericos(event)'/></div></div>"
                . "<input type='hidden' id='notaCod' value='" . $data[0]->not_id . "'>"
                . "<input type='hidden' id='notMontoMax' value='" . $monto_max . "'>"
                . "<input type='hidden' id='notSerie' value='" . $data[0]->not_serie . "'>"
                . "<input type='hidden' id='notSerieDesc' value='" . $not_documento[0] . "'>"
                . "<input type='hidden' id='grupoCod' value='" . $cod_grupo . "'/>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Tipo de Anulación:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<select class='form-control' id='notTipo'>";
        foreach ($tipo as $tipos) {
            if ($tipos->id == $data[0]->tsus_id) {
                $selected = " selected='selected' ";
            } else {
                $selected = "";
            }
            $html['body'] .= "<option value='" . $tipos->id . "' $selected >" . $tipos->nombre . "</option>";
        }
        $html['body'] .= "</select>"
                . "</div>"
                . "</div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Monto de la Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6 input-group spinner'>"
                . "<input type='number' id='notMonto' value='" . $data[0]->not_monto . "' min='0' max='99999.99' class='form-control'/>"
                . "<div class='input-group-btn-vertical'>"
                . "<button class='btn btn-default' type='button'><i class='fa fa-plus'></i></button>"
                . "<button class='btn btn-default' type='button'><i class='fa fa-minus'></i></button>"
                . "</div>"
                . "</div>"
                . "</div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Fecha de la Nota de Cr&eacute;dito:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<div class='form-group' style='margin-bottom: 0px;'></div>"
                . "<div class='input-group'><div class='input-group-addon'><i class='fa fa-calendar'></i></div>&nbsp;"
                . "<input type='text' class='form-control pull-right' id='notFecha' value='" . $data[0]->not_fecha . "' readonly >"
                . "</div></div></div><br>"
                . "<div class='row'>"
                . "<div class='col-12 col-sm-6 col-md-4'><label class='col-12'>Observaci&oacute;n:</label></div>"
                . "<div class='col-12 col-sm-6 col-md-6'>"
                . "<textarea class='form-control' rows='3' id='notObs'>" . $data[0]->not_descripcion . "</textarea>"
                . "</div>"
                . "</div>";
        return json_encode($html);
    }

    public function modificar_nota(Request $request) {
        $id_nota = $request->input("id_nota");
        $nota_numero = $request->input("nota_numero");
        $nota_monto = $request->input("nota_monto");
        $monto_ori = $request->input("monto_ori");
        $nota_fecha = $request->input("nota_fecha");
        $nota_observacion = $request->input("nota_observacion");
        $nota_tipo = $request->input("nota_tipo");
        $nota_serie = $request->input("nota_serie");
        $serie = Devengados::load_serieDetalle_x_id($nota_serie);
        $numero_nota = $serie[0]->serie_desc . "-" . $nota_numero;
        $valida = Devengados::validar_existe_nota_credito($id_nota, $numero_nota);
        $fecha_array = explode("/", $nota_fecha);
        $anio = trim($fecha_array[2]);
        $fecha_s = trim($fecha_array[2]) . "-" . trim($fecha_array[1]) . "-" . trim($fecha_array[0]);
        $resp = 0;

        if ($valida[0]->cantidad > 0) {
            $resp = 2;
        } else {
            Devengados::update_nota_credito([
                "not_anio" => "$anio",
                "not_documento" => "$numero_nota",
                "not_serie" => "$nota_serie",
                "not_numero" => "$nota_numero",
                "not_fecha" => "$fecha_s",
                "not_fechor" => DB::raw('now()'),
                "not_tsus_id" => "$nota_tipo",
                "not_descripcion" => "$nota_observacion",
                "not_monto" => "$nota_monto",
                "not_estado" => "1"
                    ], $id_nota);
            $resp = 1;
        }
        return $resp;
    }

    function detalle_eliminar_anulacion(Request $request) {
        $id_nota = $request->input("idNota");
        $nota_numero = $request->input("grupo");
        $data = Devengados::load_nota_credito($id_nota);
        $nota_credito = $data[0]->serie . "-" . $data[0]->not_numero;
        $html['head'] = "Nota de Cr&eacute;dito - Eliminaci&oacute;n";
        $html['body'] = "<label>&iquest;Est&aacute; seguro de eliminar la nota de cr&eacute;dito <b>$nota_credito</b>?</label>"
                . "<input type='hidden' id='notaCod' value='" . $id_nota . "'/>"
                . "<input type='hidden' id='grupoCod' value='" . $nota_numero . "'/>";
        return json_encode($html);
    }

    function eliminar_nota(Request $request) {
        $id_nota = $request->input("id_nota");
        Devengados::delete_nota_credito($id_nota);
        return 1;
    }

    /*     * ***************** CARGA NOTAS DE CREDITO *********************** */

    function upload_notasCreditos(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel_notasCreditos' => 'required|mimes:xlsx,xls|max:50000000'
        ]);
        if ($validacion->passes()) {
//traigo id sesion
            $per_id = auth()->user()->id;
//nombre de la tabla temporal
            $table_name = "tmp_tb_notas_" . $per_id;
//creo y retorno un grupo
            $id_grupo = Devengados::insertGrupNotaCredito([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
//traigo todas las series
            $serie = Devengados::loadSerieNotasCreditos();
//importo el excel
            $excel = $request->file('excel_notasCreditos')->store('tmp_notas');
//creo tabla temporal
            Devengados::uploadNotasCreditos_tmp_create($table_name);
//importo data a la temporal
            $import = new NotasCreditos_import($id_grupo, $serie, $table_name);
            Excel::import($import, $excel);
            //$data = Devengados::selectPagos($id_grupo);
//cargo data de la temporal            
            $data_modal = Devengados::selectNotasCreditos_tmp($table_name);
//cargo data erronea de la temporal              
            $data = Devengados::selectNotasCreditos_tmp_error($table_name);
            $html = "";
            //Jesus M;
            $modal = "<div class='container-fluid'><div class='row'>"
                    . "<div class='col-xs-12 text-right' style='padding-bottom:10px;width:100%;'>"
                    . "<button onclick='reload_modaltmpnota()' class='btn'><i class='fa fa-sync'></i></button>"
                    . "</div></div></div>"
                    . "<div class='row'><div class='table-responsive text-nowrap' id='modal_notasCreditos_tmp' style='max-height:500px;overflow:auto;'>";
            if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
                $modal .= "<table class='table table-striped'><thead>"
                        . "<th>Nro.</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA NOTA CREDITO</th>"
                        . "<th>DOCUMENTO QUE AFECTA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "<th>DETALLE</th>"
                        . "<th>NRO. AVISOS</th>"
                        . "</thead><tbody>";
                $id_serie = 0;
                $deve_num = 0;
                $i = 1;
                //Jesus M
                $cant_detalle = "";
                if (count($data_modal) == 1) {
                    $cant_detalle = "registro";
                } else {
                    $cant_detalle = "registros";
                }
                $button = "<div class='row'>"
                        . "<div class='col-3'>"
                        . "<button class='btn btn-primary' id='subir_pagos_tmp' onclick='subir_notasCreditos_tmp()'>Subir Notas de Créditos</button>"
                        . "</div>"
                        . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
                //Jesus M;
                $error = 0;
                $error_mensaje = "";
                $indice = 1;
                foreach ($data_modal as $rs) {//lista de carga excel
                    $color = "";
                    if ($rs->detalle == "ENCONTRADO") {//encontro nota
                        $color = "color:blue;";
                    }
                    $modal .= "<tr style='$color' id='$rs->id_nota'>"
                            . "<td>$i</td>"
                            . "<td>$rs->nota_fecha_emicar</td>"
                            . "<td>$rs->nota_fecha_venc</td>"
                            . "<td>$rs->nota_fecha</td>"
                            . "<td>$rs->nota_emision</td>"
                            . "<td>$rs->nota_grado</td>"
                            . "<td>$rs->nota_boleta</td>"
                            . "<td>$rs->doc_afecta</td>"
                            . "<td>$rs->nota_dni</td>"
                            . "<td>$rs->nota_alumno</td>"
                            . "<td>$rs->nota_concepto</td>"
                            . "<td>$rs->nota_serie_ticke</td>"
                            . "<td>$rs->nota_dscto</td>"
                            . "<td>$rs->nota_base_imp</td>"
                            . "<td>$rs->nota_igv</td>"
                            . "<td>" . $rs->nota_total * (-1) . "</td>"
                            . "<td>$rs->nota_monto_cancelado</td>"
                            . "<td>$rs->nota_tc</td>"
                            . "<td>$rs->nota_tipo</td>"
                            . "<td>$rs->nota_centro</td>"
                            . "<td>$rs->nota_estado_tipo</td>"
                            . "<td>$rs->nota_banco</td>"
                            . "<td>$rs->nota_observaciones</td>"
                            . "<td>$rs->nota_nro_avisos</td>"
                            . "</tr>";
                    $i++;
                }

                $modal .= "</tbody></table>";
            }
            if (count($data) != 0) {
                $button = "<b>Se presentan errores en uno o mas registros, corrijalos antes de proceder con la carga</b>";
            }
            if ($error == 1) {
                $error_mensaje = "<b>Se han encontrado fallas en el correlativo, corrija el excel antes de proceder con la inserci&oacute;n de carga.</b>";
            }
            $modal .= "</div></div>"
                    . "<div class='row' >"
                    . "<div class='col-12'style='margin-top:15px;'>$error_mensaje</div>"
                    . "<div class='col-12'style='margin-top:15px;'id='modal_subir_pagos'>"
                    . "$button"
                    . "</div>"
                    . "</div>";
            if (count($data) != 0) {
                $html = "<thead>"
                        . "<th>ACCION</th>"
                        . "<th>F. EMISION CARGO</th>"
                        . "<th>F. VENC.</th>"
                        . "<th>F. PAGO</th>"
                        . "<th>F. EMISION DOCUMENTO</th>"
                        . "<th>MATRICULA</th>"
                        . "<th>BOLETA</th>"
                        . "<th>RUC/DNI</th>"
                        . "<th>CLIENTE</th>"
                        . "<th>CONCEPTO</th>"
                        . "<th>SERIE TICKETER</th>"
                        . "<th>DSCTO</th>"
                        . "<th>BASE IMP.</th>"
                        . "<th>IGV</th>"
                        . "<th>TOTAL</th>"
                        . "<th>CANCELADO</th>"
                        . "<th>T.C</th>"
                        . "<th>TIPO</th>"
                        . "<th>CENTRO</th>"
                        . "<th>ESTADO</th>"
                        . "<th>BANCO</th>"
                        . "</thead><tbody>";
                foreach ($data as $rs) {
                    $html .= "<tr id='$rs->id_pago'>"
                            . "<td><button class='btn btn-primary' onclick='modal_pagoAlexia($rs->id_pago)'><i class='fas fa-search-plus'></i></button></td>"
                            . "<td>$rs->pago_fecha_emicar</td>"
                            . "<td>$rs->pago_fecha_venc</td>"
                            . "<td>$rs->pago_fecha</td>"
                            . "<td>$rs->pago_emision</td>"
                            . "<td>$rs->pago_grado</td>"
                            . "<td>$rs->pago_boleta</td>"
                            . "<td>$rs->pago_dni</td>"
                            . "<td>$rs->pago_alumno</td>"
                            . "<td>$rs->pago_concepto</td>"
                            . "<td>$rs->pago_serie_ticke</td>"
                            . "<td>$rs->pago_dscto</td>"
                            . "<td>$rs->pago_base_imp</td>"
                            . "<td>$rs->pago_igv</td>"
                            . "<td>$rs->pago_cuota</td>"
                            . "<td>$rs->pago_monto</td>"
                            . "<td>$rs->pago_monto_cancelado</td>"
                            . "<td>$rs->pago_tc</td>"
                            . "<td>$rs->pago_tipo</td>"
                            . "<td>$rs->pago_centro</td>"
                            . "<td>$rs->pago_estado_tipo</td>"
                            . "<td>$rs->pago_banco</td>"
                            . "</tr>";
                }
                $html .= "</tbody>";
            }
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        //'html' => Devengados::selectPagos($id_grupo)
                        'html' => $html,
                        'modal' => $modal
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function upload_notas() {
        $table_name = "tmp_tb_notas_" . auth()->user()->id;
        Devengados::upload_notas($table_name);
        return 1;
    }

    function refresh_modal_notasCreditos() {
        $table_name = "tmp_tb_notas_" . $per_id;
        $data_modal = Devengados::selectNotasCreditos_tmp($table_name);
        $modal = "";
        if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
            $modal .= "<table class='table table-striped'><thead>"
                    . "<th>Nro.</th>"
                    . "<th>F. EMISION CARGO</th>"
                    . "<th>F. VENC.</th>"
                    . "<th>F. PAGO</th>"
                    . "<th>F. EMISION DOCUMENTO</th>"
                    . "<th>MATRICULA</th>"
                    . "<th>BOLETA NOTA CREDITO</th>"
                    . "<th>DOCUMENTO QUE AFECTA</th>"
                    . "<th>RUC/DNI</th>"
                    . "<th>CLIENTE</th>"
                    . "<th>CONCEPTO</th>"
                    . "<th>SERIE TICKETER</th>"
                    . "<th>DSCTO</th>"
                    . "<th>BASE IMP.</th>"
                    . "<th>IGV</th>"
                    . "<th>TOTAL</th>"
                    . "<th>CANCELADO</th>"
                    . "<th>T.C</th>"
                    . "<th>TIPO</th>"
                    . "<th>CENTRO</th>"
                    . "<th>ESTADO</th>"
                    . "<th>BANCO</th>"
                    . "<th>DETALLE</th>"
                    . "<th>NRO. AVISOS</th>"
                    . "</thead><tbody>";
            $id_serie = 0;
            $deve_num = 0;
            $i = 1;
            //Jesus M
            $cant_detalle = "";
            if (count($data_modal) == 1) {
                $cant_detalle = "registro";
            } else {
                $cant_detalle = "registros";
            }
            $button = "<div class='row'>"
                    . "<div class='col-3'>"
                    . "<button class='btn btn-primary' id='subir_pagos_tmp' onclick='subir_notasCreditos_tmp()'>Subir Notas de Créditos</button>"
                    . "</div>"
                    . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
            $error = 0;
            $error_mensaje = "";
            $indice = 1;
            foreach ($data_modal as $rs) {//lista de carga excel
                $color = "";
                if ($rs->detalle == "ENCONTRADO") {//encontro nota
                    $color = "color:blue;";
                }
                $modal .= "<tr style='$color' id='$rs->id_nota'>"
                        . "<td>$i</td>"
                        . "<td>$rs->nota_fecha_emicar</td>"
                        . "<td>$rs->nota_fecha_venc</td>"
                        . "<td>$rs->nota_fecha</td>"
                        . "<td>$rs->nota_emision</td>"
                        . "<td>$rs->nota_grado</td>"
                        . "<td>$rs->nota_boleta</td>"
                        . "<td>$rs->doc_afecta</td>"
                        . "<td>$rs->nota_dni</td>"
                        . "<td>$rs->nota_alumno</td>"
                        . "<td>$rs->nota_concepto</td>"
                        . "<td>$rs->nota_serie_ticke</td>"
                        . "<td>$rs->nota_dscto</td>"
                        . "<td>$rs->nota_base_imp</td>"
                        . "<td>$rs->nota_igv</td>"
                        . "<td>" . $rs->nota_total * (-1) . "</td>"
                        . "<td>$rs->nota_monto_cancelado</td>"
                        . "<td>$rs->nota_tc</td>"
                        . "<td>$rs->nota_tipo</td>"
                        . "<td>$rs->nota_centro</td>"
                        . "<td>$rs->nota_estado_tipo</td>"
                        . "<td>$rs->nota_banco</td>"
                        . "<td>$rs->nota_observaciones</td>"
                        . "<td>$rs->nota_nro_avisos</td>"
                        . "</tr>";
                $i++;
            }

            $modal .= "</tbody></table>";
        }
        return response()->json([
                    'modal' => $modal
        ]);
    }

    public function lista_grupo_nota_credito() {
        $data = Devengados::lista_grupo_nota_credito();
        if (count($data) > 0) {
            $html = "<div class='table-responsive'>"
                    . "<table class='table table-hover table-sm' >"
                    . "<thead>"
                    . "<tr>"
                    . "<th>Nro.</th>"
                    . "<th>Fecha Carga</th>"
                    . "<th>Empleado Carga</th>"
                    . "<th>Estado</th>"
                    . "<th>Accion</th>"
                    . "</tr>"
                    . "</thead>"
                    . "<tbody>";
            $i = 1;
            foreach ($data as $row) {
                $detalle = "carga correcta";
                $color = "";
                $button = "";
                if (($row->cant_total) > ($row->cant_sin_serie)) {
                    $we = ($row->cant_total) - ($row->cant_sin_serie);
                    $detalle = "$we errores detectados";
                    $color = "color:red";
                    //$button = "<button class='btn btn-danger' onclick=delete_devengados($row->id_grupo)><i class='far fa-trash-alt'></i></button>";
                    $button = "";
                } else if ($row->cant_total == 0) {
                    $detalle = "no hay registros";
                    $color = "color:red";
                    //$button = "<button class='btn btn-danger' onclick=delete_devengados($row->id_grupo)><i class='far fa-trash-alt'></i></button>";
                    $button = "";
                }
                $html .= "<tr style='$color'>"
                        . "<td>$i</td>"
                        . "<td>$row->grupo_fecha</td>"
                        . "<td>$row->nombre</td>"
                        . "<td>$detalle</td>"
                        . "<td>"
                        . "<button class='btn btn-primary' onclick=load_notas_creditos_modal($row->id_grupo)><i class='fas fa-search-plus'></i></button>"
                        . $button
                        . "</td>"
                        . "</tr>";
                $i++;
            }
            $html .= "</tbody>"
                    . "</table>"
                    . "</div>";
        } else {
            $html = "<b>Sin registros...</b>";
        }

        return $html;
    }

    public function load_modal_nota_credito(Request $request) {
        $data = Devengados::lista_nota_creditos($request->input('id_grupo'));
        $html['head'] = "Lista de Notas de Créditos";
        //Jesus M
        $html['body'] = "<div class='row'><label class='col-sm-6 col-12 text-sm-right text-left'>Buscar:</label>"
                . "<div class=' col-sm-6 col-12'>"
                . "<input type='text' id='modal_text' class='form-control' onkeyup=doSearch('modal_table','modal_text') />"
                . "</div></div>"
                . "<div class='row'style='margin-top:15px;'><div class='table-responsive text-nowrap' style='max-height:500px;overflow:auto;'>"
                . "<table class='table table-striped' id='modal_table'>"
                . "<thead>"
                . "<tr>"
                . "<th id='tlmp1' class='sort' >Nro.</th>"
                . "<th id='tlmp2' class='sort'>AÑO</th>"
                . "<th id='tlmp3' class='sort'>Nro. NOTA DE CREDITO</th>"
                . "<th id='tlmp4' class='sort'>MONTO</th>"
                . "<th id='tlmp5' class='sort'>FECHA EMISI&Oacute;N</th>"
                . "<th id='tlmp6' class='sort'>FECHA Y HORA REGISTRO</th>"
                . "<th id='tlmp7' class='sort'>DNI/RUC</th>"
                . "<th id='tlmp8' class='sort'>APELLIDOS Y NOMBRES</th>"
                . "<th id='tlmp9' class='sort'>DOCUMENTO AFECTA</th>"
                . "<th id='tlmp10' class='sort'>TIPO SUSTENTO</th>"
                . "<th id='tlmp11' class='sort'>DESCRIPCIÓN</th>"
                . "<th id='tlmp12' class='sort'>ESTADO</th>"
                //. "<th id='tlmp14' class='sort'>DETALLE</th>"
                . "</tr>"
                . "</thead>"
                . "<tbody>";
        $i = 1;
        $ca = 0;
        $cp = 0;
        $not_estado = '';
        foreach ($data as $rs) {
            $anio = $rs->not_anio;
            $color = "";
            $boton = "<button class='btn btn-danger' onclick='anulacion_pago($rs->not_id)'><i class='far fa-trash-alt'></i></button>";
            if ($rs->not_estado == "1" && $rs->not_serie != null) {
                $not_estado = "Activo";
                $ca++;
            } else if ($rs->not_estado == "0") {
                //$color = "color:red";
                $not_estado = "Anulado <span class='pagoanulado'><i>i</i><p>$rs->not_descripcion</p></span>";
                $boton = "";
            } else {
                $color = "color:red";
                $not_estado = "Error";
                $boton = "";
            }
            //Jesus M
            $html['body'] .= "<tr style='$color'>"
                    . "<td>$i</td>"
                    . "<td>" . $anio . "</td>"
                    . "<td>" . $rs->not_documento . "</td>"
                    . "<td>" . $rs->not_monto . "</td>"
                    . "<td style = 'white-space:nowrap'>" . $rs->not_fecha . "</td>"
                    . "<td>" . $rs->not_fechor . "</td>"
                    . "<td>" . $rs->not_dni . "</td>"
                    . "<td>" . $rs->not_nombres . "</td>"
                    . "<td>" . $rs->not_doc_afecta . "</td>"
                    . "<td>" . $rs->tipo . "</td>"
                    . "<td>" . $rs->not_descripcion . "</td>"
                    . "<td>" . $rs->estado . "</td>"
                    //. "<td>$boton</td>"
                    //. "<td>$rs->pago_detalle</td>"
                    . "</tr>";
            //Jesus M
            $i++;
        }
        $html['body'] .= "</tbody>"
                . "</table>"
                . "</div></div>";
        //Jesus M
        $cant_detalle = "";
        if (count($data) == 1) {
            $cant_detalle = "registro";
        } else {
            $cant_detalle = "registros";
        }
        $html['body'] .= "<div class='row'>" .
                "<div class='col-12 text-right'>Cantidad: " . count($data) . " " . $cant_detalle . "&nbsp&nbsp</div>"
                . "</div>";

        return json_encode($html);
    }

    //chinitos

    public function upload_comprobantes_ose(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'excel_ose' => 'required|mimes:xlsx,xls|max:50000000'
        ]);
        if ($validacion->passes()) {
//traigo id sesion
            $per_id = auth()->user()->id;
//nombre de la tabla temporal
            $table_name = "tmp_tb_comprobantes_ose_" . $per_id;
//importo el excel
            $excel = $request->file('excel_ose')->store('tmp_comprobante_ose');
//creo tabla temporal
            Devengados::uploadComprobantesOse_tmp_create($table_name);
//importo data a la temporal
            $import = new ComprobantesOse_import($table_name);
            Excel::import($import, $excel);
//cargo data de la temporal
            $data_modal = Devengados::selectComprobanteOse_tmp($table_name);
//cargo data erronea de la temporal              
            //$data = Devengados::selectNotasCreditos_tmp_error($table_name);

            $html = "";
            //chinitos
            $modal = "<div class='container-fluid'><div class='row'>"
                    . "<div class='col-xs-12 text-right' style='padding-bottom:10px;width:100%;'>"
                    . "<button onclick='reload_modaltmpcomprobanteOse()' class='btn'><i class='fa fa-sync'></i></button>"
                    . "</div></div></div>"
                    . "<div class='row'><div class='table-responsive text-nowrap' id='modal_comprobanteOse_tmp' style='max-height:500px;overflow:auto;'>";
            if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
                $modal .= "<table class='table table-striped'><thead>"
                        . "<th>Nro.</th>"
                        . "<th>TIPO</th>"
                        . "<th>TIPO DOCUMENTO</th>"
                        . "<th>DOC. IDENTIDAD</th>"
                        . "<th>SERIE</th>"
                        . "<th>NUMERO</th>"
                        . "<th>FECHA ENVIO</th>"
                        . "<th>NOMBRES</th>"
                        . "<th>DESCUENTO</th>"
                        . "<th>RECARGO</th>"
                        . "<th>GRATUITO</th>"
                        . "<th>IGV</th>"
                        . "<th>ISC</th>"
                        . "<th>NETO</th>"
                        . "<th>TOTAL</th>"
                        . "<th>TIPO MONEDA</th>"
                        . "<th>TIPO CAMBIO</th>"
                        . "<th>OBSERVACION</th>"
                        . "</thead><tbody>";
                $id_serie = 0;
                $deve_num = 0;
                $i = 1;
                //Jesus M
                $cant_detalle = "";
                if (count($data_modal) == 1) {
                    $cant_detalle = "registro";
                } else {
                    $cant_detalle = "registros";
                }
                $button = "<div class='row'>"
                        . "<div class='col-3'>"
                        . "<button class='btn btn-primary' id='subir_comprobantesOSE_tmp' onclick='subir_comprobantesOse_tmp()'>Subir Comprobantes de la OSE</button>"
                        . "</div>"
                        . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
                //chinitos mini;
                $error = 0;
                $error_mensaje = "";
                $indice = 1;
                $ose_num = 0;
                $id_serie = "";
                foreach ($data_modal as $rs) {//lista de carga excel
                    $color = "";
                    if ($rs->id_serie != $id_serie) {//cambio de serie
                        $id_serie = $rs->id_serie;
                        $ose_num = $rs->com_numero;
                        $color = "";
                    } else {//misma serie, empieza correlativo
                        if (($rs->com_numero - $ose_num) != 1) {//error en correlativo
                            $error = 1;
                            $correlativo = $ose_num + 1;
                            $index = $rs->com_numero - $correlativo;
                            for ($a = 0; $a < $index; $a++) {
                                $color = "color:red;";
                                $numeracion = $rs->com_serie . "-" . $correlativo;
                                $modal .= "<tr style='$color'><td colspan='19'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $numeracion</td></tr>";
                                $correlativo++;
                            }
                        } else {
                            $color = "";
                        }
                    }
                    $ose_num = $rs->com_numero;
                    if ($rs->detalle == "ENCONTRADO") {//encontro nota
                        $color = "color:blue;";
                    } else if ($rs->detalle == "NUEVO") {
                        $color = "";
                    }
                    $modal .= "<tr style='$color' id='$rs->id_com'>"
                            . "<td>$i</td>"
                            . "<td>$rs->detalle</td>"
                            . "<td>$rs->com_tipo_documento</td>"
                            . "<td>$rs->com_doc_iden</td>"
                            . "<td>$rs->com_serie</td>"
                            . "<td>$ose_num </td>"
                            . "<td>$rs->com_fecha_envio</td>"
                            . "<td>$rs->com_nombres</td>"
                            . "<td>$rs->com_descuento</td>"
                            . "<td>$rs->com_recargo</td>"
                            . "<td>$rs->com_gratuito</td>"
                            . "<td>$rs->com_igv</td>"
                            . "<td>$rs->com_isc</td>"
                            . "<td>$rs->com_neto</td>"
                            . "<td>$rs->com_total</td>"
                            . "<td>$rs->com_tip_moneda</td>"
                            . "<td>$rs->com_tip_cambio</td>"
                            . "<td>$rs->com_observacion</td>"
                            . "</tr>";
                    $i++;
                }
                $modal .= "</tbody></table>";
            }
            if ($error == 1) {
                $error_mensaje = "<b>Se han encontrado fallas en el correlativo, corrija el excel antes de proceder con la inserci&oacute;n de carga.</b>";
            }
            $modal .= "</div></div>"
                    . "<div class='row' >"
                    . "<div class='col-12'style='margin-top:15px;'>$error_mensaje</div>"
                    . "<div class='col-12'style='margin-top:15px;'id='modal_subir_comprobantesOse'>"
                    . "$button"
                    . "</div>"
                    . "</div>";
            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success',
                        //'html' => Devengados::selectPagos($id_grupo)
                        'html' => $html,
                        'modal' => $modal
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function registrar_comprobanteOse() {
        $per_id = auth()->user()->id;
        $table_name = "tmp_tb_comprobantes_ose_" . auth()->user()->id;
        //carga temporal
        $data_modal = Devengados::verificar_exiten_comprobanteOse_nuevos($table_name);
        if (count($data_modal) != 0) { //si hay datos se crea registro en grupo y luego en comprobante_ose
            //creo y retorno un grupo
            $id_grupo = Devengados::insertGrupComprobanteOse([
                        'id_per' => $per_id,
                        'grupo_estado' => '1'
            ]);
            Devengados::upload_tb_comprobantesOse($table_name, $id_grupo);
            return 1;
        } else {
            return 0;
        }
    }

    function refresh_modal_comprobanteOse() {
        $per_id = auth()->user()->id;
        $table_name = "tmp_tb_comprobantes_ose_" . $per_id;
        $data_modal = Devengados::selectComprobanteOse_tmp($table_name);
        $modal = "";
        if (count($data_modal) != 0) {//si inserto correctamente prepara lista tmp
            $modal .= "<table class='table table-striped'><thead>"
                    . "<th>Nro.</th>"
                    . "<th>TIPO</th>"
                    . "<th>TIPO DOCUMENTO</th>"
                    . "<th>DOC. IDENTIDAD</th>"
                    . "<th>SERIE</th>"
                    . "<th>NUMERO</th>"
                    . "<th>FECHA ENVIO</th>"
                    . "<th>NOMBRES</th>"
                    . "<th>DESCUENTO</th>"
                    . "<th>RECARGO</th>"
                    . "<th>GRATUITO</th>"
                    . "<th>IGV</th>"
                    . "<th>ISC</th>"
                    . "<th>NETO</th>"
                    . "<th>TOTAL</th>"
                    . "<th>TIPO MONEDA</th>"
                    . "<th>TIPO CAMBIO</th>"
                    . "<th>OBSERVACION</th>"
                    . "</thead><tbody>";
            $id_serie = 0;
            $deve_num = 0;
            $i = 1;
            //Jesus M
            $cant_detalle = "";
            if (count($data_modal) == 1) {
                $cant_detalle = "registro";
            } else {
                $cant_detalle = "registros";
            }
            $button = "<div class='row'>"
                    . "<div class='col-3'>"
                    . "<button class='btn btn-primary' id='subir_pagos_tmp' onclick='subir_comprobantesOse_tmp()'>Subir Comprobantes de la OSE</button>"
                    . "</div>"
                    . "<div class='col-9 text-right'>Cantidad: " . count($data_modal) . " " . $cant_detalle . "&nbsp&nbsp</div>";
            //chinitos mini;
            $error = 0;
            $error_mensaje = "";
            $indice = 1;
            $ose_num = 0;
            $id_serie = "";
            foreach ($data_modal as $rs) {//lista de carga excel
                $color = "";
                if ($rs->detalle == "ENCONTRADO") {//encontro nota
                    $color = "color:blue;";
                }

                if ($rs->com_serie != $id_serie) {//cambio de serie
                    $id_serie = $rs->com_serie;
                    $ose_num = $rs->com_numero;
                } else {//misma serie, empieza correlativo
                    if (($rs->com_numero - $ose_num) != 1) {//error en correlativo
                        $error = 1;
                        $correlativo = $ose_num + 1;
                        $color = "color:red;";
                        $index = $rs->com_numero - $correlativo;
                        for ($a = 0; $a < $index; $a++) {
                            $numeracion = $rs->com_serie . "-" . $correlativo;
                            $modal .= "<tr style='$color'><td colspan='19'>ERROR EN CORRELATIVO NO SE ENCONTRO EL REGISTRO $numeracion</td></tr>";
                            $correlativo++;
                        }
                    }
                }
                $ose_num = $rs->com_numero;

                $modal .= "<tr style='$color' id='$rs->id_com'>"
                        . "<td>$i</td>"
                        . "<td>$rs->detalle</td>"
                        . "<td>$rs->com_tipo_documento</td>"
                        . "<td>$rs->com_doc_iden</td>"
                        . "<td>$rs->com_serie</td>"
                        . "<td>$ose_num</td>"
                        . "<td>$rs->com_fecha_envio</td>"
                        . "<td>$rs->com_nombres</td>"
                        . "<td>$rs->com_descuento</td>"
                        . "<td>$rs->com_recargo</td>"
                        . "<td>$rs->com_gratuito</td>"
                        . "<td>$rs->com_igv</td>"
                        . "<td>$rs->com_isc</td>"
                        . "<td>$rs->com_neto</td>"
                        . "<td>$rs->com_total</td>"
                        . "<td>$rs->com_tip_moneda</td>"
                        . "<td>$rs->com_tip_cambio</td>"
                        . "<td>$rs->com_observacion</td>"
                        . "</tr>";
                $i++;
            }

            $modal .= "</tbody></table>";
        }
        return response()->json([
                    'modal' => $modal
        ]);
    }

    function lista_grupo_comprobante_ose() {
        $data = Devengados::lista_grupo_comprobantes_ose();
        if (count($data) > 0) {
            $html = "<div class='table-responsive'>"
                    . "<table class='table table-hover table-sm' >"
                    . "<thead>"
                    . "<tr>"
                    . "<th>Nro.</th>"
                    . "<th>Fecha Carga</th>"
                    . "<th>Empleado Carga</th>"
                    . "<th>Estado</th>"
                    . "<th>Accion</th>"
                    . "</tr>"
                    . "</thead>"
                    . "<tbody>";
            $i = 1;
            foreach ($data as $row) {
                $detalle = "carga correcta";
                $color = "";
                $button = "";
                if (($row->cant_total) > ($row->cant_sin_serie)) {
                    $we = ($row->cant_total) - ($row->cant_sin_serie);
                    $detalle = "$we errores detectados";
                    $color = "color:red";
                    //$button = "<button class='btn btn-danger' onclick=delete_devengados($row->id_grupo)><i class='far fa-trash-alt'></i></button>";
                    $button = "";
                } else if ($row->cant_total == 0) {
                    $detalle = "no hay registros";
                    $color = "color:red";
                    $button = "";
                }
                $html .= "<tr style='$color'>"
                        . "<td>$i</td>"
                        . "<td>$row->grupo_fecha</td>"
                        . "<td>$row->nombre</td>"
                        . "<td>$detalle</td>"
                        . "<td>"
                        . "<button class='btn btn-primary' onclick=load_comprobantes_ose_modal($row->id_grupo)><i class='fas fa-search-plus'></i></button>"
                        . $button
                        . "</td>"
                        . "</tr>";
                $i++;
            }
            $html .= "</tbody>"
                    . "</table>"
                    . "</div>";
        } else {
            $html = "<b>Sin registros...</b>";
        }

        return $html;
    }

    function load_modal_comprobante_ose(Request $request) {
        $data = Devengados::lista_comprobantes_ose($request->input('id_grupo'));
        $html['head'] = "Lista de Comprobante de la OSE";
        $html['body'] = "<div class='row'><label class='col-sm-6 col-12 text-sm-right text-left'>Buscar:</label>"
                . "<div class=' col-sm-6 col-12'>"
                . "<input type='text' id='modal_text' class='form-control' onkeyup=doSearch('modal_table','modal_text') />"
                . "</div></div>"
                . "<div class='row'style='margin-top:15px;'><div class='table-responsive text-nowrap' style='max-height:500px;overflow:auto;'>"
                . "<table class='table table-striped' id='modal_table'>"
                . "<thead>"
                . "<tr>"
                . "<th id='tlmp1' class='sort'>Nro.</th>"
                . "<th id='tlmp2' class='sort'>TIPO DOCUMENTO</th>"
                . "<th id='tlmp3' class='sort'>DOC. IDENTIDAD</th>"
                . "<th id='tlmp4' class='sort'>SERIE</th>"
                . "<th id='tlmp5' class='sort'>NUMERO</th>"
                . "<th id='tlmp6' class='sort'>FECHA ENVIO</th>"
                . "<th id='tlmp7' class='sort'>NOMBRES</th>"
                . "<th id='tlmp8' class='sort'>DESCUENTO</th>"
                . "<th id='tlmp9' class='sort'>RECARGO</th>"
                . "<th id='tlmp10' class='sort'>GRATUITO</th>"
                . "<th id='tlmp11' class='sort'>IGV</th>"
                . "<th id='tlmp12' class='sort'>ISC</th>"
                . "<th id='tlmp13' class='sort'>NETO</th>"
                . "<th id='tlmp14' class='sort'>TOTAL</th>"
                . "<th id='tlmp15' class='sort'>TIPO MONEDA</th>"
                . "<th id='tlmp16' class='sort'>TIPO CAMBIO</th>"
                . "<th id='tlmp17' class='sort'>OBSERVACION</th>"
                . "<th id='tlmp18' class='sort'>FECHA Y HORA SISTEMA</th>"
                . "<th id='tlmp19' class='sort'>ESTADO</th>"
                . "</tr>"
                . "</thead>"
                . "<tbody>";
        $i = 1;
        $ca = 0;
        $cp = 0;
        $not_estado = '';
        foreach ($data as $rs) {
            $color = "";
            $boton = "<button class='btn btn-danger' onclick='anulacion_pago($rs->id_com)'><i class='far fa-trash-alt'></i></button>";
            if ($rs->com_estado == "1" && $rs->com_serie != null) {
                $not_estado = "Activo";
                $ca++;
            } else if ($rs->com_estado == "0") {
                //$color = "color:red";
                $not_estado = "Anulado <span class='pagoanulado'><i>i</i><p></p></span>";
                $boton = "";
            } else {
                $color = "color:red";
                $not_estado = "Error";
                $boton = "";
            }
            //Jesus M
            $html['body'] .= "<tr style='$color'>"
                    . "<td>$i</td>"
                    . "<td>$rs->com_tipo_documento</td>"
                    . "<td>$rs->com_doc_iden</td>"
                    . "<td>$rs->com_serie</td>"
                    . "<td>$rs->com_numero </td>"
                    . "<td>$rs->com_fecha_envio</td>"
                    . "<td>$rs->com_nombres</td>"
                    . "<td>$rs->com_descuento</td>"
                    . "<td>$rs->com_recargo</td>"
                    . "<td>$rs->com_gratuito</td>"
                    . "<td>$rs->com_igv</td>"
                    . "<td>$rs->com_isc</td>"
                    . "<td>$rs->com_neto</td>"
                    . "<td>$rs->com_total</td>"
                    . "<td>$rs->com_tip_moneda</td>"
                    . "<td>$rs->com_tip_cambio</td>"
                    . "<td>$rs->com_observacion</td>"
                    . "<td>$rs->com_fecha_sistema</td>"
                    . "<td>$rs->estado</td>"
                    . "</tr>";
            //Jesus M
            $i++;
        }
        $html['body'] .= "</tbody>"
                . "</table>"
                . "</div></div>";
        //Jesus M
        $cant_detalle = "";
        if (count($data) == 1) {
            $cant_detalle = "registro";
        } else {
            $cant_detalle = "registros";
        }
        $html['body'] .= "<div class='row'>" .
                "<div class='col-12 text-right'>Cantidad: " . count($data) . " " . $cant_detalle . "&nbsp&nbsp</div>"
                . "</div>";

        return json_encode($html);
    }

}

?>