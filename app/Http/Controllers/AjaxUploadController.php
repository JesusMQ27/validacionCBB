<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300); //3 minutes

use Validator;
use App\Imports\EstadoCuentaImport;
use App\Imports\PagosAlexiaImport;
use App\Imports\PagosAlexiaImportConcar;
use App\Imports\PagosAlexiaImportConcarDevengados;
use App\Imports\PagosAlexiaImportConcarNotasCredito;
use App\Imports\PagosAlexiaImportConcarNotasDebito; //Chinita
use App\Imports\PagosAlexiaImportConcarFacturas; //Chinita
use App\Imports\PagosAlexiaImportConcarBecados; //Chinita
use App\Imports\FacturacionImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use DB;
use App\Contabilidad;

class AjaxUploadController extends Controller {

    public function __construct() {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    function index() {
        //return view('validacionIngresos');
    }

    function action0(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'select_file0' => 'required|mimes:xlsx,xls|max:50000'
        ]);

        if ($validacion->passes()) {
            /* $image = $request->file('select_file');
              $new_name = rand() . "." . $image->getClientOriginalExtension();
              $image->move(public_path('img'), $new_name); */
            $per_id = auth()->user()->id;

            $excel = $request->file('select_file0')->store('tmpContabilidad');
            Contabilidad::crea_tmp_facturacion($per_id);
            $import_pa = new FacturacionImport($per_id);
            //$import_pa->onlySheets('Data');
            //dd($import_pa);
            Excel::import($import_pa, $excel);

            /* Excel::import(Input::file($excel), function ($reader) {

              foreach ($reader->toArray() as $row) {
              print_r($row);
              }
              }); */

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function action(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'select_file' => 'required|mimes:xlsx,xls|max:50000'
        ]);

        if ($validacion->passes()) {
            /* $image = $request->file('select_file');
              $new_name = rand() . "." . $image->getClientOriginalExtension();
              $image->move(public_path('img'), $new_name); */
            $per_id = auth()->user()->id;

            $excel = $request->file('select_file')->store('tmpContabilidad');
            Contabilidad::tmp_pagos_alexia($per_id);
            $import_pa = new PagosAlexiaImport($per_id);
            //$import_pa->onlySheets('Data');
            //dd($import_pa);
            Excel::import($import_pa, $excel);


            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function action2(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'select_file2' => 'required|mimes:xlsx,xls|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_file2')->store('tmpContabilidad');
            Contabilidad::tmp_estado_cuenta($per_id);
            $import_ec = new EstadoCuentaImport($per_id);
            Excel::import($import_ec, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function actionCo(Request $request) {
        //dd($_FILES);
        $validacion = Validator::make($request->all(), [
                        //'select_fileCo' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCo')->store('tmpContabilidad');
            Contabilidad::tmp_pagos_alexia_concar($per_id);
            $import_pa = new PagosAlexiaImportConcar($per_id);
            Excel::import($import_pa, $excel);


            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    /* Guadalupe */

    function actionCoDevengados(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'select_fileCoDeve' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);
        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCoDeve')->store('tmpContabilidad');
            Contabilidad::tmp_pagos_alexia_concar_devengados($per_id);
            $import_pa = new PagosAlexiaImportConcarDevengados($per_id);
            Excel::import($import_pa, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function actionCoNotasCredito(Request $request) {
        $validacion = Validator::make($request->all(), [
                    'select_fileCoNotaC' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCoNotaC')->store('tmpContabilidadNotaCredito');
            Contabilidad::tmp_pagos_alexia_concar_notas_credito($per_id);
            $import_pa = new PagosAlexiaImportConcarNotasCredito($per_id);
            Excel::import($import_pa, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    //Chinita
    function actionCoNotasDebito(Request $request) {
        $validacion = Validator::make($request->all(), [
                        //'select_fileCoNotaD' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCoNotaD')->store('tmpContabilidadNotaDedito');
            Contabilidad::tmp_pagos_alexia_concar_notas_debito($per_id);
            $import_pa = new PagosAlexiaImportConcarNotasDebito($per_id);
            Excel::import($import_pa, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function actionCoFacturas(Request $request) {
        $validacion = Validator::make($request->all(), [
                        //'select_fileCoFactura' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_file2');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCoFactura')->store('tmpContabilidadFacturas');
            Contabilidad::tmp_pagos_alexia_concar_facturas($per_id);
            $import_pa = new PagosAlexiaImportConcarFacturas($per_id);
            Excel::import($import_pa, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

    function actionCoBecados(Request $request) {
        $validacion = Validator::make($request->all(), [
                        //'select_fileCoFactura' => 'required|mimes:xlsx,xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:50000'
        ]);

        if ($validacion->passes()) {
            //$image = $request->file('select_fileCoBecado');
            //$new_name = rand() . "." . $image->getClientOriginalExtension();
            //$image->move(public_path('img'), $new_name);
            $per_id = auth()->user()->id;

            $excel = $request->file('select_fileCoBecado')->store('tmpContabilidadBecados');
            Contabilidad::tmp_pagos_alexia_concar_becados($per_id);
            $import_pa = new PagosAlexiaImportConcarBecados($per_id);
            Excel::import($import_pa, $excel);

            return response()->json([
                        'message' => 'Exito!',
                        'uploaded-image' => '',
                        'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                        'message' => $validacion->errors()->all(),
                        'uploaded-image' => '',
                        'class_name' => 'alert-danger'
            ]);
        }
    }

}

?>
