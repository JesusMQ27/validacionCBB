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
                <input type="text" class="form-control pull-right" id="fechaConcarFactura1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcarFactura2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCoFactura" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCoFactura" id="select_fileCoFactura" style="" />
                    <input type="submit" name="uploadCoFactura" id="uploadCoFactura" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCoFactura" style="display: none;"></div>
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
                <input type="number" class="form-control pull-right" id="subdiario7HFactura" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8HFactura" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11HFactura" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario7BFactura" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8BFactura" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11BFactura" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcarFacturas" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar_facturas()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivo</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcarFacturas").attr("disabled", true);
        $("#fechaConcarFactura1").daterangepicker({
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
            $("#fechaConcarFactura1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concarFacturaredito($("#fechaConcarFactura1").val(), $("#fechaConcarFactura2").val(), $("#subdiario7HFactura").val(), $("#subdiario8HFactura").val(), $("#subdiario11HFactura").val(), $("#subdiario7BFactura").val(), $("#subdiario8BFactura").val(), $("#subdiario11BFactura").val(), $("#messageCoFactura").html());
            var fechaConcarFactura1 = $("#fechaConcarFactura2").val();
            var array_fecha1 = fechaConcarFactura1.split("/");
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

            $("#fechaConcarFactura2").daterangepicker({
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
                $("#fechaConcarFactura2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concarFacturaredito($("#fechaConcarFactura1").val(), $("#fechaConcarFactura2").val(), $("#subdiario7HFactura").val(), $("#subdiario8HFactura").val(), $("#subdiario11HFactura").val(), $("#subdiario7BFactura").val(), $("#subdiario8BFactura").val(), $("#subdiario11BFactura").val(), $("#messageCoFactura").html());
            });
        });
        $("#fechaConcarFactura1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcarFactura1").click();
        $("#upload_formCoFactura").on('submit', function (event) {
            $("#uploadCoFactura").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCoFacturas')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#messageCoFactura").removeClass();
                },
                success: function (data) {
                    $("#messageCoFactura").css("display", "block");
                    $("#messageCoFactura").html(data.message);
                    $("#messageCoFactura").addClass(data.class_name);
                    $("#uploadCoFactura").attr("disabled", false);
                    mostrar_btn_concarFacturaredito($("#fechaConcarFactura1").val(), $("#fechaConcarFactura2").val(), $("#subdiario7HFactura").val(), $("#subdiario8HFactura").val(), $("#subdiario11HFactura").val(), $("#subdiario7BFactura").val(), $("#subdiario8BFactura").val(), $("#subdiario11BFactura").val(), $("#messageCoFactura").html());
                }
            });
        });
    });
    $("#subdiario7HFactura, #subdiario8HFactura, #subdiario11HFactura, #subdiario7BFactura, #subdiario8BFactura, #subdiario11BFactura").on('keyup blur', function (e) {
        if ($("#subdiario7HFactura").val().length > 0 && $("#subdiario8HFactura").val().length > 0 && $("#subdiario11HFactura").val().length > 0 && $("#subdiario7BFactura").val().length > 0 && $("#subdiario8BFactura").val().length > 0 && $("#subdiario11BFactura").val().length > 0) {
            mostrar_btn_concarFacturaredito($("#fechaConcarFactura1").val(), $("#fechaConcarFactura2").val(), $("#subdiario7HFactura").val(), $("#subdiario8HFactura").val(), $("#subdiario11HFactura").val(), $("#subdiario7BFactura").val(), $("#subdiario8BFactura").val(), $("#subdiario11BFactura").val(), $("#messageCoFactura").html());
        } else {
            $("#btnGenerarConcarFacturas").attr("disabled", true);
        }
    });
    function mostrar_btn_concarFacturaredito(fecha1, fecha2, val7h, val8h, val11h, val7b, val8b, val11b, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && val7b > 0 && val8b > 0 && val11b > 0 && mesaje_co === "Exito!") {
            $("#btnGenerarConcarFacturas").attr("disabled", false);
        } else {
            $("#btnGenerarConcarFacturas").attr("disabled", true);
        }
    }
</script>