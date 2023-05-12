/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function fnc_generar_reporte() {
    var fechaInicio = $("#fechaRepor1").val();
    var fechaFin = $("#fechaRepor2").val();
    var serie = $("#cbbSerie").select().val();
    var tipo = $("#cbbTipo").select().val();

    //alert(fechaInicio+"**"+fechaFin+"**"+serie+"**"+tipo);
    window.location.href = "../../genera_reporte?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&serie=" + serie + "&tipo=" + tipo;

}


function fnc_generar_reporte_devengado() {
    var anio = $("#cbbAnio").val();
    var fecha_fin = $("#fechaRepor3").val();
    window.location.href = "../../genera_reporte_devengado?anioDeve=" + anio + "&fecha_fin3=" + fecha_fin;

}

function fnc_generar_data_concar() {
    var fechaInicio = $("#fechaConcar1").val();
    var fechaFin = $("#fechaConcar2").val();
    var v7h = $("#subdiario7H").val();
    var v8h = $("#subdiario8H").val();
    var v11h = $("#subdiario11H").val();
    var v7b = $("#subdiario7B").val();
    var v8b = $("#subdiario8B").val();
    var v11b = $("#subdiario11B").val();
    window.location.href = "../../genera_reporte_concar?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7h=" + v7h + "&v8h=" + v8h + "&v11h=" + v11h + "&v7b=" + v7b + "&v8b=" + v8b + "&v11b=" + v11b;
}

function fnc_generar_data_concar_devengados() {
    var fechaInicio = $("#fechaConcarDeve1").val();
    var fechaFin = $("#fechaConcarDeve2").val();
    var v7h = $("#subdiario7HDeve").val();
    var v8h = $("#subdiario8HDeve").val();
    var v11h = $("#subdiario11HDeve").val();
    window.location.href = "../../genera_reporte_concar_devengados?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7h=" + v7h + "&v8h=" + v8h + "&v11h=" + v11h;
}


function fnc_generar_data_concar_notas_credito() {
    var fechaInicio = $("#fechaConcarNotaC1").val();
    var fechaFin = $("#fechaConcarNotaC2").val();
    var v7h = $("#subdiario7CNotaC").val();
    var v8h = $("#subdiario8CNotaC").val();
    var v11h = $("#subdiario11CNotaC").val();
    window.location.href = "../../genera_reporte_concar_notas_credito?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7c=" + v7h + "&v8c=" + v8h + "&v11c=" + v11h;
}

//Chinita
function fnc_generar_data_concar_notas_debito() {
    var fechaInicio = $("#fechaConcarNotaD1").val();
    var fechaFin = $("#fechaConcarNotaD2").val();
    var v7h = $("#subdiario7HNotaD").val();
    var v8h = $("#subdiario8HNotaD").val();
    var v11h = $("#subdiario11HNotaD").val();
    var v7b = $("#subdiario7BNotaD").val();
    var v8b = $("#subdiario8BNotaD").val();
    var v11b = $("#subdiario11BNotaD").val();
    window.location.href = "../../genera_reporte_concar_notas_debito?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7h=" + v7h + "&v8h=" + v8h + "&v11h=" + v11h + "&v7b=" + v7b + "&v8b=" + v8b + "&v11b=" + v11b;
}

function fnc_generar_data_concar_facturas() {
    var fechaInicio = $("#fechaConcarFactura1").val();
    var fechaFin = $("#fechaConcarFactura2").val();
    var v7h = $("#subdiario7HFactura").val();
    var v8h = $("#subdiario8HFactura").val();
    var v11h = $("#subdiario11HFactura").val();
    var v7b = $("#subdiario7BFactura").val();
    var v8b = $("#subdiario8BFactura").val();
    var v11b = $("#subdiario11BFactura").val();
    window.location.href = "../../genera_reporte_concar_facturas?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7h=" + v7h + "&v8h=" + v8h + "&v11h=" + v11h + "&v7b=" + v7b + "&v8b=" + v8b + "&v11b=" + v11b;
}

function fnc_generar_data_concar_becados() {
    var fechaInicio = $("#fechaConcarBecado1").val();
    var fechaFin = $("#fechaConcarBecado2").val();
    var v7m = $("#subdiario7MBecado").val();
    var v8m = $("#subdiario8MBecado").val();
    var v11m = $("#subdiario11MBecado").val();
    var v7c = $("#subdiario7CBecado").val();
    var v8c = $("#subdiario8CBecado").val();
    var v11c = $("#subdiario11CBecado").val();
    window.location.href = "../../genera_reporte_concar_becados?fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&v7m=" + v7m + "&v8m=" + v8m + "&v11m=" + v11m + "&v7c=" + v7c + "&v8c=" + v8c + "&v11c=" + v11c;
}


