<div class="container-fluid" style="margin-top: 10px;">
    <div class="row">
        <div class="col-12 text-right" style="padding-bottom: 10px;">
            <button onclick="load()" class="btn"><i class="fa fa-sync"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
            <div class="card">
                <h4 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
                    Lista de Devengados
                </h4>
                <div class="card-body table-responsive" id='lgd' style="max-height: 500px;overflow: auto;">

                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12" style="margin-bottom: 1.5rem;">
            <div class="card">
                <h4 class="card-title" style="margin-top: .5rem;margin-left: .8rem;">
                    Lista de Pagos
                </h4>
                <div class="card-body table-responsive" id='lgp' style="max-height: 500px;overflow: auto;">

                </div>
            </div>
        </div>               
    </div>
</div>
<script type="text/javascript">
    load_grupo_pagos();
    load_grupo_devengados();
    function load() {
        load_grupo_pagos();
        load_grupo_devengados();
    }
    //DEVENGADOS
    function load_grupo_devengados() {
        $.ajax({
            type: 'get',
            url: '{{route("deveadmin.load_grupodeve")}}',
            beforeSend: function (xhr) {
                $("#lgd").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            }, success: function (data, textStatus, jqXHR) {
                $("#lgd").html(data);
            }
        });
    }
    function load_devengados_modal(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.load_modaldeve")}}',
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

    function mostrarModalDetalleAnulacion(devengado, monto, boleta, grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.detalle_anulacion")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                idDevengado: devengado,
                montoTotal: monto,
                boleta: boleta,
                grupo: grupo
            }, beforeSend: function (xhr) {
                $("#modalAnulacion #modal-footer-content3").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modalAnulacion .modal-title").html(data.head);
                $("#modalAnulacion #modal-body3").html(data.body);
            }
        }).done(function () {
            $("#modalAnulacion").modal('show');
            $('.spinner .btn:first-of-type').on('click', function () {
                var input = $(this).parent().parent().children('input');
                input.val(parseInt(input.val(), 10) + 1);
            });
            $('.spinner .btn:last-of-type').on('click', function () {
                var input = $(this).parent().parent().children('input');
                var newvalue = parseInt(input.val(), 10) - 1;
                if (newvalue >= 0)
                    input.val(newvalue);
            });
            $("#fechaNota").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                linkedCalendar: false,
                autoUpdateInput: false,
                showCustomRangeLabel: false,
                locale: {
                    format: "DD/MM/YYYY"
                }
            }, function (start, end, label) {
            }).on('apply.daterangepicker', function (ev, start) {
                $("#fechaNota").val(start.endDate.format('DD/MM/YYYY'));
            });
        });
    }

    function mostrarDetalleAnulacion(nota) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.info_anulacion")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                idNota: nota
            }, beforeSend: function (xhr) {
                $("#modalDetalleAnulacion #modal-footer-content3").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modalDetalleAnulacion .modal-title").html(data.head);
                $("#modalDetalleAnulacion #modal-body4").html(data.body);
            }
        }).done(function () {
            $("#modalDetalleAnulacion").modal('show');
        });
    }

    function delete_devengados(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.delete_grupodeve")}}',
            //dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_grupo: id_grupo
            }, beforeSend: function (xhr) {

            }, success: function (data, textStatus, jqXHR) {
                if (data == 1) {
                    alert("grupo eliminado correctamente");
                } else {
                    alert("ocurrio algo...");
                }
            }
        }).done(function () {
            load_grupo_devengados();
        });
    }
    //PAGOS
    function load_grupo_pagos() {
        $.ajax({
            type: 'get',
            url: '{{route("deveadmin.load_grupopago")}}',
            beforeSend: function (xhr) {
                $("#lgp").html("<span class='fa fa-sync glyphicon-refresh-animate'></span> Cargando...");
            }, success: function (data, textStatus, jqXHR) {
                $("#lgp").html(data);
            }
        });
    }
    function load_pagos_modal(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.load_modalpago")}}',
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
            }
        }).done(function () {
            $("#myModal").modal('show');
            var tlmp1 = 1;
            var tlmp2 = 1;
            var tlmp3 = 1;
            var tlmp4 = 1;
            var tlmp5 = 1;
            var tlmp6 = 1;
            var tlmp7 = 1;
            var tlmp8 = 1;
            var tlmp9 = 1;
            var tlmp10 = 1;
            var tlmp11 = 1;
            var tlmp12 = 1;
            var tlmp13 = 1;
            var tlmp14 = 1;
            let table = "modal_table"
            ordenarth("#tlmp1", tlmp1, table);
            ordenarth("#tlmp2", tlmp2, table);
            ordenarth("#tlmp3", tlmp3, table);
            ordenarth("#tlmp4", tlmp4, table);
            ordenarth("#tlmp5", tlmp5, table);
            ordenarth("#tlmp6", tlmp6, table);
            ordenarth("#tlmp7", tlmp7, table);
            ordenarth("#tlmp8", tlmp8, table);
            ordenarth("#tlmp9", tlmp9, table);
            ordenarth("#tlmp10", tlmp10, table);
            ordenarth("#tlmp11", tlmp11, table);
            ordenarth("#tlmp12", tlmp12, table);
            ordenarth("#tlmp13", tlmp13, table);
            ordenarth("#tlmp14", tlmp14, table);
        })
    }
    function delete_pagos(id_grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.delete_grupopago")}}',
            //dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_grupo: id_grupo,
            }, beforeSend: function (xhr) {

            }, success: function (data, textStatus, jqXHR) {
                if (data === 1) {
                    alert("grupo eliminado correctamente");
                } else {
                    alert("ocurrio algo...");
                }
            }
        }).done(function () {
            load_grupo_pagos();
        })
    }
    function anulacion_pago(id_pago) {
        if (confirm('seguro de anular pago?')) {
            var comentario = prompt("Cual es el motivo?")
            $.ajax({
                type: 'POST',
                url: '{{route("deveadmin.anulacion_pago")}}',
                dataType: 'json',
                data: {
                    id_pago: id_pago,
                    comentario: comentario
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, beforeSend: function (xhr) {

                }, success: function (data, textStatus, jqXHR) {
                    if (data === "1") {
                        alert("Anulacion realizada con exito");
                        $("#modal-footer button").click();
                        closeModal()
                    } else {
                        alert("Error al realizar la anulacion");
                    }
                }
            });
        }
    }

    function guardar_nota() {
        var devengado = $.trim($("#idDevengado").val());
        var boleta = $.trim($("#deveBoleta").val());
        var nota = $.trim($("#numeroNota").val());
        var monto = $.trim($("#montoNota").val());
        var fecha = $.trim($("#fechaNota").val());
        var observacion = $.trim($("#obsNota").val());
        var monto_max = $.trim($("#montoMax").val());
        var grupo = $.trim($("#grupoCod").val());
        var tipo = $.trim($("#tipoNota").val());

        var mensaje = "";
        if (nota === "") {
            mensaje += "Ingrese la nota de credito<br>";
        }
        if (monto === "0" || monto === "") {
            mensaje += "Ingrese el monto<br>";
        } else {
            if (parseFloat(monto) > parseFloat(monto_max)) {
                mensaje += "El monto no puede ser mayor a " + monto_max + "<br>";
            }
        }
        if (fecha === "") {
            mensaje += "Ingrese la fecha<br>";
        }
        if (mensaje !== "") {
            $("#modal-footer-content3").html("<div class='alert alert-danger' style='display: block;'>" + mensaje + "</div>");
        } else {
            $("#modal-footer-content3").html("");
            $.ajax({
                type: 'POST',
                url: '{{route("deveadmin.anulacion_devengado")}}',
                dataType: 'json',
                data: {
                    id: devengado,
                    nota: nota,
                    monto: monto,
                    monto_ori: monto_max,
                    fecha: fecha,
                    observacion: observacion,
                    boleta: boleta,
                    tipo: tipo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, beforeSend: function (xhr) {

                }, success: function (data, textStatus, jqXHR) {
                    if (data === 1) {
                        $("#modal-footer-content3").html("<div class='alert alert-success' style='display: block;'>"
                                + "Anulacion realizada con exito" + "</div>");
                        setTimeout(function () {
                            $("#modalAnulacion #modal-footer-content3").html("");
                            $("#modalAnulacion").hide();
                            $("#modalAnulacion .modal-title").html("");
                            $("#modalAnulacion .modal-body").html("");
                            $('#modalAnulacion').modal('toggle');
                            load_devengados_modal(grupo);
                        }, 4000);
                    } else {
                        $("#modal-footer-content3").html("<div class='alert alert-danger' style='display: block;'>"
                                + "Error al realizar la anulacion" + "</div>");
                    }
                }
            });
        }
    }

    function mostrarEditarAnulacion(nota, grupo, monto_max) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.detalle_edita_anulacion")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                idNota: nota,
                grupo: grupo,
                monto_max: monto_max
            }, beforeSend: function (xhr) {
                $("#modalEditarAnulacion #modal-footer-content5").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modalEditarAnulacion .modal-title").html(data.head);
                $("#modalEditarAnulacion #modal-body5").html(data.body);
            }
        }).done(function () {
            $("#modalEditarAnulacion").modal('show');
            $('.spinner .btn:first-of-type').on('click', function () {
                var input = $(this).parent().parent().children('input');
                input.val(parseInt(input.val(), 10) + 1);
            });
            $('.spinner .btn:last-of-type').on('click', function () {
                var input = $(this).parent().parent().children('input');
                var newvalue = parseInt(input.val(), 10) - 1;
                if (newvalue >= 0)
                    input.val(newvalue);
            });
            $("#notFecha").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                linkedCalendar: false,
                autoUpdateInput: false,
                showCustomRangeLabel: false,
                locale: {
                    format: "DD/MM/YYYY"
                }
            }, function (start, end, label) {
            }).on('apply.daterangepicker', function (ev, start) {
                $("#notFecha").val(start.endDate.format('DD/MM/YYYY'));
            });
        });
    }

    function editar_nota() {
        var nota_id = $.trim($("#notaCod").val());
        var nota_numero = $.trim($("#notNumero").val());
        var nota_monto = $.trim($("#notMonto").val());
        var nota_fecha = $.trim($("#notFecha").val());
        var nota_obs = $.trim($("#notObs").val());
        var nota_monto_max = $.trim($("#notMontoMax").val());
        var grupo = $.trim($("#grupoCod").val());
        var nota_tipo = $.trim($("#notTipo").val());
        var serie = $.trim($("#notSerie").val());
        var nota_serieDesc = $.trim($("#notSerieDesc").val());
        var mensaje = "";
        if (nota_numero === "") {
            mensaje += "Ingrese la nota de credito<br>";
        }
        if (nota_monto === "0" || nota_monto === "") {
            mensaje += "Ingrese el monto<br>";
        } else {
            if (parseFloat(nota_monto) > parseFloat(nota_monto_max)) {
                mensaje += "El monto no puede ser mayor a " + nota_monto_max + "<br>";
            }
        }
        if (nota_fecha === "") {
            mensaje += "Ingrese la fecha<br>";
        }
        if (mensaje !== "") {
            $("#modal-footer-content5").html("<div class='alert alert-danger' style='display: block;'>" + mensaje + "</div>");
        } else {
            $("#modal-footer-content5").html("");
            $.ajax({
                type: 'POST',
                url: '{{route("deveadmin.modificar_nota")}}',
                dataType: 'json',
                data: {
                    id_nota: nota_id,
                    nota_numero: nota_numero,
                    nota_monto: nota_monto,
                    monto_ori: nota_monto_max,
                    nota_fecha: nota_fecha,
                    nota_observacion: nota_obs,
                    nota_tipo: nota_tipo,
                    nota_serie: serie
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, beforeSend: function (xhr) {

                }, success: function (data, textStatus, jqXHR) {
                    if (data === 1) {
                        $("#modal-footer-content5").html("<div class='alert alert-success' style='display: block;'>"
                                + "Nota de credito modificada con exito" + "</div>");
                        setTimeout(function () {
                            $("#modalEditarAnulacion #modal-footer-content5").html("");
                            $("#modalEditarAnulacion").hide();
                            $("#modalEditarAnulacion .modal-title").html("");
                            $("#modalEditarAnulacion .modal-body").html("");
                            $('#modalEditarAnulacion').modal('toggle');
                            load_devengados_modal(grupo);
                        }, 4000);
                    } else if (data === 2) {
                        $("#modal-footer-content5").html("<div class='alert alert-danger' style='display: block;'>"
                                + "No puede modificar la nota de credito a " + nota_serieDesc + "-" + nota_numero + " porque ya existe." + "</div>");
                    } else {
                        $("#modal-footer-content5").html("<div class='alert alert-danger' style='display: block;'>"
                                + "Error al editar la nota de credito" + "</div>");
                    }
                }
            });
        }
    }

    function mostrarEliminarAnulacion(nota, grupo) {
        $.ajax({
            type: 'post',
            url: '{{route("deveadmin.detalle_eliminar_anulacion")}}',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                idNota: nota,
                grupo: grupo
            }, beforeSend: function (xhr) {
                $("#modalEliminarAnulacion #modal-footer-content6").html("");
            }, success: function (data, textStatus, jqXHR) {
                $("#modalEliminarAnulacion .modal-title").html(data.head);
                $("#modalEliminarAnulacion #modal-body6").html(data.body);
            }
        }).done(function () {
            $("#modalEliminarAnulacion").modal('show');
        });
    }

    function eliminar_nota() {
        var nota_id = $.trim($("#notaCod").val());
        var grupo = $.trim($("#grupoCod").val());
        if (nota_id !== "") {
            $("#modal-footer-content6").html("");
            $.ajax({
                type: 'POST',
                url: '{{route("deveadmin.eliminar_nota")}}',
                dataType: 'json',
                data: {
                    id_nota: nota_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, beforeSend: function (xhr) {

                }, success: function (data, textStatus, jqXHR) {
                    if (data === 1) {
                        $("#modal-footer-content6").html("<div class='alert alert-success' style='display: block;'>"
                                + "Nota de credito eliminada con exito" + "</div>");
                        setTimeout(function () {
                            $("#modalEliminarAnulacion #modal-footer-content6").html("");
                            $("#modalEliminarAnulacion").hide();
                            $("#modalEliminarAnulacion .modal-title").html("");
                            $("#modalEliminarAnulacion .modal-body").html("");
                            $('#modalEliminarAnulacion').modal('toggle');
                            load_devengados_modal(grupo);
                        }, 4000);
                    } else {
                        $("#modal-footer-content6").html("<div class='alert alert-danger' style='display: block;'>"
                                + "Error al eliminar la nota de credito" + "</div>");
                    }
                }
            });
        }
    }

    function validaNumericos(event) {
        if (event.charCode >= 48 && event.charCode <= 57) {
            return true;
        }
        return false;
    }
</script>