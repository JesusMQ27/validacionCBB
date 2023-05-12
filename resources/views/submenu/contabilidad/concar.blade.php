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
                <input type="text" class="form-control pull-right" id="fechaConcar1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaConcar2" value="" readonly >
            </div>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-6 col-12">
            <div class="form-group" style="margin-bottom: 0px;">
                <label> Subir archivos pagos: </label>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <form method="post" id="upload_formCo" enctype="multipart/form-data" >
                    {{csrf_field()}}
                    <input type="file" name="select_fileCo" id="select_fileCo" style="" />
                    <input type="submit" name="uploadCo" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <div class="alert" id="messageCo" style="display: none;"></div>
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
                <input type="number" class="form-control pull-right" id="subdiario7H" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8H" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11H" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario7B" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario8B" value=""  >
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
                <input type="number" class="form-control pull-right" id="subdiario11B" value=""  >
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">

            <button class="btn btn-primary" id="btnGenerarConcar" style="bottom: 0px;margin-top: 30px" onclick="fnc_generar_data_concar()">
                <i class="fa fa-list-alt"></i>  
                Generar Archivos</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btnGenerarConcar").attr("disabled", true);
        $("#fechaConcar1").daterangepicker({
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
            $("#fechaConcar1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_concar($("#fechaConcar1").val(), $("#fechaConcar2").val(), $("#subdiario7H").val(), $("#subdiario8H").val(), $("#subdiario11H").val(), $("#subdiario7B").val(), $("#subdiario8B").val(), $("#subdiario11B").val(), $("#messageCo").html());
			var fechaConcar1= $("#fechaConcar1").val();
			var array_fecha1=fechaConcar1.split("/");
			var day_1 = parseInt(array_fecha1[0]);
			var month_1 = parseInt(array_fecha1[1]);
			var year_1 = parseInt(array_fecha1[2]);
			var str_msj="";
			var str_can="";
			if(month_1==12){
				var lastDate = new Date(year_1, month_1 + 1, 0);
				var lastDay = lastDate.getDate();
				str_can=lastDay-day_1;
				str_msj="day";
			}else{
				str_can=1;
				str_msj="month";
			}
						
            $("#fechaConcar2").daterangepicker({
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
                $("#fechaConcar2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_concar($("#fechaConcar1").val(), $("#fechaConcar2").val(), $("#subdiario7H").val(), $("#subdiario8H").val(), $("#subdiario11H").val(), $("#subdiario7B").val(), $("#subdiario8B").val(), $("#subdiario11B").val(), $("#messageCo").html());
            });
        });
        $("#fechaConcar1").on('click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaConcar1").click();
        $("#upload_formCo").on('submit', function (event) {
            $("#uploadCo").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.actionCo')}}",
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
                    $("#uploadCo").attr("disabled", false);
                    mostrar_btn_concar($("#fechaConcar1").val(), $("#fechaConcar2").val(), $("#subdiario7H").val(), $("#subdiario8H").val(), $("#subdiario11H").val(), $("#subdiario7B").val(), $("#subdiario8B").val(), $("#subdiario11B").val(), $("#messageCo").html());
                }
            });
        });
    });
    $("#subdiario7H, #subdiario8H, #subdiario11H, #subdiario7B, #subdiario8B, #subdiario11B").on('keyup blur', function (e) {
        if ($("#subdiario7H").val().length > 0 && $("#subdiario8H").val().length > 0 && $("#subdiario11H").val().length > 0 && $("#subdiario7B").val().length > 0 && $("#subdiario8B").val().length > 0 && $("#subdiario11B").val().length > 0) {
            mostrar_btn_concar($("#fechaConcar1").val(), $("#fechaConcar2").val(), $("#subdiario7H").val(), $("#subdiario8H").val(), $("#subdiario11H").val(), $("#subdiario7B").val(), $("#subdiario8B").val(), $("#subdiario11B").val(), $("#messageCo").html());
        } else {
            $("#btnGenerarConcar").attr("disabled", true);
        }
    });
    function mostrar_btn_concar(fecha1, fecha2, val7h, val8h, val11h, val7b, val8b, val11b, mesaje_co) {
        if (fecha1 !== "" && fecha2 !== "" && val7h > 0 && val8h > 0 && val11h > 0 && val7b > 0 && val8b > 0 && val11b && mesaje_co === "Exito!") {
            $("#btnGenerarConcar").attr("disabled", false);
        } else {
            $("#btnGenerarConcar").attr("disabled", true);
        }
    }
</script>