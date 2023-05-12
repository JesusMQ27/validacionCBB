
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <span class="btn btn-info btn-circle btn-sm" style="float: left;">
            <i class="fas fa-info-circle"></i>
        </span>
        <h4 class="card-title" style="float: left;margin-left: 5px;">Cargar Notas de Creditos</h4>
    </div>
    <div class="col-sm-12 col-12" >
        <form method="post" class="form-inline" id="carga_notasCreditos" enctype="multipart/form-data" >
            {{csrf_field()}}
            <div style="
                 background-color: #EDEDED;
                 border: 1px solid #DFDFDF;
                 border-radius: 5px;">
                <input type="file" name="excel_notasCreditos" id="excel_notasCreditos"  />
                <input type="submit" name="upload_notasCreditos" id="upload_notasCreditos" style="border-top-left-radius: 0;border-bottom-left-radius: 0;;border-color: #1cc88a!important;color: white;" class="btn bg-gradient-success" value="Cargar Excel"/>
            </div>            
        </form>
    </div>
    <div class="alert" id="message_notasCreditos" style="display: none;padding: 0.2rem;margin-left: 15px;"></div>
</div>
<div class="row" style="margin-top: 30px;">
    <div class="col-12">
        <div class="table-responsive">
            <table id="result_notasCreditos" class="table table hover">

            </table>
        </div>
        <span id="result_notasCreditos_mensaje"></span>
    </div>
</div>
<div class="container-fluid" style="margin-top: 10px;">
    <div class="row">
        <div class="col-12 text-right" style="padding-bottom: 10px;">
            <button onclick="loadNotasCreditos()" class="btn"><i class="fa fa-sync"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12" style="margin-bottom: 1.5rem;">
            <div class="card">
                <h4 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
                    Lista de Notas de Cr&eacute;ditos
                </h4>
                <div class="card-body table-responsive" id='loadData' style="max-height: 280px;overflow: auto;">

                </div>
            </div>
        </div>         
    </div>
</div>
<script type="text/javascript">
    $("#carga_notasCreditos").on('submit', function (e) {
        let subhtml;
        e.preventDefault();
        $.ajax({
            url: "{{route('devenotas.upload_notasCreditos')}}",
            method: "POST",
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $("#message_notasCreditos").removeClass();
                $("#result_notasCreditos").html("");
                $("#result_notasCreditos_mensaje").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#message_notasCreditos").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
                $("#upload_notasCreditos").attr('disabled', true);
            }, success: function (data, textStatus, jqXHR) {
                $("#upload_notasCreditos").attr('disabled', false);
                $("#message_notasCreditos").css("display", "block");
                $("#message_notasCreditos").html(data.message);
                $("#message_notasCreditos").addClass(data.class_name);
                $("#carga_notasCreditos #excel_notasCreditos").val("");

                if (Object.keys(data.html).length > 0) {
                    $("#result_notasCreditos").html(data.html);
                    $("#result_notasCreditos_mensaje").html("<span class='alert-danger'><b>Tienes algunos registros que no guardaron el numero de serie correctamente.</b> Puedes editarlos o borrar todo el grupo insertado.</span>");
                } else {
                    $("#result_notasCreditos_mensaje").html("<span class='alert-success'><b>Carga realizada exitosamente</b></span>")
                }
                $("#result_notasCreditos_mensaje").append("<div ><button id='btnplmt' onclick='modal3_load()' class='btn btn-success'>Verificar Carga Temporal</button></div>");
                if (Object.keys(data.modal).length > 0) {//modal con carga temporal
                    $("#modal-titleModal").html("Carga Temporal");
                    $("#modal-bodyModal").html(data.modal);
                    $("#myModal3").modal('show');
                }
            }

        }).done(function () {
            $("#carga_notasCreditos #upload_notas").attr('disabled', false);
            setTimeout(function () {
                $("#message_notasCreditos").css("display", "none");
            }, 3000);
        }).fail(function () {
            /* alert("algo ah ocurrido");
             window.parent.location.reload();*/
        });
    })
    function reload_modaltmpnota() {
        $.ajax({
            type: 'post',
            url: "{{route('devenotas.refresh_modal_notasCreditos')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {

            }, beforeSend: function (xhr) {
                $("#modal_notasCreditos_tmp").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...")
            }, success: function (data, textStatus, jqXHR) {
                if (Object.keys(data.modal).length > 0) {
                    $("#modal_notasCreditos_tmp").html(data.modal);
                }
            }
        });
    }

    function subir_notasCreditos_tmp() {
        if (confirm("Solo se insertaran los registros NUEVOS \n")) {
            $.ajax({
                type: 'post',
                url: '{{route("notascarga.upload_notas")}}',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                }, beforeSend: function (xhr) {
                    $("#subir_notasCreditos_tmp").attr('disabled', true);
                }, success: function (data, textStatus, jqXHR) {
                    $("#btnplmt").attr('disabled', true);
                    setTimeout(function () {
                        alert("Carga subida correctamente");
                        $("#subir_notasCreditos_tmp").attr('disabled', false);
                        $("#modal-footer-nota button").click();
                    }, 3000)
                }

            }).fail(function (e) {
                alert("error al insertar data");
                $("#subir_notasCreditos_tmp").attr('disabled', false);
            });
        }
    }
    load_grupo_notasCreditos();
    function loadNotasCreditos() {
        load_grupo_notasCreditos();
    }

    function load_grupo_notasCreditos() {
        $.ajax({
            type: 'get',
            url: '{{route("notaadmin.load_gruponota")}}',
            beforeSend: function (xhr) {
                $("#loadData").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            }, success: function (data, textStatus, jqXHR) {
                $("#loadData").html(data);
            }
        });
    }

    function load_notas_creditos_modal(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("notaadmin.load_modalnota")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_grupo: id_grupo
            }, beforeSend: function (xhr) {
                $("#modal-footer-content").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modal-title").html(data.head);
                $("#modal-body").html(data.body);
                $("#modal-footer-content").html(data.footer);
            }
        }).done(function () {
            $("#myModal").modal('show');
            var tlmd1 = 1;
            var tlmd2 = 1;
            var tlmd3 = 1;
            var tlmd4 = 1;
            var tlmd5 = 1;
            var tlmd6 = 1;
            var tlmd7 = 1;
            var tlmd8 = 1;
            var tlmd9 = 1;
            var tlmd10 = 1;
            var tlmd11 = 1;
            var tlmd12 = 1;
            var tlmd13 = 1;
            let table = "modal_table";
            ordenarth("#tlmd1", tlmd1, table);
            ordenarth("#tlmd2", tlmd2, table);
            ordenarth("#tlmd3", tlmd3, table);
            ordenarth("#tlmd4", tlmd4, table);
            ordenarth("#tlmd5", tlmd5, table);
            ordenarth("#tlmd6", tlmd6, table);
            ordenarth("#tlmd7", tlmd7, table);
            ordenarth("#tlmd8", tlmd8, table);
            ordenarth("#tlmd9", tlmd9, table);
            ordenarth("#tlmd10", tlmd10, table);
            ordenarth("#tlmd11", tlmd11, table);
            ordenarth("#tlmd12", tlmd12, table);
            ordenarth("#tlmd13", tlmd13, table);
        });
    }
</script>