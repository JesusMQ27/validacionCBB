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
                <input type="text" class="form-control pull-right" id="fechaConcarNotaD1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarNotaD2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoNotaD" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoNotaD" id="select_fileCoNotaD" style="" />
                    <input type="submit" name="uploadCoNotaD" id="uploadCoNotad" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCoNotaD" style="display: none;"></div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 7H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario7HNotaD" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 8H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario8HNotaD" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 11H: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario11HNotaD" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 7B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario7BNotaD" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 8B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario8BNotaD" value=""  >
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subdiario 11B: </label>
            </div>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-book"></i>
                </div>&nbsp;
                <input type="number" class="form-control pull-right" id="subdiario11BNotaD" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcarNotasDebito" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar_notas_debito()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarNotasDebito").attr("disabled", true);
        $("#fechaConcarNotaD1").daterangepicker({
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
            $("#fechaConcarNotaD1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concarNotaDredito($("#fechaConcarNotaD1").val(), $("#fechaConcarNotaD2").val(), $("#subdiario7HNotaD").val(), $("#subdiario8HNotaD").val(), $("#subdiario11HNotaD").val(), $("#subdiario7BNotaD").val(), $("#subdiario8BNotaD").val(), $("#subdiario11BNotaD").val(), $("#messageCoNotaD").html());
            var fechaConcarNotaD1 = $("#fechaConcarNotaD2").val();
            var array_fecha1 = fechaConcarNotaD1.split("/");
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

            $("#fechaConcarNotaD2").daterangepicker({
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
                $("#fechaConcarNotaD2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concarNotaDredito($("#fechaConcarNotaD1").val(), $("#fechaConcarNotaD2").val(), $("#subdiario7HNotaD").val(), $("#subdiario8HNotaD").val(), $("#subdiario11HNotaD").val(), $("#subdiario7BNotaD").val(), $("#subdiario8BNotaD").val(), $("#subdiario11BNotaD").val(), $("#messageCoNotaD").html());
            });
        });
        $("#fechaConcarNotaD1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarNotaD1").click();
        $("#upload_formCoNotaD").on('submit', function (event) {
            $("#uploadCoNotaD").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCoNotasDebito')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCoNotaD").removeClass();
                },
                success: function (data) {
                    $("#messageCoNotaD").css("display", "block");
                    $("#messageCoNotaD").html(data.message);
                    $("#messageCoNotaD").addClass(data.class_name);
                    $("#uploadCoNotaD").attr("disabled", false);
                    mostrar_btn_concarNotaDredito($("#fechaConcarNotaD1").val(), $("#fechaConcarNotaD2").val(), $("#subdiario7HNotaD").val(), $("#subdiario8HNotaD").val(), $("#subdiario11HNotaD").val(), $("#subdiario7BNotaD").val(), $("#subdiario8BNotaD").val(), $("#subdiario11BNotaD").val(), $("#messageCoNotaD").html());
                }
            });
        });
    });
    $("#subdiario7HNotaD, #subdiario8HNotaD, #subdiario11HNotaD, #subdiario7BNotaD, #subdiario8BNotaD, #subdiario11BNotaD").on('keyup blur', function (e) {
        if ($("#subdiario7HNotaD").val().length > 0 && $("#subdiario8HNotaD").val().length > 0 && $("#subdiario11HNotaD").val().length > 0 && $("#subdiario7BNotaD").val().length > 0 && $("#subdiario8BNotaD").val().length > 0 && $("#subdiario11BNotaD").val().length > 0) {
            mostrar_btn_concarNotaDredito($("#fechaConcarNotaD1").val(), $("#fechaConcarNotaD2").val(), $("#subdiario7HNotaD").val(), $("#subdiario8HNotaD").val(), $("#subdiario11HNotaD").val(), $("#subdiario7BNotaD").val(), $("#subdiario8BNotaD").val(), $("#subdiario11BNotaD").val(), $("#messageCoNotaD").html());
        } else {
            $("#btnGenerarConcarNotasDebito").attr("disabled", true);
        }
    });
    function mostrar_btn_concarNotaDredito(fecha1, fecha2, val7h, val8h, val11h, val7b, val8b, val11b, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && val7b > 0 && val8b > 0 && val11b > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarNotasDebito").attr("disabled", false);
        } else {
            $("#btnGenerarConcarNotasDebito").attr("disabled", true);
        }
    }
</script>