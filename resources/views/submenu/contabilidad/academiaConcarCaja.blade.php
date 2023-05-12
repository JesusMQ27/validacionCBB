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
                <input type="text" class="form-control pull-right" id="fechaConcarCaja1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarCaja2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoCaja" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoCaja" id="select_fileCoCaja" style="" />
                    <input type="submit" name="uploadCoCaja" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCo" style="display: none;"></div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 1C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario1C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 2C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario2C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 3C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario3C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 4C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario4C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 6C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario6C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 12C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario12C" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28C: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28C" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 01: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario01" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 02: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario02" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 03: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario03" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 04: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario04" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 06: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario06" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 27: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario27" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <button class="btn btn-primary" id="btnGenerarConcarCaja" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar()">
                <i class="fa fa-list-alt"></i>
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarCaja").attr("disabled", true);
        $("#fechaConcarCaja1").daterangepicker({
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
            $("#fechaConcarCaja1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concar($("#fechaConcarCaja1").val(), $("#fechaConcarCaja2").val(), $("#subdiario1C").val(), $("#subdiario2C").val(), $("#subdiario3C").val(), $("#subdiario4C").val(), $("#subdiario6C").val(), $("#subdiario10C").val(), $("#subdiario12C").val(), $("#subdiario28C").val(), $("#subdiario01").val(), $("#subdiario02").val(), $("#subdiario03").val(), $("#subdiario04").val(), $("#subdiario06").val(), $("#subdiario10").val(), $("#subdiario27").val(), $("#subdiario28").val(), $("#messageCo").html());
            var fechaConcarCaja1 = $("#fechaConcarCaja1").val();
            var array_fecha1 = fechaConcarCaja1.split("/");
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
                //Para 1 mes
                //str_can = 1;
                //str_msj = "month";
                // -----------
                //Para el ultimo dia en el mes
                var lastDate = new Date(year_1, month_1, 0);
                var lastDay = lastDate.getDate();
                str_can = lastDay - day_1;
                str_msj = "day";
            }

            $("#fechaConcarCaja2").daterangepicker({
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
                $("#fechaConcarCaja2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concar($("#fechaConcarCaja1").val(), $("#fechaConcarCaja2").val(), $("#subdiario1C").val(), $("#subdiario2C").val(), $("#subdiario3C").val(), $("#subdiario4C").val(), $("#subdiario6C").val(), $("#subdiario10C").val(), $("#subdiario12C").val(), $("#subdiario28C").val(), $("#subdiario01").val(), $("#subdiario02").val(), $("#subdiario03").val(), $("#subdiario04").val(), $("#subdiario06").val(), $("#subdiario10").val(), $("#subdiario27").val(), $("#subdiario28").val(), $("#messageCo").html());
            });
        });
        $("#fechaConcarCaja1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarCaja1").click();
        $("#upload_formCoCaja").on('submit', function (event) {
            $("#uploadCoCaja").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                //url: "{{route('ajaxupload.actionCo')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCo").removeClass();
                },
                success: function (data) {
                    $("#messageCo").css("display", "block");
                    $("#messageCo").html(data.message);
                    $("#messageCo").addClass(data.class_name);
                    $("#uploadCoCaja").attr("disabled", false);
                    mostrar_btn_concar($("#fechaConcarCaja1").val(), $("#fechaConcarCaja2").val(), $("#subdiario1C").val(), $("#subdiario2C").val(), $("#subdiario3C").val(), $("#subdiario4C").val(), $("#subdiario6C").val(), $("#subdiario10C").val(), $("#subdiario12C").val(), $("#subdiario28C").val(), $("#subdiario01").val(), $("#subdiario02").val(), $("#subdiario03").val(), $("#subdiario04").val(), $("#subdiario06").val(), $("#subdiario10").val(), $("#subdiario27").val(), $("#subdiario28").val(), $("#messageCo").html());
                }
            });
        });
    });
    $("#subdiario1C, #subdiario2C, #subdiario3C, #subdiario4C, #subdiario6C, #subdiario10C, #subdiario12C, #subdiario28C, #subdiario01, #subdiario02, #subdiario03, #subdiario04, #subdiario06, #subdiario10, #subdiario27, #subdiario28").on('keyup blur', function (e) {
        if ($("#subdiario7H").val().length > 0 && $("#subdiario8H").val().length > 0 && $("#subdiario11H").val().length > 0 && $("#subdiario7B").val().length > 0 && $("#subdiario8B").val().length > 0 && $("#subdiario11B").val().length > 0) {
            mostrar_btn_concar($("#fechaConcarCaja1").val(), $("#fechaConcarCaja2").val(), $("#subdiario1C").val(), $("#subdiario2C").val(), $("#subdiario3C").val(), $("#subdiario4C").val(), $("#subdiario6C").val(), $("#subdiario10C").val(), $("#subdiario12C").val(), $("#subdiario28C").val(), $("#subdiario01").val(), $("#subdiario02").val(), $("#subdiario03").val(), $("#subdiario04").val(), $("#subdiario06").val(), $("#subdiario10").val(), $("#subdiario27").val(), $("#subdiario28").val(), $("#messageCo").html());
        } else {
            $("#btnGenerarConcarCaja").attr("disabled", true);
        }
    });
    function mostrar_btn_concar(fecha1, fecha2, val1c, val2c, val3c, val4c, val6c, val10c, val12c, val28c, val01, val02, val03, val04, val06, val10, val27, val28, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val1c > 0 && val2c > 0 && val3c > 0 && val4c > 0 && val6c > 0 && val10c > 0 && val12c > 0 && val28c > 0 && val01 > 0 && val02 > 0 && val03 > 0 && val04 > 0 && val06 > 0 && val10 > 0 && val27 > 0 && val28 > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarCaja").attr("disabled", false);
        } else {
            $("#btnGenerarConcarCaja").attr("disabled", true);
        }
    }
</script>