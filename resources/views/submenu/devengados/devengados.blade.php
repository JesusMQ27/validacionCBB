
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Devengados</h4>
        <span style="float: left;margin-top: 7px;font-size: 14px;font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;<a href="../../plantillas/plantilla_devengados_cbb.xlsx">Descargar plantilla</a></span>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_devengados" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel" id="excel"  />
                <input type="submit" name="upload" id="upload" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="message" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_devengado" class="table table hover">

            </table>
        </div>
        <span id="result_devengado_mensaje"></span>
    </div>
</div>
<script type="text/javascript">
    $("#carga_devengados").on('submit', function (e) {
        e.preventDefault();
        let subhtml;
        $.ajax({
            url: "{{route('devecarga.upload_devengados')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (objeto) {
                $("#carga_devengados #message").removeClass();
                $("#result_devengado").html("");
            },
            success: function (data) {
                $("#message").css("display", "block");
                $("#message").html(data.message);
                $("#message").addClass(data.class_name);
                $("#carga_devengados #excel").val("");
                if (Object.keys(data.html).length > 0) {
                    $.each(data.html, function (i, e) {
                        subhtml += "<tr id='" + e.id_devengado + "'>"
                                + "<td>" + e.deve_grado + "</td>"
                                + "<td>" + e.deve_fecha + "</td>"
                                + "<td>" + e.deve_dni + "</td>"
                                + "<td>" + e.deve_boleta + "</td>"
                                + "<td>" + e.deve_alumno + "</td>"
                                + "<td>" + e.deve_cuota + "</td>"
                                + "<td>" + e.deve_monto + "</td>"
                                + "<td><button class='btn btn-primary' onclick='modal_devengado(" + e.id_devengado + ")'><i class='fas fa-search-plus'></i></button></td>"
                                + "</tr>";
                    });
                    $("#result_devengado").html("<thead>"
                            + "<th>GRADO</th>"
                            + "<th>FECHA DE EMISION</th>"
                            + "<th>DNI</th>"
                            + "<th>N. BOLETA</th>"
                            + "<th>APELLIDOS Y NOMBRES</th>"
                            + "<th>CUOTAS</th>"
                            + "<th>MONTO</th>"
                            + "<th>ACCION</th>"
                            + "</thead>"
                            + "<tbody>" + subhtml + "</tbody>");
                    $("#result_devengado_mensaje").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");
                } else {
                    $("#result_devengado_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                }
            }
        }).done(function () {
            $("#carga_devengados #upload").attr('disabled', false);
            setTimeout(function () {
                $("#message").css("display", "none");
            }, 3000)
        }).fail(function () {
            /*alert("algo ah ocurrido");
             window.parent.location.reload();*/
        });
    })
    function modal_devengado(id_devengado) {
        $.ajax({
            type: 'post',
            url: '{{route("devecarga.modal_carga")}}',
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
    function update_devengado(id_devengado) {
        let deve_grado = $("#deve_grado").val();
        let deve_emision = $("#deve_emision").val();
        let deve_dni = $("#deve_dni").val();
        let deve_boleta = $("#deve_boleta").val();
        let deve_alumno = $("#deve_alumno").val();
        let deve_cuota = $("#deve_cuota").val();
        let deve_monto = $("#deve_monto").val();

        $.ajax({
            type: 'post',
            url: '{{route("devecarga.update_devengado")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_devengado: id_devengado,
                deve_grado: deve_grado,
                deve_emision: deve_emision,
                deve_dni: deve_dni,
                deve_boleta: deve_boleta,
                deve_alumno: deve_alumno,
                deve_cuota: deve_cuota,
                deve_monto: deve_monto,
            }, beforeSend: function (xhr) {
                $("#update_devengado").attr('disabled', true)
            }, success: function (data, textStatus, jqXHR) {
                alert(data.mensaje)
                console.log(data);
                if (data.tipo == "1") {
                    $("#result_devengado tbody #" + id_devengado).remove();
                    $("#modal-footer button").click();
                    if ($("#result_devengado >tbody >tr").length == 0) {
                        $("#result_devengado").html("");
                        $("#result_devengado_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>");
                    }
                } else if (data.tipo == "2") {
                    $("#update_devengado").attr('disabled', false)
                }
            }
        })
    }
</script>