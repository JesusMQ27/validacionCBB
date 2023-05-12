<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- Custom fonts for this template-->
        <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"/>
        <link href="../../css/sb-admin-2.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="../../vendor/daterangepicker/daterangepicker.css"/>
        <link href="../../css/tabs.css" rel="stylesheet"/>
        <link href="../../vendor/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="../../css/carga.css" rel="stylesheet"/>

        <script src="../../vendor/jquery/jquery.min.js"></script>
        <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="../../vendor/jquery-confirm-master/js/jquery-confirm.js"></script>
        <link rel="stylesheet" href="../../vendor/jquery-confirm-master/css/jquery-confirm.css"/>

        <!-- daterangepicker -->
        <script src="../../vendor/daterangepicker/moment.min.js"></script>
        <script src="../../vendor/daterangepicker/daterangepicker.js"></script>
        <script src="../../vendor/fileinput/fileinput.js" type="text/javascript"></script>
        <script src="../../vendor/fileinput/theme.js" type="text/javascript"></script>
        <script src="../../vendor/fileinput/popper.min.js" type="text/javascript"></script>
        <script src="../../vendor/fileinput/es.js" type="text/javascript"></script>

        <link rel="stylesheet" href="../../css/estilos.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            @foreach($data as $fila)
                            @if($fila->nombre==$href)
                            <a class="nav-item nav-link active" id="nav-home-tab" onclick="carga_contenido('{{($fila->nombre)}}', '{{strtolower($fila->menuPa_tittle)}}','{{($fila->id)}}', this)" data-toggle="tab" href="#{{($fila->nombre)}}" role="tab" aria-controls="nav-home" aria-selected="true">{{($fila->titulo)}}</a><!-- Lupita -->
                            @else
                            <a class="nav-item nav-link" id="nav-home-tab" onclick="carga_contenido('{{($fila->nombre)}}', '{{strtolower($fila->menuPa_tittle)}}','{{($fila->id)}}', this)" data-toggle="tab" href="#{{($fila->nombre)}}" role="tab" aria-controls="nav-home" aria-selected="true">{{($fila->titulo)}}</a><!-- Lupita -->
                            @endif
                            @endforeach
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                    </div>
                </div>
            </div>
        </div>
        <div id="modalSubirArchivos" class="modal fade" role="dialog" aria-labelledby="modalSubirArchivos" aria-hidden="true" data-backdrop='static'>
            <div class="modal-dialog modal-xl">
                <div class="modal-content" id="">
                    <div class="modal-header" id="modal-header" style="background-color: #4e73df;color: #fff;">
                        <h4 class="modal-title"><i class="fa fa-file"></i>&nbsp;&nbsp;Subir archivos de SCOTIABANK, BBVA, BCP e INTERBANK</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;
                        </button>
                    </div>
                    <div class="modal-body" id="divTexto">
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnGeneraExcel_archivos" class="btn btn-success" onclick="generar_excel_de_txt();">Generar Excel</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#5B5B5F;color:#fff;">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modalValidarInfo" class="modal fade" role="dialog" aria-labelledby="modalValidarInfo" aria-hidden="true" data-backdrop='static'>
            <div class="modal-dialog modal-xl" style="max-width: 98%;margin: 1.75rem auto;">
                <div class="modal-content" id="">
                    <div class="modal-header" id="modal-header" style="background-color: #4e73df;color: #fff;">
                        <h4 class="modal-title"><i class="fa fa-file"></i>&nbsp;&nbsp;Informaci&oacute;n validada</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
                                    <label id="cant_ec_alexia" style="font-weight: bold;"></label>
                                </div>
                                <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
                                    <label id="cant_alexia_factu" style="font-weight: bold;"></label>
                                </div>
                            </div>
                            <div class="row" style="float: right;">
                                <span style="height: 20px;width: 20px;background-color: black;border-radius: 50%;display:inline-block;"></span>&nbsp; Registros nuevos &nbsp;&nbsp;&nbsp;
                                <span style="height: 20px;width: 20px;background-color: red;border-radius: 50%;display:inline-block;"></span>&nbsp; Registros ya encontrados &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-success" id="subir_info" style="color:#fff;" onclick="subir_informacion();">Subir Informaci&oacute;n</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#5B5B5F;color:#fff;margin-left: 15px;">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="modal_reg_eecc_alexia" class="modal fade" role="dialog" aria-labelledby="modal_reg_eecc_alexia" aria-hidden="true" data-backdrop='static'>
            <div class="modal-dialog modal-xl" style="max-width: 98%;margin: 1.75rem auto;">
                <div class="modal-content" id="">
                    <div class="modal-header" id="modal-header" style="background-color: #4e73df;color: #fff;">
                        <h4 class="modal-title"><i class="fa fa-file"></i>&nbsp;&nbsp;Ver registros EECC vs ALEXIA</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#5B5B5F;color:#fff;">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal_reg_alexia_facturacion" class="modal fade" role="dialog" aria-labelledby="modal_reg_alexia_facturacion" aria-hidden="true" data-backdrop='static'>
            <div class="modal-dialog modal-xl" style="max-width: 98%;margin: 1.75rem auto;">
                <div class="modal-content" id="">
                    <div class="modal-header" id="modal-header" style="background-color: #4e73df;color: #fff;">
                        <h4 class="modal-title"><i class="fa fa-file"></i>&nbsp;&nbsp;Ver registros ALEXIA vs FACTURACI&Oacute;N</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#5B5B5F;color:#fff;">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../js/data.js"></script>
        <script src="../../js/contabilidad.js"></script>

        <script type="text/javascript">
                                    $(document).ready(function(){
                                    $("#nav-tab a").each(function(){
                                    if ($(this).hasClass('active')) {
                                    $(this).click();
                                    }
                                    });
                                    });
                                    function subir_informacion() {


                                    $("#subir_info").attr("disabled", true);
                                    $.confirm({
                                    title: 'Confirmacion!',
                                            content: '\u00bfDeseas grabar la informaci\u00f3n?',
                                            columnClass: 'small',
                                            typeAnimated: true,
                                            offsetTop: 5,
                                            type: 'red',
                                            offsetBottom: 40000,
                                            //autoClose: 'cancel|5000',
                                            buttons: {
                                            confirm: {
                                            text: ' Aceptar ',
                                                    btnClass: 'btn-red',
                                                    action: function () {
                                                    var fecha_inicio = $("#fechaFact1").val();
                                                    var fecha_fin = $("#fechaFact2").val();
                                                    var cantEA = $("#txtCantEA").val();
                                                    var cantPF = $("#txtCantPF").val();
                                                    $("#cargar").append("");
                                                    $("#cargar").html("");
                                                    $("#cargar").removeClass("cargita");
                                                    $.ajax({
                                                    url: "{{route('modal.subir_info')}}",
                                                            dataType: "html",
                                                            type: "POST",
                                                            data: {
                                                            fecha_inicio: fecha_inicio,
                                                                    fecha_fin: fecha_fin,
                                                                    cantEA: cantEA,
                                                                    cantPF: cantPF
                                                            },
                                                            beforeSend: function (objeto) {
                                                            $("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
                                                            $("#cargar").addClass("cargita");
                                                            },
                                                            headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                            },
                                                            error: function (xhr, ajaxOptions, thrownError) {
                                                            //$("#contentMenu").html(xhr.responseText);
                                                            },
                                                            success: function (datos) {
                                                            $("#cargar").append("");
                                                            $("#cargar").html("");
                                                            $("#cargar").removeClass("cargita");
                                                            $("#subir_info").attr("disabled", false);
                                                            exito(datos);
                                                            setTimeout(function(){
                                                            $("#modalValidarInfo").modal('hide');
                                                            }, 3500);
                                                            setTimeout(function(){
                                                            location.reload();
                                                            }, 3800);
                                                            }
                                                    });
                                                    }
                                            },
                                                    cancel: {
                                                    text: ' Cancelar ',
                                                            action: function () {
                                                            $("#subir_info").attr("disabled", false);
                                                            }
                                                    }
                                            }
                                    });
                                    }

                                    function exito(cadena){
                                    $.alert({
                                    title: 'Mensaje',
                                            content: '<h5>' + cadena + '</h5>',
                                            typeAnimated: true,
                                            columnClass: 'medium',
                                            type: 'green',
                                            offsetTop: 5,
                                            offsetBottom: 40000,
                                            autoClose: 'ok|3000'
                                    });
                                    }

        </script>
    </body>
</html>
