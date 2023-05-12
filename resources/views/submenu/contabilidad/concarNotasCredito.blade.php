<?php ?>

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
                <input type="text" class="form-control pull-right" id="fechaConcarNotaC1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarNotaC2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoNotaC" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoNotaC" id="select_fileCoNotaC" style="" />
                    <input type="submit" name="uploadCoNotaC" id="uploadCoNotaC" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCoNotaC" style="display: none;"></div>
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
                <input type="number" class="form-control pull-right" id="subdiario7CNotaC" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8CNotaC" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11CNotaC" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcarNotasCreditos" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar_notas_credito()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarNotasCreditos").attr("disabled", true);
        $("#fechaConcarNotaC1").daterangepicker({
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
            $("#fechaConcarNotaC1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concarNotaCredito($("#fechaConcarNotaC1").val(), $("#fechaConcarNotaC2").val(), $("#subdiario7CNotaC").val(), $("#subdiario8CNotaC").val(), $("#subdiario11CNotaC").val(), $("#messageCoNotaC").html());
            var fechaConcarNotaC1 = $("#fechaConcarNotaC2").val();
            var array_fecha1 = fechaConcarNotaC1.split("/");
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

            $("#fechaConcarNotaC2").daterangepicker({
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
                $("#fechaConcarNotaC2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concarNotaCredito($("#fechaConcarNotaC1").val(), $("#fechaConcarNotaC2").val(), $("#subdiario7CNotaC").val(), $("#subdiario8CNotaC").val(), $("#subdiario11CNotaC").val(), $("#messageCoNotaC").html());
            });
        });
        $("#fechaConcarNotaC1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarNotaC1").click();
        $("#upload_formCoNotaC").on('submit', function (event) {
            $("#uploadCoNotaC").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCoNotasCredito')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCoNotaC").removeClass();
                },
                success: function (data) {
                    $("#messageCoNotaC").css("display", "block");
                    $("#messageCoNotaC").html(data.message);
                    $("#messageCoNotaC").addClass(data.class_name);
                    $("#uploadCoNotaC").attr("disabled", false);
                    mostrar_btn_concarNotaCredito($("#fechaConcarNotaC1").val(), $("#fechaConcarNotaC2").val(), $("#subdiario7CNotaC").val(), $("#subdiario8CNotaC").val(), $("#subdiario11CNotaC").val(), $("#messageCoNotaC").html());
                }
            });
        });
    });
    $("#subdiario7CNotaC, #subdiario8CNotaC, #subdiario11CNotaC").on('keyup blur', function (e) {
        if ($("#subdiario7CNotaC").val().length > 0 && $("#subdiario8CNotaC").val().length > 0 && $("#subdiario11CNotaC").val().length > 0) {
            mostrar_btn_concarNotaCredito($("#fechaConcarNotaC1").val(), $("#fechaConcarNotaC2").val(), $("#subdiario7CNotaC").val(), $("#subdiario8CNotaC").val(), $("#subdiario11CNotaC").val(), $("#messageCoNotaC").html());
        } else {
            $("#btnGenerarConcarNotasCreditos").attr("disabled", true);
        }
    });
    function mostrar_btn_concarNotaCredito(fecha1, fecha2, val7h, val8h, val11h, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarNotasCreditos").attr("disabled", false);
        } else {
            $("#btnGenerarConcarNotasCreditos").attr("disabled", true);
        }
    }
</script>