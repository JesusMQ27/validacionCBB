
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Devengados</h4>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_devengadosAlexia" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel" id="excel"  />
                <input type="submit" name="upload" id="uploadAlexia" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="messageAlexia" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_devengadoAlexia" class="table table hover">

            </table>
        </div>
        <span id="result_devengadoAlexia_mensaje"></span>
    </div>
</div>
<script type="text/javascript">
    $("#carga_devengadosAlexia").on('submit', function (e) {
        e.preventDefault();
        let subhtml;
        $.ajax({
            type: 'post',
            url: "{{route('devecarga.upload_devengadosAlexia')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (objeto) {
                $("#messageAlexia").removeClass();
                $("#messageAlexia").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#result_devengadoAlexia").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#uploadAlexia").attr('disabled', true);
                $("#result_devengadoAlexia_mensaje").html("");
            },
            success: function (data) {
                $("#result_devengadoAlexia").html("");
                $("#uploadAlexia").attr('disabled', false);
                $("#messageAlexia").css("display", "block");
                $("#messageAlexia").html(data.message);
                $("#messageAlexia").addClass(data.class_name);
                $("#carga_devengadosAlexia #excel").val("");
                if (Object.keys(data.html).length > 0) {//error
                    $("#result_devengadoAlexia").html(data.html);
                    $("#result_devengadoAlexia_mensaje").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");

                } else {//succes
                    $("#result_devengadoAlexia_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                    $("#modal_subir_devengados").html("<button class='btn btn-primary' id='subir_devengados_tmp' onclick='subir_devengados_tmp()'>Subir Devengados</button>");
                }
                $("#result_devengadoAlexia_mensaje").append("<div ><button id='btndlmt' onclick='modal2_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                if (Object.keys(data.modal).length > 0) {//modal con carga temporal
                    $("#modal-title2").html("Carga Temporal");
                    $("#modal-body2").html(data.modal);
                    $("#myModal2").modal('show');
                }
            }
        }).done(function () {
            setTimeout(function () {
                $("#messageAlexia").css("display", "none");
            }, 3000)
        }).fail(function () {
            /*alert("algo ah ocurrido");
             window.parent.location.reload();*/
        });
    })
    function reload_modaltmpdeve() {
        $.ajax({
            type: 'post',
            url: "{{route('devecarga.refresh_modal_devengadosAlexia')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {

            }, beforeSend: function (xhr) {
                $("#modal_devengados_tmp").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...")
            }, success: function (data, textStatus, jqXHR) {
                if (Object.keys(data.modal).length > 0) {
                    $("#modal_devengados_tmp").html(data.modal);
                }
            }
        })
    }
    function modal_devengadoAlexia(id_devengado) {
        $.ajax({
            type: 'post',
            url: '{{route("devecarga.modal_cargaAlexia")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_devengado: id_devengado,
            }, beforeSend: function (xhr) {
                $("#modal-footer-content").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modal-title").html(data.head);
                $("#modal-body").html(data.body);
            }
        }).done(function () {
            $("#myModal").modal('show');
        })
    }
    function update_devengadoAlexia(id_devengado) {
        let deve_fecha_emicar = $.trim($("#deve_fecha_emicar").val());
        let deve_fecha_venc = $.trim($("#deve_fecha_venc").val());
        let deve_fecha = $.trim($("#deve_fecha").val());
        let deve_emision = $.trim($("#deve_emision").val());
        let deve_grado = $.trim($("#deve_grado").val());
        let deve_boleta = $.trim($("#deve_boleta").val());
        let deve_dni = $.trim($("#deve_dni").val());
        let deve_alumno = $.trim($("#deve_alumno").val());
        let deve_concepto = $.trim($("#deve_concepto").val());
        let deve_serie_ticke = $.trim($("#deve_serie_ticke").val());
        let deve_dscto = $.trim($("#deve_dscto").val());
        let deve_base_imp = $.trim($("#deve_base_imp").val());
        let deve_igv = $.trim($("#deve_igv").val());
        let deve_monto = $.trim($("#deve_monto").val());
        let deve_monto_cancelado = $.trim($("#deve_monto_cancelado").val());
        let deve_tc = $.trim($("#deve_tc").val());
        let deve_tipo = $.trim($("#deve_tipo").val());
        let deve_centro = $.trim($("#deve_centro").val());
        let deve_estado_tipo = $.trim($("#deve_estado_tipo").val());
        let deve_banco = $.trim($("#deve_banco").val());


        $.ajax({
            type: 'post',
            url: '{{route("devecarga.update_devengadoAlexia")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_devengado: id_devengado,
                deve_fecha_emicar: deve_fecha_emicar,
                deve_fecha_venc: deve_fecha_venc,
                deve_fecha_pag: deve_fecha,
                deve_fecha: deve_emision,
                deve_grado: deve_grado,
                deve_boleta: deve_boleta,
                deve_dni: deve_dni,
                deve_alumno: deve_alumno,
                deve_concepto: deve_concepto,
                deve_serie_ticke: deve_serie_ticke,
                deve_dscto: deve_dscto,
                deve_base_imp: deve_base_imp,
                deve_igv: deve_igv,
                deve_monto: deve_monto,
                deve_monto_cancelado: deve_monto_cancelado,
                deve_tc: deve_tc,
                deve_tipo: deve_tipo,
                deve_centro: deve_centro,
                deve_estado_tipo: deve_estado_tipo,
                deve_banco: deve_banco,
            }, beforeSend: function (xhr) {
                $("#update_devengadoAlexia").attr('disabled', true)
            }, success: function (data, textStatus, jqXHR) {
                alert(data.mensaje)
                if (data.tipo == "1") {
                    $("#result_devengadoAlexia tbody #" + id_devengado).remove();
                    $("#modal-footer button").click();
                    if ($("#result_devengadoAlexia >tbody >tr").length == 0) {
                        $("#result_devengadoAlexia").html("");
                        $("#result_devengadoAlexia_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>");
                        $("#result_devengadoAlexia_mensaje").append("<div ><button id='btndlmt' onclick='modal2_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                        $("#modal_subir_devengados").html("<button class='btn btn-primary' id='subir_devengados_tmp' onclick='subir_devengados_tmp()'>Subir Devengados</button>");
                    }
                } else if (data.tipo == "2") {
                    $("#update_devengadoAlexia").attr('disabled', false)
                }
            }
        }).fail(function (e) {
            alert("Algo ah ocurrido");
            location.reload();
        })
    }

    function subir_devengados_tmp() {
        if (confirm("Solo se insertaran registros NUEVOS,debido a ello, puede que las cantidades no sean iguales.\n Esta accion no se puede deshacer")) {
            $.ajax({
                type: 'post',
                url: '{{route("devecarga.upload_devengadoAlexia")}}',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                }, beforeSend: function (xhr) {
                    $("#subir_devengados_tmp").attr('disabled', true);
                }, success: function (data, textStatus, jqXHR) {
                    $("#btndlmt").attr('disabled', true);
                    setTimeout(function () {
                        alert("Carga subida correctamente");
                        $("#subir_devengados_tmp").attr('disabled', false);
                        $("#modal-footer2 button").click();
                    }, 3000)
                }

            }).fail(function (e) {
                alert("error al insertar data");
                $("#subir_devengados_tmp").attr('disabled', false);

            });
        }

    }
</script>
