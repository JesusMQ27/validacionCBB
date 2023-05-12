<?php /* Chinita */ ?>

<div class="container-fluid" style="margin-top: 30px;">

    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Fecha Inicio: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>&nbsp;
                <input type="text" class="form-control pull-right" id="fechaConcarBecado1" value="" readonly >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Fecha Fin: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>&nbsp;
                <input type="text" class="form-control pull-right" id="fechaConcarBecado2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoBecado" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoBecado" id="select_fileCoBecado" style="" />
                    <input type="submit" name="uploadCoBecado" id="uploadCoBecado" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCoBecado" style="display: none;"></div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 7M: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario7MBecado" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 8M: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario8MBecado" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 11M: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario11MBecado" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 7C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario7CBecado" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 8C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario8CBecado" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 11C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario11CBecado" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcarBecados" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar_becados()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarBecados").attr("disabled", true);
        $("#fechaConcarBecado1").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            linkedCalendar: false,
            autoUpdateInput: false,
            showCustomRangeLabel: false,
            locale: {
                format: "DD/MM/YYYY"
            }
        }, function (start) {

        }).on('apply.daterangepicker', function (ev, start) {
            $("#fechaConcarBecado1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concarBecado($("#fechaConcarBecado1").val(), $("#fechaConcarBecado2").val(), $("#subdiario7MBecado").val(), $("#subdiario8MBecado").val(), $("#subdiario11MBecado").val(), $("#subdiario7CBecado").val(), $("#subdiario8CBecado").val(), $("#subdiario11CBecado").val(), $("#messageCoBecado").html());
            var fechaConcarBecado1 = $("#fechaConcarBecado2").val();
            var array_fecha1 = fechaConcarBecado1.split("/");
            var day_1 = parseInt(array_fecha1[0]);
            var month_1 = parseInt(array_fecha1[1]);
            var year_1 = parseInt(array_fecha1[2]);
            var str_msj = "";
            var str_can = "";
            if (month_1 == 12) {
                var lastDate = new Date(year_1, month_1 + 1, 0);
                var lastDay = lastDate.getDate();
                str_can = lastDay - day_1;
                str_msj = "day";
            } else {
                str_can = 1;
                str_msj = "month";
            }

            $("#fechaConcarBecado2").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                linkedCalendar: false,
                autoUpdateInput: false,
                showCustomRangeLabel: false,
                starDate: start.endDate.format("DD/MM/YYYY"),
                minDate: start.endDate.format("DD/MM/YYYY"),
                maxDate: moment(start.endDate.format("MM/DD/YYYY")).add(str_can, str_msj),
                locale: {
                    format: "DD/MM/YYYY"
                }
            }, function (start, end, label) {
            }).on('apply.daterangepicker', function (ev, start) {
                $("#fechaConcarBecado2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concarBecado($("#fechaConcarBecado1").val(), $("#fechaConcarBecado2").val(), $("#subdiario7MBecado").val(), $("#subdiario8MBecado").val(), $("#subdiario11MBecado").val(), $("#subdiario7CBecado").val(), $("#subdiario8CBecado").val(), $("#subdiario11CBecado").val(), $("#messageCoBecado").html());
            });
        });
        $("#fechaConcarBecado1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarBecado1").click();
        $("#upload_formCoBecado").on('submit', function (event) {
            $("#uploadCoBecado").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCoBecados')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCoBecado").removeClass();
                },
                success: function (data) {
                    $("#messageCoBecado").css("display", "block");
                    $("#messageCoBecado").html(data.message);
                    $("#messageCoBecado").addClass(data.class_name);
                    $("#uploadCoBecado").attr("disabled", false);
                    mostrar_btn_concarBecado($("#fechaConcarBecado1").val(), $("#fechaConcarBecado2").val(), $("#subdiario7MBecado").val(), $("#subdiario8MBecado").val(), $("#subdiario11MBecado").val(), $("#subdiario7CBecado").val(), $("#subdiario8CBecado").val(), $("#subdiario11CBecado").val(), $("#messageCoBecado").html());
                }
            });
        });
    });
    $("#subdiario7MBecado, #subdiario8MBecado, #subdiario11MBecado, #subdiario7CBecado, #subdiario8CBecado, #subdiario11CBecado").on('keyup blur', function (e) {
        if ($("#subdiario7MBecado").val().length > 0 && $("#subdiario8MBecado").val().length > 0 && $("#subdiario11MBecado").val().length > 0 && $("#subdiario7CBecado").val().length > 0 && $("#subdiario8CBecado").val().length > 0 && $("#subdiario11CBecado").val().length > 0) {
            mostrar_btn_concarBecado($("#fechaConcarBecado1").val(), $("#fechaConcarBecado2").val(), $("#subdiario7MBecado").val(), $("#subdiario8MBecado").val(), $("#subdiario11MBecado").val(), $("#subdiario7CBecado").val(), $("#subdiario8CBecado").val(), $("#subdiario11CBecado").val(), $("#messageCoBecado").html());
        } else {
            $("#btnGenerarConcarBecados").attr("disabled", true);
        }
    });
    function mostrar_btn_concarBecado(fecha1, fecha2, val7h, val8h, val11h, val7b, val8b, val11b, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && val7b > 0 && val8b > 0 && val11b > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarBecados").attr("disabled", false);
        } else {
            $("#btnGenerarConcarBecados").attr("disabled", true);
        }
    }
</script>