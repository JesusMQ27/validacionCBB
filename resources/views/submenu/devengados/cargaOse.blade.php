<!-- chinitos-->
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Excel de OSE</h4>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_ose" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel_ose" id="excel_ose"  />
                <input type="submit" name="upload_ose" id="upload_ose" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="message_cargaOse" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_ose" class="table table hover">

            </table>
        </div>
        <span id="result_ose_mensaje"></span>
    </div>
</div>
<div class="container-fluid" style="margin-top: 10px;">
    <div class="row">
        <div class="col-12 text-right" style="padding-bottom: 10px;">
            <button onclick="loadCargaOse()" class="btn"><i class="fa fa-sync"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12" style="margin-bottom: 1.5rem;">
            <div class="card">
                <h4 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
                    Lista de Comprobantes de la OSE
                </h4>
                <div class="card-body table-responsive" id='loadDataOse' style="max-height: 280px;overflow: auto;">

                </div>
            </div>
        </div>         
    </div>
</div>
<script type="text/javascript">
    $("#carga_ose").on('submit', function (e) {
        let subhtml;
        e.preventDefault();
        $.ajax({
            url: "{{route('osecarga.upload_ose')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $("#message_cargaOse").removeClass();
                $("#result_ose").html("");
                $("#result_ose_mensaje").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#message_cargaOse").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#upload_ose").attr('disabled', true);
            }, success: function (data, textStatus, jqXHR) {
                $("#upload_ose").attr('disabled', false);
                $("#message_cargaOse").css("display", "block");
                $("#message_cargaOse").html(data.message);
                $("#message_cargaOse").addClass(data.class_name);
                $("#carga_ose #excel_ose").val("");

                if (Object.keys(data.html).length > 0) {
                    $("#result_ose").html(data.html);
                    $("#result_ose_mensaje").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");
                } else {
                    $("#result_ose_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>");
                }
                $("#result_ose_mensaje").append("<div ><button id='btnplmt' onclick='modal7_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                if (Object.keys(data.modal).length > 0) {//modal con carga temporal
                    const errorCorrelativo = data.modal.indexOf('ERROR EN CORRELATIVO');
                    $("#modal-titleModalOse").html("Carga Temporal");
                    $("#modal-bodyModalOse").html(data.modal);
                    /*if (errorCorrelativo !== -1) {
                        $("#subir_comprobantesOSE_tmp").attr("disabled", true);
                    } else {
                        $("#subir_comprobantesOSE_tmp").removeAttr("disabled");
                    }*/
                    $("#myModal7").modal('show');
                }
            }

        }).done(function () {
            $("#carga_ose #uploadOse").attr('disabled', false);
            setTimeout(function () {
                $("#message_cargaOse").css("display", "none");
            }, 3000);
        }).fail(function () {
        });
    });

    function reload_modaltmpcomprobanteOse() {
        $.ajax({
            type: 'post',
            url: "{{route('osecarga.refresh_modal_comprobanteOse')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {

            }, beforeSend: function (xhr) {
                $("#modal_comprobanteOse_tmp").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            }, success: function (data, textStatus, jqXHR) {
                if (Object.keys(data.modal).length > 0) {
                    const errorCorrelativo = data.modal.indexOf('ERROR EN CORRELATIVO');
                    $("#modal_comprobanteOse_tmp").html(data.modal);

                }
            }
        });
    }

    function subir_comprobantesOse_tmp() {
        if (confirm("Solo se insertaran los registros NUEVOS \n")) {
            $.ajax({
                type: 'post',
                url: '{{route("osecarga.registrar_comprobanteOse")}}',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                }, beforeSend: function (xhr) {
                    $("#subir_comprobanteOse_tmp").attr('disabled', true);
                }, success: function (data, textStatus, jqXHR) {
                    const respuesta = $.trim(data) + '';
                    if (respuesta === '1') {
                        $("#btnplmt").attr('disabled', true);
                        setTimeout(function () {
                            alert("Carga subida correctamente");
                            $("#subir_comprobanteOse_tmp").attr('disabled', false);
                            $("#modal-footer-nota button").click();
                            $("#myModal7").modal('hide');
                        }, 3000);
                        setTimeout(function () {
                            location.reload();
                        }, 3500);
                    } else {
                        alert("No existen registros nuevos para cargar.");
                    }
                }

            }).fail(function (e) {
                alert("error al insertar data");
                $("#subir_comprobanteOse_tmp").attr('disabled', false);
            });
        }
    }

    load_grupo_comprobantesOse();
    function loadCargaOse() {
        load_grupo_comprobantesOse();
    }

    function load_grupo_comprobantesOse() {
        $.ajax({
            type: 'get',
            url: '{{route("oseadmin.load_grupocomprobanteOse")}}',
            beforeSend: function (xhr) {
                $("#loadDataOse").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            }, success: function (data, textStatus, jqXHR) {
                $("#loadDataOse").html(data);
            }
        });
    }

    function load_comprobantes_ose_modal(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("oseadmin.load_modalcomprobanteOse")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_grupo: id_grupo
            }, beforeSend: function (xhr) {
                $("#modal-footer-content").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modal-title").html(data.head);
                $("#modal-body").html(data.body);
                $("#modal-footer-content").html(data.footer);
            }
        }).done(function () {
            $("#myModal").modal('show');
            var tlmd1 = 1;
            var tlmd2 = 1;
            var tlmd3 = 1;
            var tlmd4 = 1;
            var tlmd5 = 1;
            var tlmd6 = 1;
            var tlmd7 = 1;
            var tlmd8 = 1;
            var tlmd9 = 1;
            var tlmd10 = 1;
            var tlmd11 = 1;
            var tlmd12 = 1;
            var tlmd13 = 1;
            var tlmd14 = 1;
            var tlmd15 = 1;
            var tlmd16 = 1;
            var tlmd17 = 1;
            var tlmd18 = 1;
            var tlmd19 = 1;
            let table = "modal_table";
            ordenarth("#tlmd1", tlmd1, table);
            ordenarth("#tlmd2", tlmd2, table);
            ordenarth("#tlmd3", tlmd3, table);
            ordenarth("#tlmd4", tlmd4, table);
            ordenarth("#tlmd5", tlmd5, table);
            ordenarth("#tlmd6", tlmd6, table);
            ordenarth("#tlmd7", tlmd7, table);
            ordenarth("#tlmd8", tlmd8, table);
            ordenarth("#tlmd9", tlmd9, table);
            ordenarth("#tlmd10", tlmd10, table);
            ordenarth("#tlmd11", tlmd11, table);
            ordenarth("#tlmd12", tlmd12, table);
            ordenarth("#tlmd13", tlmd13, table);
            ordenarth("#tlmd14", tlmd14, table);
            ordenarth("#tlmd15", tlmd15, table);
            ordenarth("#tlmd16", tlmd16, table);
            ordenarth("#tlmd17", tlmd17, table);
            ordenarth("#tlmd18", tlmd18, table);
            ordenarth("#tlmd19", tlmd19, table);
        });
    }
</script>