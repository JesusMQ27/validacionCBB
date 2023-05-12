
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Pagos</h4>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_pagosAlexia" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel_pagos" id="excel_pagosAlexia"  />
                <input type="submit" name="upload_pagos" id="upload_pagosAlexia" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="message_pagosAlexia" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_pagoAlexia" class="table table hover">

            </table>
        </div>
        <span id="result_pago_mensajeAlexia"></span>
    </div>
</div>

<script type="text/javascript">
    $("#carga_pagosAlexia").on('submit', function (e) {
        let subhtml;
        e.preventDefault();
        $.ajax({
            url: "{{route('devepagos.upload_pagosAlexia')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $("#message_pagosAlexia").removeClass();
                $("#result_pagoAlexia").html("");
                $("#result_pago_mensajeAlexia").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#message_pagosAlexia").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#upload_pagosAlexia").attr('disabled', true);
            }, success: function (data, textStatus, jqXHR) {
                $("#upload_pagosAlexia").attr('disabled', false);
                $("#message_pagosAlexia").css("display", "block");
                $("#message_pagosAlexia").html(data.message);
                $("#message_pagosAlexia").addClass(data.class_name);
                $("#carga_pagosAlexia #excel_pagosAlexia").val("");
                if (Object.keys(data.html).length > 0) {
                    $("#result_pagoAlexia").html(data.html);
                    $("#result_pago_mensajeAlexia").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");
                } else {
                    $("#result_pago_mensajeAlexia").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                }
                $("#result_pago_mensajeAlexia").append("<div ><button id='btnplmt' onclick='modal2_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                if (Object.keys(data.modal).length > 0) {//modal con carga temporal
                    $("#modal-title2").html("Carga Temporal");
                    $("#modal-body2").html(data.modal);
                    $("#myModal2").modal('show');
                }
            }

        }).done(function () {
            $("#carga_pagosAlexia #upload_pagos").attr('disabled', false);
            setTimeout(function () {
                $("#message_pagosAlexia").css("display", "none");
            }, 3000)
        }).fail(function () {
            /* alert("algo ah ocurrido");
             window.parent.location.reload();*/
        });
    })
    function reload_modaltmppago() {
        $.ajax({
            type: 'post',
            url: "{{route('devepago.refresh_modal_pagosAlexia')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {

            }, beforeSend: function (xhr) {
                $("#modal_pagos_tmp").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...")
            }, success: function (data, textStatus, jqXHR) {
                if (Object.keys(data.modal).length > 0) {
                    $("#modal_pagos_tmp").html(data.modal);
                }
            }
        })
    }
    function modal_pagoAlexia(id_pago) {
        $.ajax({
            type: 'post',
            url: '{{route("devepago.modal_pagoAlexia")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_pago: id_pago
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
    function update_pagoAlexia(id_pago) {
        let pago_fecha_emicar = $.trim($("#pago_fecha_emicar").val());
        let pago_fecha_venc = $.trim($("#pago_fecha_venc").val());
        let pago_fecha = $.trim($("#pago_fecha").val());
        let pago_emision = $.trim($("#pago_emision").val());
        let pago_grado = $.trim($("#pago_grado").val());
        let pago_boleta = $.trim($("#pago_boleta").val());
        let pago_dni = $.trim($("#pago_dni").val());
        let pago_alumno = $.trim($("#pago_alumno").val());
        let pago_concepto = $.trim($("#pago_concepto").val());
        let pago_serie_ticke = $.trim($("#pago_serie_ticke").val());
        let pago_dscto = $.trim($("#pago_dscto").val());
        let pago_base_imp = $.trim($("#pago_base_imp").val());
        let pago_igv = $.trim($("#pago_igv").val());
        let pago_monto = $.trim($("#pago_monto").val());
        let pago_monto_cancelado = $.trim($("#pago_monto_cancelado").val());
        let pago_tc = $.trim($("#pago_tc").val());
        let pago_tipo = $.trim($("#pago_tipo").val());
        let pago_centro = $.trim($("#pago_centro").val());
        let pago_estado_tipo = $.trim($("#pago_estado_tipo").val());
        let pago_banco = $.trim($("#pago_banco").val());
        $.ajax({
            type: 'post',
            url: '{{route("devepago.update_pagoAlexia")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_pago: id_pago,
                pago_fecha_emicar: pago_fecha_emicar,
                pago_fecha_venc: pago_fecha_venc,
                pago_fecha: pago_fecha,
                pago_emision: pago_emision,
                pago_grado: pago_grado,
                pago_boleta: pago_boleta,
                pago_dni: pago_dni,
                pago_alumno: pago_alumno,
                pago_concepto: pago_concepto,
                pago_serie_ticke: pago_serie_ticke,
                pago_dscto: pago_dscto,
                pago_base_imp: pago_base_imp,
                pago_igv: pago_igv,
                pago_monto: pago_monto,
                pago_monto_cancelado: pago_monto_cancelado,
                pago_tc: pago_tc,
                pago_tipo: pago_tipo,
                pago_centro: pago_centro,
                pago_estado_tipo: pago_estado_tipo,
                pago_banco: pago_banco

            }, beforeSend: function (xhr) {
                $("#update_pagoAlexia").attr('disabled', true)
            }, success: function (data, textStatus, jqXHR) {
                alert(data.mensaje)
                if (data.tipo == "1") {
                    $("#result_pagoAlexia tbody #" + id_pago).remove();
                    $("#modal-footer button").click();
                    if ($("#result_pagoAlexia >tbody >tr").length == 0) {
                        $("#result_pagoAlexia").html("");
                        $("#result_pago_mensajeAlexia").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                        $("#result_pago_mensajeAlexia").append("<div ><button id='btnplmt' onclick='modal2_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                        $("#modal_subir_pagos").html("<button class='btn btn-primary' onclick='subir_pagos_tmp()'>Subir Pagos</button></button>");
                    }
                } else if (data.tipo == "2") {
                    $("#update_pagoAlexia").attr('disabled', false)
                }
            }
        })
    }
    function subir_pagos_tmp() {
        if (confirm("Solo se insertaran los registros NUEVOS y DEVENGADOS, \n debido a ello puede que las cantidades no sean iguales.\n Esta accion no se puede deshacer")) {
            $.ajax({

                type: 'post',
                url: '{{route("pagocarga.upload_pagoAlexia")}}',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                }, beforeSend: function (xhr) {
                    $("#subir_pagos_tmp").attr('disabled', true);
                }, success: function (data, textStatus, jqXHR) {
                    $("#btnplmt").attr('disabled', true);
                    setTimeout(function () {
                        alert("Carga subida correctamente");
                        $("#subir_pagos_tmp").attr('disabled', false);
                        $("#modal-footer2 button").click();
                    }, 3000)
                }

            }).fail(function (e) {
                alert("error al insertar data");
                $("#subir_pagos_tmp").attr('disabled', false);
            });
        }
    }

</script>