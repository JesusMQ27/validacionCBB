
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
                <input type="text" class="form-control pull-right" id="fechaFact1" value="" readonly >
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
                <input type="text" class="form-control pull-right" id="fechaFact2" value="" readonly >
            </div>
        </div>
    </div>

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-12">
            <span class="btn btn-info btn-circle btn-sm" style="float: left;">
                <i class="fas fa-info-circle"></i>
            </span>
            <h4 class="card-title" style="float: left;">&nbsp;Facturaci&oacute;n Electr&oacute;nica</h4>
        </div>
        <div class="col-md-12 col-sm-12 col-12">
            <!--<label>Para la validaci&oacute;n de la informaci&oacute;n el sistema considera lo declarado a sunat (Facturaci&oacute;n electr&oacute;nica)
                seg&uacute;n el rango de fechas que seleccione.
            </label>-->
            <form method="post" id="upload_form0" enctype="multipart/form-data" >
                {{csrf_field()}}
                <input type="file" name="select_file0" id="select_file0" style="" />
                <input type="submit" name="upload0" id="upload0" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </form>
        </div>
        <div class="col-sm-6 col-12">
            <div class="alert" id="message0" style="display: none;"></div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-sm-12 col-12">
            <span class="btn btn-info btn-circle btn-sm" style="float: left;">
                <i class="fas fa-info-circle"></i>
            </span>
            <h4 class="card-title" style="float: left;">&nbsp;Pagos Alexia</h4>
        </div>
        <div class="col-sm-12 col-12">
            <form method="post" id="upload_form" enctype="multipart/form-data" >
                {{csrf_field()}}
                <input type="file" name="select_file" id="select_file" style="" />
                <input type="submit" name="upload" id="upload" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </form>
        </div>
        <div class="col-sm-6 col-12">
            <div class="alert" id="message" style="display: none;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-12">
            <span class="btn btn-info btn-circle btn-sm" style="float: left;">
                <i class="fas fa-info-circle"></i>
            </span>
            <h4 class="card-title" style="float: left;">&nbsp;Estado de Cuenta</h4>
            <span style="float: left;margin-top: 7px;font-size: 14px;font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;<a href="../../plantillas/Estado_cuenta_cbb.xlsx">Descargar plantilla</a></span>
            <span style="float: left;margin-top: 7px;font-size: 14px;font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="#" onclick="mostrarSubirArchivos();" style="color: black;">Subir archivos de SCOTIABANK, BBVA, BCP e INTERBANK</a></span>
        </div>
        <div class="col-sm-12 col-12">
            <form method="post" id="upload_form2" enctype="multipart/form-data" >
                {{csrf_field()}}
                <input type="file" name="select_file2" id="select_file2" style="" />
                <input type="submit" name="upload2" id="upload2" style="border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </form>
        </div>
        <div class="col-sm-6 col-12">
            <div class="alert" id="message2" style="display: none;"></div>
        </div>

    </div>
</div>

<div class="col-lg-2 col-md-4 col-sm-6 col-12">
    <button class="btn btn-primary" id="btnValidaInfo" style="bottom: 0px;margin-top: 30px" disabled="" onclick="mostrarInfoValidada();" >Validar Informaci&oacute;n</button>
</div>
<div id="cargar"></div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#upload_form0").on('submit', function (event) {
            $("#upload0").attr("disabled", true);
            event.preventDefault();
            $.ajax({
                url: "{{route('ajaxupload.action0')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#message0").removeClass();
                    $("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
                    $("#cargar").addClass("cargita");
                },
                success: function (data) {
                    $("#cargar").append("");
                    $("#cargar").html("");
                    $("#cargar").removeClass("cargita");
                    $("#message0").css("display", "block");
                    $("#message0").html(data.message);
                    $("#message0").addClass(data.class_name);
                    $("#upload0").attr("disabled", false);
                    mostrar_btn_validar_informacion($("#fechaFact1").val(), $("#fechaFact2").val(), $("#message0").html(), $("#message").html(), $("#message2").html());
                }
            });
        });

        $("#upload_form").on('submit', function (event) {
            event.preventDefault();
            $("#upload").attr("disabled", true);
            $.ajax({
                url: "{{route('ajaxupload.action')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#message").removeClass();
                    $("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
                    $("#cargar").addClass("cargita");
                },
                success: function (data) {
                    $("#cargar").append("");
                    $("#cargar").html("");
                    $("#cargar").removeClass("cargita");
                    $("#message").css("display", "block");
                    $("#message").html(data.message);
                    $("#message").addClass(data.class_name);
                    $("#upload").attr("disabled", false);
                    mostrar_btn_validar_informacion($("#fechaFact1").val(), $("#fechaFact2").val(), $("#message0").html(), $("#message").html(), $("#message2").html());
                }
            });
        });

        $("#upload_form2").on('submit', function (event) {
            event.preventDefault();
            $("#upload2").attr("disabled", true);
            $.ajax({
                url: "{{route('ajaxupload.action2')}}",
                method: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (objeto) {
                    $("#message2").removeClass();
                    $("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
                    $("#cargar").addClass("cargita");
                },
                success: function (data) {
                    $("#cargar").append("");
                    $("#cargar").html("");
                    $("#cargar").removeClass("cargita");
                    $("#message2").css("display", "block");
                    $("#message2").html(data.message);
                    $("#message2").addClass(data.class_name);
                    $("#upload2").attr("disabled", false);
                    mostrar_btn_validar_informacion($("#fechaFact1").val(), $("#fechaFact2").val(), $("#message0").html(), $("#message").html(), $("#message2").html());
                }
            });
        });

        $("#fechaFact1").daterangepicker({
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
            $("#fechaFact1").val(start.endDate.format('DD/MM/YYYY'));
            mostrar_btn_validar_informacion($("#fechaFact1").val(), $("#fechaFact2").val(), $("#message0").html(), $("#message").html(), $("#message2").html());
            $("#fechaFact2").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                linkedCalendar: false,
                autoUpdateInput: false,
                showCustomRangeLabel: false,
                starDate: start.endDate.format("DD/MM/YYYY"),
                minDate: start.endDate.format("DD/MM/YYYY"),
                maxDate: moment(start.endDate.format("MM/DD/YYYY")).add(31, "days"),
                locale: {
                    format: "DD/MM/YYYY"
                }
            }, function (start, end, label) {
            }).on('apply.daterangepicker', function (ev, start) {
                $("#fechaFact2").val(start.endDate.format("DD/MM/YYYY"));
                mostrar_btn_validar_informacion($("#fechaFact1").val(), $("#fechaFact2").val(), $("#message0").html(), $("#message").html(), $("#message2").html());
            });

        });


        $("#fechaFact1").on('click mousedown ', function () {

            $(".calendar-table").find('.today').removeClass("active start-date active end-date");
        });
        $("#fechaFact1").click();

    });

    function mostrarSubirArchivos() {
        $("#btnGeneraExcel_archivos").attr("disabled", true);
        $.ajax({
            url: "{{route('modal.subirArchivo')}}",
            dataType: "html",
            type: "POST",
            data: {
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //$("#contentMenu").html(xhr.responseText);
            },
            success: function (datos) {
                //modal.find('.modal-body').append('<div class="overlay" id="divRegMatri"><i class="fa fa-refresh fa-spin"></i></div>');

                $("#modalSubirArchivos").find('.modal-body').html(datos);
                $("#modalSubirArchivos").modal('show');
            }
        }).done(function () {
            //$("#btnGeneraExcel_archivos").attr("disabled", false);
            $("#image-file").fileinput({
                theme: 'fa',
                uploadUrl: "{{route('image.upload')}}",

                uploadExtraData: function () {
                    return {
                        _token: "{{ csrf_token() }}",
                    };
                },
                allowedFileExtensions: ['txt', 'xlsx', 'xls'/*, 'png', 'gif', 'jpeg'*/],
                overwriteInitial: false,
                //maxFileSize: 2048,
                maxFilesNum: 20,
            }).on('filebatchuploadcomplete', function (event, previewId, index, fileId) {
                $("#btnGeneraExcel_archivos").attr("disabled", false);
            });

        });
    }

    function generar_excel_de_txt() {
        var usuario = $("#usuarioId").val();
        window.location.href = "../../genera_eecc_banco?user=" + usuario;

    }

    function mostrar_btn_validar_informacion(fecha1, fecha2, facturacion, pagoAlexia, eecc) {

        //alert(fecha1 + "**" + fecha2 + "**" + pagoAlexia + "**" + eecc);
        if (fecha1 !== "" && fecha2 !== "" && facturacion === "Exito!" && pagoAlexia === "Exito!" && eecc === "Exito!") {
            $("#btnValidaInfo").attr("disabled", false);
        }

    }

    function mostrarInfoValidada() {
        var fecha_inicio = $("#fechaFact1").val();
        var fecha_fin = $("#fechaFact2").val();
        $("#btnValidaInfo").attr("disabled", true);
        $("#cargar").append("");
        $("#cargar").html("");
        $("#cargar").removeClass("cargita");
        $.ajax({
            url: "{{route('modal.infoValidada')}}",
            dataType: "html",
            type: "POST",
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin
            },
            beforeSend: function (objeto) {
                $("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
                $("#cargar").addClass("cargita");
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //$("#contentMenu").html(xhr.responseText);
            },
            success: function (datos) {
                $("#cargar").append("");
                $("#cargar").removeClass("cargita");
                $("#btnValidaInfo").attr("disabled", false);
                //modal.find('.modal-body').append('<div class="overlay" id="divRegMatri"><i class="fa fa-refresh fa-spin"></i></div>');
                var arreglo = datos.split("*****");
                $("#modalValidarInfo").find('.modal-body').html(arreglo[0]);
                $("#cant_ec_alexia").html("Cantidad de registros: " + arreglo[1]);
                $("#cant_alexia_factu").html("Cantidad de registros: " + arreglo[2]);
                $("#modalValidarInfo").modal('show');
            }
        });
    }

</script>
