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
                <input type="text" class="form-control pull-right" id="fechaConcarPagos1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarPagos2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoPagos" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoPagos" id="select_fileCoPagos" style="" />
                    <input type="submit" name="uploadCoPagos" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
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
                <label> Subdiario 1D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario1D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 2D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario2D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 3D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario3D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 4D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario4D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 6D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario6D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 12D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario12D" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28D: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28D" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 1I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario1I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 2I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario2I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 3I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario3I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 4I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario4I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 6I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario6I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 10I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario10I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 12I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario12I" value=""  >
            </div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 28I: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario28I" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <button class="btn btn-primary" id="btnGenerarConcarPagos" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar()">
                <i class="fa fa-list-alt"></i>
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarPagos").attr("disabled", true);
        $("#fechaConcarPagos1").daterangepicker({
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
            $("#fechaConcarPagos1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concar($("#fechaConcarPagos1").val(), $("#fechaConcarPagos2").val(), $("#subdiario1D").val(), $("#subdiario2D").val(), $("#subdiario3D").val(), $("#subdiario4D").val(), $("#subdiario6D").val(), $("#subdiario10D").val(), $("#subdiario12D").val(), $("#subdiario1I").val(), $("#subdiario2I").val(), $("#subdiario3I").val(), $("#subdiario4I").val(), $("#subdiario6I").val(), $("#subdiario10I").val(), $("#subdiario12I").val(), $("#subdiario28I").val(), $("#messageCo").html());
            var fechaConcarPagos1 = $("#fechaConcarPagos1").val();
            var array_fecha1 = fechaConcarPagos1.split("/");
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

            $("#fechaConcarPagos2").daterangepicker({
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
                $("#fechaConcarPagos2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concar($("#fechaConcarPagos1").val(), $("#fechaConcarPagos2").val(), $("#subdiario1D").val(), $("#subdiario2D").val(), $("#subdiario3D").val(), $("#subdiario4D").val(), $("#subdiario6D").val(), $("#subdiario10D").val(), $("#subdiario12D").val(), $("#subdiario1I").val(), $("#subdiario2I").val(), $("#subdiario3I").val(), $("#subdiario4I").val(), $("#subdiario6I").val(), $("#subdiario10I").val(), $("#subdiario12I").val(), $("#subdiario28I").val(), $("#messageCo").html());
            });
        });
        $("#fechaConcarPagos1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarPagos1").click();
        $("#upload_formCoPagos").on('submit', function (event) {
            $("#uploadCoPagos").attr("disabled", true);
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
                    $("#uploadCoPagos").attr("disabled", false);
                    mostrar_btn_concar($("#fechaConcarPagos1").val(), $("#fechaConcarPagos2").val(), $("#subdiario1D").val(), $("#subdiario2D").val(), $("#subdiario3D").val(), $("#subdiario4D").val(), $("#subdiario6D").val(), $("#subdiario10D").val(), $("#subdiario12D").val(), $("#subdiario1I").val(), $("#subdiario2I").val(), $("#subdiario3I").val(), $("#subdiario4I").val(), $("#subdiario6I").val(), $("#subdiario10I").val(), $("#subdiario12I").val(), $("#subdiario28I").val(), $("#messageCo").html());
                }
            });
        });
    });
    $("#subdiario7H, #subdiario8H, #subdiario11I, #subdiario7B, #subdiario8B, #subdiario11D").on('keyup blur', function (e) {
        if ($("#subdiario7H").val().length > 0 && $("#subdiario8H").val().length > 0 && $("#subdiario11I").val().length > 0 && $("#subdiario7B").val().length > 0 && $("#subdiario8B").val().length > 0 && $("#subdiario11D").val().length > 0) {
            mostrar_btn_concar($("#fechaConcarPagos1").val(), $("#fechaConcarPagos2").val(), $("#subdiario7H").val(), $("#subdiario8H").val(), $("#subdiario11I").val(), $("#subdiario7B").val(), $("#subdiario8B").val(), $("#subdiario11D").val(), $("#messageCo").html());
        } else {
            $("#btnGenerarConcarPagos").attr("disabled", true);
        }
    });
    function mostrar_btn_concar(fecha1, fecha2, val1c, val2c, val3c, val4c, val6c, val10Ic, val12Ic, val28Ic, val1I, val2I, val3I, val4I, val6I, val10I, val12I, val28I, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val1c > 0 && val2c > 0 && val3c > 0 && val4c > 0 && val6c > 0 && val10Ic > 0 && val12Ic > 0 && val28Ic > 0 && val1I > 0 && val2I > 0 && val3I > 0 && val4I > 0 && val6I > 0 && val10I > 0 && val12I > 0 && val28I > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarPagos").attr("disabled", false);
        } else {
            $("#btnGenerarConcarPagos").attr("disabled", true);
        }
    }
</script>