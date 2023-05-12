<?php /* guadalupe */ ?>

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
                <input type="text" class="form-control pull-right" id="fechaConcarDeve1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarDeve2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoDeve" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoDeve" id="select_fileCoDeve" style="" />
                    <input type="submit" name="uploadCoDeve" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCoDeve" style="display: none;"></div>
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
                <input type="number" class="form-control pull-right" id="subdiario7HDeve" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8HDeve" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11HDeve" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcarDevengados" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar_devengados()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarDevengados").attr("disabled", true);
        $("#fechaConcarDeve1").daterangepicker({
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
            $("#fechaConcarDeve1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concarDeve($("#fechaConcarDeve1").val(), $("#fechaConcarDeve2").val(), $("#subdiario7HDeve").val(), $("#subdiario8HDeve").val(), $("#subdiario11HDeve").val(), $("#messageCoDeve").html());
            var fechaConcarDeve1 = $("#fechaConcarDeve1").val();
            var array_fecha1 = fechaConcarDeve1.split("/");
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

            $("#fechaConcarDeve2").daterangepicker({
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
                $("#fechaConcarDeve2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concarDeve($("#fechaConcarDeve1").val(), $("#fechaConcarDeve2").val(), $("#subdiario7HDeve").val(), $("#subdiario8HDeve").val(), $("#subdiario11HDeve").val(), $("#messageCoDeve").html());
            });
        });
        $("#fechaConcarDeve1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarDeve1").click();
        $("#upload_formCoDeve").on('submit', function (event) {
            $("#uploadCoDeve").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCoDeve')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCoDeve").removeClass();
                },
                success: function (data) {
                    $("#messageCoDeve").css("display", "block");
                    $("#messageCoDeve").html(data.message);
                    $("#messageCoDeve").addClass(data.class_name);
                    $("#uploadCoDeve").attr("disabled", false);
                    mostrar_btn_concarDeve($("#fechaConcarDeve1").val(), $("#fechaConcarDeve2").val(), $("#subdiario7HDeve").val(), $("#subdiario8HDeve").val(), $("#subdiario11HDeve").val(), $("#messageCoDeve").html());
                }
            });
        });
    });
    $("#subdiario7HDeve, #subdiario8HDeve, #subdiario11HDeve").on('keyup blur', function (e) {
        if ($("#subdiario7HDeve").val().length > 0 && $("#subdiario8HDeve").val().length > 0 && $("#subdiario11HDeve").val().length > 0) {
            mostrar_btn_concarDeve($("#fechaConcarDeve1").val(), $("#fechaConcarDeve2").val(), $("#subdiario7HDeve").val(), $("#subdiario8HDeve").val(), $("#subdiario11HDeve").val(), $("#messageCoDeve").html());
        } else {
            $("#btnGenerarConcarDevengados").attr("disabled", true);
        }
    });
    function mostrar_btn_concarDeve(fecha1, fecha2, val7h, val8h, val11h, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarDevengados").attr("disabled", false);
        } else {
            $("#btnGenerarConcarDevengados").attr("disabled", true);
        }
    }
</script>