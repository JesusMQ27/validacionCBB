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
                <input type="text" class="form-control pull-right" id="fechaConcarBanco1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarBanco2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoBanco" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoBanco" id="select_fileCoBanco" style="" />
                    <input type="submit" name="uploadCoBanco" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
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
                <label> Subdiario 1B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario1B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 2B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario2B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 3B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario3B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 4B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario4B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 6B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario6B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 12B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario12B" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28B" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 1H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario1H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 2H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario2H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 3H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario3H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 4H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario4H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 6H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario6H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 12H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario12H" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28H" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <button class="btn btn-primary" id="btnGenerarConcarBanco" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar()">
                <i class="fa fa-list-alt"></i>
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarBanco").attr("disabled", true);
        $("#fechaConcarBanco1").daterangepicker({
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
            $("#fechaConcarBanco1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concar($("#fechaConcarBanco1").val(), $("#fechaConcarBanco2").val(), $("#subdiario1B").val(), $("#subdiario2B").val(), $("#subdiario3B").val(), $("#subdiario4B").val(), $("#subdiario6B").val(), $("#subdiario10B").val(), $("#subdiario12B").val(), $("#subdiario28B").val(), $("#subdiario1H").val(), $("#subdiario2H").val(), $("#subdiario3H").val(), $("#subdiario4H").val(), $("#subdiario6H").val(), $("#subdiario10H").val(), $("#subdiario12H").val(), $("#subdiario28H").val(), $("#messageCo").html());
            var fechaConcarBanco1 = $("#fechaConcarBanco1").val();
            var array_fecha1 = fechaConcarBanco1.split("/");
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

            $("#fechaConcarBanco2").daterangepicker({
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
                $("#fechaConcarBanco2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concar($("#fechaConcarBanco1").val(), $("#fechaConcarBanco2").val(), $("#subdiario1B").val(), $("#subdiario2B").val(), $("#subdiario3B").val(), $("#subdiario4B").val(), $("#subdiario6B").val(), $("#subdiario10B").val(), $("#subdiario12B").val(), $("#subdiario28B").val(), $("#subdiario1H").val(), $("#subdiario2H").val(), $("#subdiario3H").val(), $("#subdiario4H").val(), $("#subdiario6H").val(), $("#subdiario10H").val(), $("#subdiario12H").val(), $("#subdiario28H").val(), $("#messageCo").html());
            });
        });
        $("#fechaConcarBanco1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarBanco1").click();
        $("#upload_formCoBanco").on('submit', function (event) {
            $("#uploadCoBanco").attr("disabled", true);
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
                    $("#uploadCoBanco").attr("disabled", false);
                    mostrar_btn_concar($("#fechaConcarBanco1").val(), $("#fechaConcarBanco2").val(), $("#subdiario1B").val(), $("#subdiario2B").val(), $("#subdiario3B").val(), $("#subdiario4B").val(), $("#subdiario6B").val(), $("#subdiario10B").val(), $("#subdiario12B").val(), $("#subdiario28B").val(), $("#subdiario1H").val(), $("#subdiario2H").val(), $("#subdiario3H").val(), $("#subdiario4H").val(), $("#subdiario6H").val(), $("#subdiario10H").val(), $("#subdiario12H").val(), $("#subdiario28H").val(), $("#messageCo").html());
                }
            });
        });
    });
    $("#subdiario1B, #subdiario2B, #subdiario3B, #subdiario4B, #subdiario6B, #subdiario10B, #subdiario12B, #subdiario28B, #subdiario1H, #subdiario2H, #subdiario3H, #subdiario4H, #subdiario6H, #subdiario10H, #subdiario12H, #subdiario28H").on('keyup blur', function (e) {
        if ($("#subdiario1B").val().length > 0 && $("#subdiario2B").val().length > 0 && $("#subdiario3B").val().length > 0 && $("#subdiario4B").val().length > 0 && $("#subdiario6B").val().length > 0 && $("#subdiario10B").val().length > 0 && $("#subdiario12B").val().length > 0 && $("#subdiario28B").val().length > 0 && $("#subdiario1H").val().length > 0 && $("#subdiario2H").val().length > 0 && $("#subdiario3H").val().length > 0 && $("#subdiario4H").val().length > 0 && $("#subdiario6H").val().length > 0 && $("#subdiario10H").val().length > 0 && $("#subdiario12H").val().length > 0 && $("#subdiario28H").val().length > 0) {
            mostrar_btn_concar($("#fechaConcarBanco1").val(), $("#fechaConcarBanco2").val(), $("#subdiario1B").val(), $("#subdiario2B").val(), $("#subdiario3B").val(), $("#subdiario4B").val(), $("#subdiario6B").val(), $("#subdiario10B").val(), $("#subdiario12B").val(), $("#subdiario28B").val(), $("#subdiario1H").val(), $("#subdiario2H").val(), $("#subdiario3H").val(), $("#subdiario4H").val(), $("#subdiario6H").val(), $("#subdiario10H").val(), $("#subdiario12H").val(), $("#subdiario28H").val(), $("#messageCo").html());
        } else {
            $("#btnGenerarConcarBanco").attr("disabled", true);
        }
    });
    function mostrar_btn_concar(fecha1, fecha2, val1c, val2c, val3c, val4c, val6c, val10Hc, val12Hc, val28Hc, val1H, val2H, val3H, val4H, val6H, val10H, val12H, val28H, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val1c > 0 && val2c > 0 && val3c > 0 && val4c > 0 && val6c > 0 && val10Hc > 0 && val12Hc > 0 && val28Hc > 0 && val1H > 0 && val2H > 0 && val3H > 0 && val4H > 0 && val6H > 0 && val10H > 0 && val12H > 0 && val28H > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarBanco").attr("disabled", false);
        } else {
            $("#btnGenerarConcarBanco").attr("disabled", true);
        }
    }
</script>