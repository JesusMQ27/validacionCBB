<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
/*
  Route::get('/', function () {
  return view('auth.login');
  }); */
//Route::get('/', 'Auth\LoginController@showLoginForm')->middleware('guest');
Route::get('/', 'Auth\LoginController@showLoginForm');

//Auth::routes();
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('principal', 'PrincipalController@principal')->name('principal');
//Route::get('/principal', 'HomeController@index')->name('principal');
//Route::get('/principal', 'LoginController@index')->name('principal');
Route::post('menu/valida', 'MenusController@valida')->name('valida');
//carga modulo
Route::get('menu/devengado/{href}', 'MenusController@loadMenuDeve')->name('loadMenu');
Route::get('menu/contabilidad/{href}', 'MenusController@loadMenuConta')->name('loadMenu');
Route::post('submenu/{href}', 'MenusController@loadSubMenuValida')->name('loadSubMenu');
Route::get('submenu/{href}', 'MenusController@loadSubMenu')->name('loadSubMenu');


/* * ************devengados rutas***************** */
//administracion
Route::get('devengados/administracion-lgd', 'DevengadosController@lista_grupo_devengado')->name('deveadmin.load_grupodeve');
Route::post('devengados/administracion-dan', 'DevengadosController@detalle_anulacion')->name('deveadmin.detalle_anulacion');
Route::post('devengados/administracion-dgd', 'DevengadosController@delete_grupo_devengado')->name('deveadmin.delete_grupodeve');
Route::post('devengados/administracion-lmd', 'DevengadosController@load_modal_devengado')->name('deveadmin.load_modaldeve');
//Route::post('devengados/administracion-ads', 'DevengadosController@anulacion_load_serie')->name('deveadmin.anulacion_load_serie');//se elimina
Route::post('devengados/administracion-ad', 'DevengadosController@anulacion_devengado')->name('deveadmin.anulacion_devengado');
Route::post('devengados/administracion-ia', 'DevengadosController@info_anulacion')->name('deveadmin.info_anulacion');
Route::post('devengados/administracion-dea', 'DevengadosController@detalle_edita_anulacion')->name('deveadmin.detalle_edita_anulacion');
Route::post('devengados/administracion-notmod', 'DevengadosController@modificar_nota')->name('deveadmin.modificar_nota');
Route::post('devengados/administracion-notel', 'DevengadosController@detalle_eliminar_anulacion')->name('deveadmin.detalle_eliminar_anulacion');
Route::post('devengados/administracion-notelim', 'DevengadosController@eliminar_nota')->name('deveadmin.eliminar_nota');

Route::get('devengados/administracion-lgp', 'DevengadosController@lista_grupo_pago')->name('deveadmin.load_grupopago');
Route::post('devengados/administracion-dgp', 'DevengadosController@delete_grupo_pago')->name('deveadmin.delete_grupopago');
Route::post('devengados/administracion-lmp', 'DevengadosController@load_modal_pago')->name('deveadmin.load_modalpago');
Route::post('devengados/administracion-ap', 'DevengadosController@anulacion_pago')->name('deveadmin.anulacion_pago');
//Reportes

Route::post('devengados/reporte', 'DevengadosController@deve_reporte')->name('deve_reporte.lista');
Route::get('devengados/reporte/excel', 'DevengadosController@deve_reporte_excel');
//carga devengados - historico
Route::post('devengados/carga_devengados', 'DevengadosController@upload_devengados')->name('devecarga.upload_devengados');
Route::post('devengados/modal/carga', 'DevengadosController@load_devengado_modal')->name('devecarga.modal_carga');
Route::post('devengados/update_devengado', 'DevengadosController@update_devengado')->name('devecarga.update_devengado');
//carga devengados - alexia
Route::post('devengados/carga_devengadosA', 'DevengadosController@upload_devengadosAlexia')->name('devecarga.upload_devengadosAlexia');
Route::post('devengados/modal/cargaA', 'DevengadosController@load_devengadoAlexia_modal')->name('devecarga.modal_cargaAlexia');
Route::post('devengados/refresh_devengadosA_tmp', 'DevengadosController@refresh_modal_devengadosAlexia')->name('devecarga.refresh_modal_devengadosAlexia');
Route::post('devengados/update_devengadoA', 'DevengadosController@update_devengadoAlexia')->name('devecarga.update_devengadoAlexia');
Route::post('devengados/upload_devengadoA', 'DevengadosController@upload_devengadoAlexia')->name('devecarga.upload_devengadoAlexia');
////Route::get('devengados/descarga', 'DevengadosController@download_devengados')->name('ajaxdownload.action');
//carga pagos - historico
Route::post('devengados/carga_pagos', 'DevengadosController@upload_pagos')->name('devepagos.upload_pagos');
Route::post('devengados/modal/pago', 'DevengadosController@load_pago_modal')->name('devepago.modal_pago');
Route::post('devengados/update_pago', 'DevengadosController@update_pago')->name('devepago.update_pago');
//carga pagos - alexia
Route::post('devengados/carga_pagosA', 'DevengadosController@upload_pagosAlexia')->name('devepagos.upload_pagosAlexia');
Route::post('devengados/modal/pagoA', 'DevengadosController@load_pago_modalAlexia')->name('devepago.modal_pagoAlexia');
Route::post('devengados/update_pagoA', 'DevengadosController@update_pagoAlexia')->name('devepago.update_pagoAlexia');
Route::post('devengados/refresh_pagosA_tmp', 'DevengadosController@refresh_modal_pagosAlexia')->name('devepago.refresh_modal_pagosAlexia');
Route::post('devengados/upload_pagoA', 'DevengadosController@upload_pagoAlexia')->name('pagocarga.upload_pagoAlexia');
Route::post('devengados/carga_notasCreditos', 'DevengadosController@upload_notasCreditos')->name('devenotas.upload_notasCreditos');
Route::post('devengados/refresh_notasCreditos_tmp', 'DevengadosController@refresh_modal_notasCreditos')->name('devenotas.refresh_modal_notasCreditos');
Route::post('devengados/upload_notasCreditos', 'DevengadosController@upload_notas')->name('notascarga.upload_notas');
Route::get('devengados/administracion-nota', 'DevengadosController@lista_grupo_nota_credito')->name('notaadmin.load_gruponota');
Route::post('devengados/administracion-modalnota', 'DevengadosController@load_modal_nota_credito')->name('notaadmin.load_modalnota');
//chinitos
Route::post('devengados/upload_ose', 'DevengadosController@upload_comprobantes_ose')->name('osecarga.upload_ose');
Route::post('devengados/refresh_comprobanteOse_tmp', 'DevengadosController@refresh_modal_comprobanteOse')->name('osecarga.refresh_modal_comprobanteOse');
Route::post('devengados/upload_comprobanteOse', 'DevengadosController@registrar_comprobanteOse')->name('osecarga.registrar_comprobanteOse');
Route::get('devengados/administracion-ose', 'DevengadosController@lista_grupo_comprobante_ose')->name('oseadmin.load_grupocomprobanteOse');
Route::post('devengados/administracion-modalcomprobanteOse', 'DevengadosController@load_modal_comprobante_ose')->name('oseadmin.load_modalcomprobanteOse');


/* * ************contabilidad rutas************* */
Route::post('upload0', 'AjaxUploadController@action0')->name('ajaxupload.action0');
Route::post('upload', 'AjaxUploadController@action')->name('ajaxupload.action');
Route::post('upload2', 'AjaxUploadController@action2')->name('ajaxupload.action2');
Route::get('genera_reporte', 'ContabilidadController@reporte_contabilidad');
Route::post('contabilidad/modalModalsubirArchivo', 'ContabilidadController@modalsubirArchivo')->name('modal.subirArchivo');
Route::post('contabilidad/subirArchivos', 'ContabilidadController@subirArchivos')->name('image.upload');
Route::get('genera_eecc_banco', 'ContabilidadController@reporte_eecc_banco');
Route::post('contabilidad/modalModalinfoValidada', 'ContabilidadController@modalinfoValidada')->name('modal.infoValidada');
Route::post('subir_info', 'ContabilidadController@subirInformacion')->name('modal.subir_info');

Route::post('btn_ea', 'ContabilidadController@verRegistrosea')->name('ver.registros_ea');
Route::post('btn_pf', 'ContabilidadController@verRegistropf')->name('ver.registros_pf');
Route::get('genera_reporte_devengado', 'ContabilidadController@reporte_contabilidad_devengado');

Route::get('genera_reporte_concar', 'ContabilidadController@reporte_concar');

Route::post('uploadCo', 'AjaxUploadController@actionCo')->name('ajaxupload.actionCo');

#Devengados concar
Route::post('uploadCoDeve', 'AjaxUploadController@actionCoDevengados')->name('ajaxupload.actionCoDeve');
Route::get('genera_reporte_concar_devengados', 'ContabilidadController@reporte_concar_devengados');

#Notas de credito concar
Route::post('uploadCoNotaC', 'AjaxUploadController@actionCoNotasCredito')->name('ajaxupload.actionCoNotasCredito');
Route::get('genera_reporte_concar_notas_credito', 'ContabilidadController@reporte_concar_notas_credito');

#Chinita
Route::post('uploadCoDeve', 'AjaxUploadController@actionCoDevengados')->name('ajaxupload.actionCoDeve');
Route::get('genera_reporte_concar_devengados', 'ContabilidadController@reporte_concar_devengados');

Route::post('uploadCoNotaC', 'AjaxUploadController@actionCoNotasCredito')->name('ajaxupload.actionCoNotasCredito');
Route::get('genera_reporte_concar_notas_credito', 'ContabilidadController@reporte_concar_notas_credito');

#Notas de debito concar
Route::post('uploadCoNotaD', 'AjaxUploadController@actionCoNotasDebito')->name('ajaxupload.actionCoNotasDebito');
Route::get('genera_reporte_concar_notas_debito', 'ContabilidadController@reporte_concar_notas_debito');

#Facturas concar
Route::post('uploadCoFactura', 'AjaxUploadController@actionCoFacturas')->name('ajaxupload.actionCoFacturas');
Route::get('genera_reporte_concar_facturas', 'ContabilidadController@reporte_concar_facturas');

#Becados concar
Route::post('uploadCoBecado', 'AjaxUploadController@actionCoBecados')->name('ajaxupload.actionCoBecados');
Route::get('genera_reporte_concar_becados', 'ContabilidadController@reporte_concar_becados');
