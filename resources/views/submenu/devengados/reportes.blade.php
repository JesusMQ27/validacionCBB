<div class='container-fluid' style="margin-top: 50px;">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="col-12">
                Fecha Inicio:
            </label>
            <div class="col-12">
                <input class="form-control" id='deverpfini' readonly="">
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="col-12">
                Fecha Fin:
            </label>
            <div class="col-12">
                <input class="form-control" id='deverpffin' readonly="">
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="col-12">
                Serie:
            </label>
            <div class="col-12">
                <select class="form-control" id='deverpserie'>
                    <option value='1'>-- Todos --</option>
                    <?php
                    $db_ext = \DB::connection('mysql');
                    $query = "SELECT id_serie AS id,serie_desc as codigo,CONCAT(serie_desc,' - ',serie_tipo) AS serie FROM tb_serie ORDER BY serie_tipo;";
                    $series = $db_ext->select($query);
                    /* foreach ($series as $lista) {
                      echo "<option value='" . $lista->codigo . "'>" . $lista->serie . "</option>";
                      } */
                    ?>
                    @foreach($series as $fila)
                    <option value='{{($fila->id)}}'>{{($fila->serie)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <label class="col-12">
                Tipo:
            </label>
            <div class="col-12">
                <select class="form-control" id='deverptipo'>
                    <option value="4"> Todos los Devengados</option>
                    <option value="1"> Deudores (Devengados)</option>
                    <option value="2"> Pagos </option>
                    <option value="3"> Anulados -Notas de Cr&eacute;ditos</option>
                    <!-- chinitos -->
                    <option value="5"> Comprobantes declarados de la OSE</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12"style="margin-top: 15px;margin-bottom: 15px;" >
            <button class="btn btn-primary" style="float:right;" onclick='deverepo()'>
                <i class="fa fa-list-alt"></i>
                Consultar 
            </button>
            <button class="btn btn-success" style="float:right;margin-right: 15px;" onclick='deverepoexcel()'>
                <i class="fa fa-list-alt"></i>
                Generar Reporte
            </button>

        </div>
    </div>
    <div id='result_reporte'>

    </div>
</div>
<script type="text/javascript">
    DTrangePK1("#deverpfini", "#deverpffin");
    $("#deverpfini").click();
    function deverepo() {
        var devepfini = $.trim($("#deverpfini").val());
        var devepffin = $.trim($("#deverpffin").val());
        var deverpserie = $("#deverpserie").select().val();
        var deverptipo = $("#deverptipo").select().val();
        if (devepfini == "" || devepffin == "") {
            alert("complete los campos de la fecha");
            return;
        }
        $.ajax({
            type: 'POST',
            url: '{{route("deve_reporte.lista")}}',
            dataType: 'Json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                devepfini: devepfini,
                devepffin: devepffin,
                deverpserie: deverpserie,
                deverptipo: deverptipo
            },
            beforeSend: function (xhr) {
                $("#result_reporte").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            },
            success: function (data, textStatus, jqXHR) {
                $("#result_reporte").html(data.html);
                var trp1 = 1;
                var trp2 = 1;
                var trp3 = 1;
                var trp4 = 1;
                var trp5 = 1;
                var trp6 = 1;
                var trp7 = 1;
                var trp8 = 1;
                var trp9 = 1;
                var trp10 = 1;
                var trp11 = 1;
                var trp12 = 1;
                //chinitos
                var trp13 = 1;
                var trp14 = 1;
                let table = "repo_table";
                ordenarth("#trp1", trp1, table);
                ordenarth("#trp2", trp2, table);
                ordenarth("#trp3", trp3, table);
                ordenarth("#trp4", trp4, table);
                ordenarth("#trp5", trp5, table);
                ordenarth("#trp6", trp6, table);
                ordenarth("#trp7", trp7, table);
                ordenarth("#trp8", trp8, table);
                ordenarth("#trp9", trp9, table);
                ordenarth("#trp10", trp10, table);
                ordenarth("#trp11", trp11, table);
                ordenarth("#trp12", trp12, table);
                //chinitos
                ordenarth("#trp13", trp13, table);
                ordenarth("#trp14", trp14, table);

            }
        }).fail(function (e) {
            alert("algo ah ocurrido");
            //window.parent.location.reload();
        });
    }
    function deverepoexcel() {
        var devepfini = $.trim($("#deverpfini").val());
        var devepffin = $.trim($("#deverpffin").val());
        var deverpserie = $("#deverpserie").select().val();
        var deverptipo = $("#deverptipo").select().val();
        if (devepfini == "" || devepffin == "") {
            alert("complete los campos de la fecha");
            return;
        }
        window.location.href = "../../devengados/reporte/excel?devepfini=" + devepfini + "&devepffin=" + devepffin + "&deverpserie=" + deverpserie + "&deverptipo=" + deverptipo;
    }


    function DTrangePK1(id, id2) {

        $(id).daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "linkedCalendars": false,
            "autoUpdateInput": false,
            "showCustomRangeLabel": false,
            "startDate": false,
            "singleClasses": "",
            locale: {
                format: "YYYY-MM-DD"
            }
        }, function (start, end, label) {
            /* $(id).val(start.format('YYYY-MM-DD'));
             DtrangePK2(id2, start.format('MM/DD/YYYY'), moment(start.format('MM/DD/YYYY')).subtract(6, 'days'), start.format('MM/DD/YYYY'))             
             */
        }).on('apply.daterangepicker', function (ev, picker) {
            $(id2).val("");
            $(id).val(picker.endDate.format('YYYY-MM-DD'));
            //DtrangePK2(id2, picker.endDate.format('YYYY-MM-DD'), moment(picker.endDate.format('YYYY-MM-DD'), 'YYYY-MM-DD').subtract(6, 'days'), picker.endDate.format('YYYY-MM-DD'))
            DtrangePK2(id2, picker.endDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'), moment(picker.endDate.format('YYYY-MM-DD'), 'YYYY-MM-DD').add(1, 'year'))
        })
        $("#deverpfini").on(' click mousedown ', function () {
            $(".calendar-table").find('.today').removeClass("active start-date active end-date")
        })
    }
    function DtrangePK2(id, startDate, minDate, MaxDate) {
        $(id).daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "linkedCalendars": false,
            "autoUpdateInput": false,
            "showCustomRangeLabel": false,
            "startDate": startDate,
            "minDate": minDate,
            "maxDate": MaxDate,
            locale: {
                format: "YYYY-MM-DD"
            }
        }, function (start, end, label) {
            $(id).val(start.format('YYYY-MM-DD'));
        })
    }
</script>