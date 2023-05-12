<?php ?>

<fieldset class="col-md-12" id="fieldset">    
    <legend id="legend">Reportes Contabilidad</legend>
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
                    <input type="text" class="form-control pull-right" id="fechaRepor1" value="" readonly >
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
                    <input type="text" class="form-control pull-right" id="fechaRepor2" value="" readonly >
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label> Serie: </label>
                </div>
                <div class="input-group">
                    <select class="form-control" id="cbbSerie" name="cbbSerie">
                        <option value="0">-- Todos --</option>
                        <?php
                        $db_ext = \DB::connection('mysql');
                        $query = "SELECT id_serie AS id,serie_desc as codigo,CONCAT(serie_desc,' - ',serie_tipo) AS serie FROM tb_serie ORDER BY serie_tipo;";
                        $series = $db_ext->select($query);
                        ?>
                        @foreach($series as $fila)
                        <option value='{{($fila->codigo)}}'>{{($fila->serie)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label> Tipo de Reporte: </label>
                </div>
                <div class="input-group">
                    <select class="form-control" id="cbbTipo" name="cbbTipo">
                        <option value="1">Reporte Informe Contabilidad CBB</option>
                        <option value="2">Reporte Informe Comprobantes CBB</option>
                        <option value="3">Reporte Declarac&oacute;n CBB para la SUNAT(OSE)</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <button class="btn btn-primary" id="btnGeneraReporte" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_reporte()">
                    <i class="fa fa-list-alt"></i>  
                    Generar Reporte</button>
            </div>
        </div>

        <div class="row" id="divReporteConta" style="margin-bottom: 30px;">
        </div>
    </div>
</fieldset>
<br><br>
<fieldset class="col-md-12" id="fieldset">    
    <legend id="legend">Reporte Devengado</legend>
    <div class="container-fluid" style="margin-top: 30px;">
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label> A&ntilde;o: </label>
                </div>
                <div class="input-group">
                    <select class="form-control" id="cbbAnio" name="cbbAnio">
                        <option value="0">-- Seleccione --</option>
                        <?php
                        $db_ext2 = \DB::connection('mysql');
                        $query2 = "SELECT YEAR(NOW()) as anio;";
                        $anio = $db_ext->select($query2);
                        $anio_actual = $anio[0]->anio;

                        for ($i = $anio_actual; $i >= 2010; $i--) {
                            echo "<option value='" . $i . "'>" . $i . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label> Fecha hasta: </label>
                </div>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>&nbsp;
                    <input type="text" class="form-control pull-right" id="fechaRepor3" value="" readonly >
                </div>
            </div>            
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <button class="btn btn-primary" id="btnReporteDevengado" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_reporte_devengado()">
                    <i class="fa fa-list-alt"></i>  
                    Generar Reporte</button>
            </div>
        </div>
    </div>
</fieldset>
<script>
    $(document).ready(function () {

        $("#btnGeneraReporte").attr("disabled", true);
        $("#btnReporteDevengado").attr("disabled", true);

        $("#fechaRepor1").daterangepicker({
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
            $("#fechaRepor1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_generar_reporte($("#fechaRepor1").val(), $("#fechaRepor2").val());

            var fechaConcar1 = $("#fechaRepor1").val();
            var array_fecha1 = fechaConcar1.split("/");
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

            $("#fechaRepor2").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                linkedCalendar: false,
                autoUpdateInput: false,
                showCustomRangeLabel: false,
                starDate: start.endDate.format("DD/MM/YYYY"),
                minDate: start.endDate.format("DD/MM/YYYY"),
                //maxDate: moment(start.endDate.format("MM/DD/YYYY")).add(31, "days"),
                maxDate: moment(start.endDate.format("MM/DD/YYYY")).add(str_can, str_msj),
                locale: {
                    format: "DD/MM/YYYY"
                }
            }, function (start, end, label) {
                $("#fechaRepor2").val(start.format("DD/MM/YYYY"));
            }).on('apply.daterangepicker', function (ev, start) {
                $("#fechaRepor2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_generar_reporte($("#fechaRepor1").val(), $("#fechaRepor2").val());
            });
        });

        $("#fechaRepor1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaRepor1").click();


        $("#fechaRepor3").daterangepicker({
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
            $("#fechaRepor3").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_boton_reporte_devengado($("#cbbAnio").select().val(), $("#fechaRepor3").val());
        });

        $('#cbbAnio').on('change', function () {
            if ($(this).select().val() !== "0") {
                mostrar_boton_reporte_devengado($("#cbbAnio").select().val(), $("#fechaRepor3").val());
            } else {
                $("#btnReporteDevengado").attr("disabled", true);
            }

        });


        function mostrar_btn_generar_reporte(fecha1, fecha2) {
            if (fecha1 !== "" && fecha2 !== "") {
                $("#btnGeneraReporte").attr("disabled", false);
            } else {
                $("#btnGeneraReporte").attr("disabled", true);
            }

        }


        function mostrar_boton_reporte_devengado(anio, fecha_hasta) {
            if (anio !== 0 && fecha_hasta !== "") {
                $("#btnReporteDevengado").attr("disabled", false);
            } else {
                $("#btnReporteDevengado").attr("disabled", true);
            }
        }
    });
</script>
