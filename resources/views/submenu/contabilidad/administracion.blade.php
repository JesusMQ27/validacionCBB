<?php ?>

<div class="container-fluid" style="margin-top: 50px;">
    <div class="row">
        <div class="col-md-12 col-12" style="margin-bottom: 1.5rem;">
            <div class="card">
                <h4 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
                    Lista de Cargas
                </h4>
                <div class="card-body table-responsive" style='max-height:450px;overflow:auto;'>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nro.</th>
                                <th>Fecha y hora de Carga</th>
                                <th>Empleado que realiz&oacute; la carga</th>
                                <th>Estado</th>
                                <th>Ver registros EECC vs ALEXIA</th>
                                <th>Ver registros ALEXIA vs FACTURACI&Oacute;N</th>
                            </tr>
                        </thead>
                        <tbody id='lp'>
                            <?php
                            $db_ext = \DB::connection('mysql');
                            $query = "select t1.*,IF(t1.cantidad_ep=1,'Registro','Registros') as nombre_ep,IF(t1.cantidad_pf=1,'Registro','Registros') as nombre_pf
from (
SELECT a.*,b.cantidad_pf FROM (
SELECT a.congru_id AS id,cont_fecha as fecha_hora,CONCAT(per_pate,' ',per_mate,' ',per_nomb) AS personal,IF(a.cont_estado=1,'Carga correcta','Error en la carga') as estado,
COUNT(c.conep_id) AS cantidad_ep
FROM db_cbb.tb_contabilidad_grupo a
INNER JOIN db_cbb.tb_personal b ON a.id_per=b.id
LEFT JOIN db_cbb.tb_contabilidad_eecc_pagos c ON a.congru_id=c.congru_id
GROUP BY cont_fecha,a.congru_id ORDER BY cont_fecha DESC
) as a
INNER JOIN (
SELECT a.congru_id AS id,cont_fecha as fecha_hora,CONCAT(per_pate,' ',per_mate,' ',per_nomb) AS personal,IF(a.cont_estado=1,'Carga correcta','Error en la carga') as estado,
COUNT(d.conpf_id) as cantidad_pf
FROM db_cbb.tb_contabilidad_grupo a
INNER JOIN db_cbb.tb_personal b ON a.id_per=b.id
LEFT JOIN db_cbb.tb_contabilidad_pagos_facturacion d ON a.congru_id=d.congru_id
GROUP BY cont_fecha,a.congru_id ORDER BY cont_fecha DESC) AS b ON a.id=b.id
) as t1 ";
                            $lista_cargas = $db_ext->select($query);
                            $valor = 1;
                            ?>
                            @foreach($lista_cargas as $fila)
                            <tr>
                                <td>{{($valor)}}</td>
                                <td>{{($fila->fecha_hora)}}</td>
                                <td>{{($fila->personal)}}</td>
                                <td>{{($fila->estado)}}</td>
                                <td> 
                                    <button class='btn btn-primary' id="btn_ea" onclick=ver_registros_ea({{($fila->id)}})><i class='fas fa-search-plus'></i></button>
                                    &nbsp;&nbsp; <label>{{($fila->cantidad_ep)}} {{($fila->nombre_ep)}}</label>
                                </td>
                                <td>
                                    <button class='btn btn-primary' id="btn_pf" onclick=ver_registros_pf({{($fila->id)}})><i class='fas fa-search-plus'></i></button>
                                    &nbsp;&nbsp;<label>{{($fila->cantidad_pf)}} {{($fila->nombre_pf)}}</label></td>
                                <?php $valor++; ?>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
    function ver_registros_ea(codigo){
    $.ajax({
    url: "{{route('ver.registros_ea')}}",
            dataType: "html",
            type: "POST",
            data: {
            grupo: codigo
            },
            beforeSend: function (objeto) {
            //$("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
            //$("#cargar").addClass("cargita");
            },
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (xhr, ajaxOptions, thrownError) {
            //$("#contentMenu").html(xhr.responseText);
            },
            success: function (datos) {
            $("#modal_reg_eecc_alexia").find('.modal-body').html(datos);
            $("#modal_reg_eecc_alexia").modal('show');
            }
    });
    }

    function ver_registros_pf(codigo){

    $.ajax({
    url: "{{route('ver.registros_pf')}}",
            dataType: "html",
            type: "POST",
            data: {
            grupo: codigo
            },
            beforeSend: function (objeto) {
            //$("#cargar").append("<section style='text-align:center;'><img style='margin: 0 auto; padding-top: 15%;' src='../../img/cargar.gif'></section>");
            //$("#cargar").addClass("cargita");
            },
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (xhr, ajaxOptions, thrownError) {
            //$("#contentMenu").html(xhr.responseText);
            },
            success: function (datos) {
            $("#modal_reg_alexia_facturacion").find('.modal-body').html(datos);
            $("#modal_reg_alexia_facturacion").modal('show');
            }
    });
    }
</script>
