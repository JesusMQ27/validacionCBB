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
        <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="../../vendor/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">
        <!-- Custom styles for this template-->
        <!--<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">-->
        <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
        <link href="../../css/tabs.css" rel="stylesheet">
        <link href="../../css/estilos.css" rel="stylesheet">
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

        <!-- The Modal -->
        <div class="modal fade " id="myModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" style="max-width: 95%;">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header">
                        <h4 class="modal-title" id="modal-title">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body">
                        ...
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer" id="modal-footer">
                        <div class="row">
                            <div class="col-12" id="modal-footer-content">

                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal()">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- The Modal -->
        <div class="modal fade " id="myModal2" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" style="max-width: 95%;">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header2">
                        <h4 class="modal-title" id="modal-title2">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body2">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer" id="modal-footer2">
                        <div class="row">
                            <div class="col-12" id="modal-footer-content2">

                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Anulacion-->
        <div class="modal fade" id="modalAnulacion" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header3">
                        <h4 class="modal-title" id="modal-title3">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal3()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body3">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="" id="modal-footer3">
                        <div class="row col-12 col-sm-12 col-md-12">
                            <div class="col-9 col-sm-9 col-md-9" id="modal-footer-content3">
                            </div>
                            <div class="col-3 col-sm-3 col-md-3">
                                <button type="button" class="btn btn-success" onclick="guardar_nota();">Guardar</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal3()">Cerrar</button>
                            </div>
                        </div>
                    </div><br/>
                </div>
            </div>
        </div>
        <!-- Modal Detalle Anulacion-->
        <div class="modal fade" id="modalDetalleAnulacion" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header4">
                        <h4 class="modal-title" id="modal-title4">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal4()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body4">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="" id="modal-footer4">
                        <div class="row col-12 col-sm-12 col-md-12">
                            <div class="col-9 col-sm-9 col-md-9" id="modal-footer-content4">
                            </div>
                            <div class="col-3 col-sm-3 col-md-3 text-right">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal4()">Cerrar</button>
                            </div>
                        </div>
                    </div><br/>
                </div>
            </div>
        </div>
        <!-- Modal Editar Anulacion-->
        <div class="modal fade" id="modalEditarAnulacion" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header5">
                        <h4 class="modal-title" id="modal-title5">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal5()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body5">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="" id="modal-footer5">
                        <div class="row col-12 col-sm-12 col-md-12">
                            <div class="col-9 col-sm-9 col-md-8" id="modal-footer-content5">
                            </div>
                            <div class="col-3 col-sm-3 col-md-4 text-right">
                                <button type="button" class="btn btn-success" onclick="editar_nota();">Modificar</button>&nbsp;
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal5()">Cerrar</button>
                            </div>
                        </div>
                    </div><br/>
                </div>
            </div>
        </div>
        <!-- Modal Eliminar Anulacion-->
        <div class="modal fade" id="modalEliminarAnulacion" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header6">
                        <h4 class="modal-title" id="modal-title6">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal6()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-body6">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="" id="modal-footer6">
                        <div class="row col-12 col-sm-12 col-md-12">
                            <div class="col-9 col-sm-9 col-md-8" id="modal-footer-content6">
                            </div>
                            <div class="col-3 col-sm-3 col-md-4 text-right">
                                <button type="button" class="btn btn-success" onclick="eliminar_nota();">Eliminar</button>&nbsp;
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal6()">Cerrar</button>
                            </div>
                        </div>
                    </div><br/>
                </div>
            </div>
        </div>
        <!-- The Modal Nota -->
        <div class="modal fade " id="myModal3" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" style="max-width: 95%;">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header">
                        <h4 class="modal-title" id="modal-titleModal">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-bodyModal">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer" id="modal-footer-nota">
                        <div class="row">
                            <div class="col-12" id="modal-footer-content3">

                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- chinitos-->
        <!-- modal comprobantes Ose-->
        <div class="modal fade " id="myModal7" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" style="max-width: 95%;">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" id="modal-header">
                        <h4 class="modal-title" id="modal-titleModalOse">...</h4>
                        <button  type="button" class="close" data-dismiss="modal" style="position: absolute;right: 50px;margin-right: 10px;font-size: 30px;padding-top: 30px;">&macr;</button>
                        <button onclick="closeModal()" type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body" id="modal-bodyModalOse">
                        ...
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer" id="modal-footer-ose">
                        <div class="row">
                            <div class="col-12" id="modal-footer-content7">

                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../vendor/jquery/jquery.min.js"></script>
        <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../../vendor/daterangepicker/moment.min.js"></script>
        <script src="../../vendor/daterangepicker/daterangepicker.js"></script>
        <script src="../../js/data.js"></script>

        <script type="text/javascript">
                            $(document).ready(function(){
                            $("#nav-tab a").each(function(){
                            if ($(this).hasClass('active')) {
                            $(this).click();
                            }
                            });
                            });
                            function closeModal(){
                            setTimeout(function(){
                            $("#modal-title").html("");
                            $("#modal-body").html("");
                            }, 500)
                            }
                            function modal_load() {
                            $("#myModal").modal('show');
                            }
                            function modal2_load() {
                            $("#myModal2").modal('show');
                            }
                            function modal3_load() {
                            $("#myModal3").modal('show');
                            }
                            function closeModal3(){
                            setTimeout(function(){
                            $("#modalAnulacion .modal-title").html("");
                            $("#modalAnulacion .modal-body").html("");
                            }, 500);
                            }
                            function closeModal4(){
                            setTimeout(function(){
                            $("#modalDetalleAnulacion .modal-title").html("");
                            $("#modalDetalleAnulacion .modal-body").html("");
                            }, 500);
                            }
                            function closeModal5(){
                            setTimeout(function(){
                            $("#modalEditarAnulacion .modal-title").html("");
                            $("#modalEditarAnulacion .modal-body").html("");
                            }, 500);
                            }
                            function closeModal6(){
                            setTimeout(function(){
                            $("#modalEliminarAnulacion .modal-title").html("");
                            $("#modalEliminarAnulacion .modal-body").html("");
                            }, 500);
                            }
                            //chinitos
                            function modal7_load() {
                            $("#myModal7").modal('show');
                            }
                            function closeModal7(){
                            setTimeout(function(){
                            $("#myModal7 .modal-title").html("");
                            $("#myModal7 .modal-body").html("");
                            }, 500);
                            }
        </script>
    </body>
</html>