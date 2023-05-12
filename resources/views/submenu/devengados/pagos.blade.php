
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Pagos</h4>
        <span style="float: left;margin-top: 7px;font-size: 14px;font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;<a href="../../plantillas/plantilla_pagos_cbb.xlsx">Descargar plantilla</a></span>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_pagos" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel_pagos" id="excel_pagos"  />
                <input type="submit" name="upload_pagos" id="upload_pagos" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="message_pagos" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_pago" class="table table hover">

            </table>
        </div>
        <span id="result_pago_mensaje"></span>
    </div>
</div>
<script type="text/javascript">
    $("#carga_pagos").on('submit', function (e) {
        let subhtml;
        e.preventDefault();
        $.ajax({
            url: "{{route('devepagos.upload_pagos')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $("#result_pago").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#message_pagos").css("display", "block");
                $("#message_pagos").html(data.message);
                $("#message_pagos").addClass(data.class_name);
                $("#carga_pagos #excel_pagos").val("");
                if (Object.keys(data.html).length > 0) {
                    $.each(data.html, function (i, e) {
                        subhtml += "<tr id='" + e.id_pago + "'>"
                                + "<td>" + e.pago_grado + "</td>"
                                + "<td>" + e.pago_emision + "</td>"
                                + "<td>" + e.pago_dni + "</td>"
                                + "<td>" + e.pago_boleta + "</td>"
                                + "<td>" + e.pago_alumno + "</td>"
                                + "<td>" + e.pago_cuota + "</td>"
                                + "<td>" + e.pago_monto + "</td>"
                                + "<td>" + e.pago_fecha + "</td>"
                                + "<td><button class='btn btn-primary' onclick='modal_pago(" + e.id_pago + ")'><i class='fas fa-search-plus'></i></button></td>"
                                + "</tr>";
                    });
                    $("#result_pago").html("<thead>"
                            + "<th>GRADO</th>"
                            + "<th>FECHA DE EMISION</th>"
                            + "<th>DNI</th>"
                            + "<th>N. BOLETA</th>"
                            + "<th>APELLIDOS Y NOMBRES</th>"
                            + "<th>CUOTAS</th>"
                            + "<th>MONTO</th>"
                            + "<th>FECHA DE PAGO</th>"
                            + "<th>ACCION</th>"
                            + "</thead>"
                            + "<tbody>" + subhtml + "</tbody>");
                    $("#result_pago_mensaje").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");
                } else {
                    $("#result_pago_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                }

            }
        }).done(function () {
            $("#carga_pagos #upload_pagos").attr('disabled', false);
            setTimeout(function () {
                $("#message_pagos").css("display", "none");
            }, 3000)
        }).fail(function () {
            /* alert("algo ah ocurrido");
             window.parent.location.reload();*/
        });
    }
    )
    function modal_pago(id_pago) {
        $.ajax({
            type: 'post',
            url: '{{route("devepago.modal_pago")}}',
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
    function update_pago(id_pago) {
        let pago_grado = $("#pago_grado").val();
        let pago_emision = $("#pago_emision").val();
        let pago_dni = $("#pago_dni").val();
        let pago_boleta = $("#pago_boleta").val();
        let pago_alumno = $("#pago_alumno").val();
        let pago_cuota = $("#pago_cuota").val();
        let pago_monto = $("#pago_monto").val();
        let pago_fecha = $("#pago_fecha").val();
        $.ajax({
            type: 'post',
            url: '{{route("devepago.update_pago")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_pago: id_pago,
                pago_grado: pago_grado,
                pago_emision: pago_emision,
                pago_dni: pago_dni,
                pago_boleta: pago_boleta,
                pago_alumno: pago_alumno,
                pago_cuota: pago_cuota,
                pago_monto: pago_monto,
                pago_fecha: pago_fecha
            }, beforeSend: function (xhr) {
                $("#update_pago").attr('disabled', true)
            }, success: function (data, textStatus, jqXHR) {
                alert(data.mensaje)

                if (data.tipo == "1") {
                    $("#result_pago tbody #" + id_pago).remove();
                    $("#modal-footer button").click();
                    if ($("#result_pago >tbody >tr").length == 0) {
                        $("#result_pago").html("");
                        $("#result_pago_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                    }
                } else if (data.tipo == "2") {
                    $("#update_pago").attr('disabled', false)
                }
            }
        })
    }
</script>