function load_modulo(id, href, obj) {
    $(".list-menu-options a").each(function (index) {
        $(this).removeClass("fontColor");
    });
    $(obj).addClass("fontColor");
    //list-menu-options
    $.ajax({
        type: 'post',
        url: 'menu/valida',
        data: {
            id: id,
            href: href
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            //console.log(data);
            if (typeof data[0] === "undefined") {
                alert("Algo ah ocurrido");
                location.reload();
            }
            $("#contenido").html('<iframe style="width:100%;height: 85vh;border:0;border-radio:5px" class="sombreado" allowfullscreen src="/' + data[0].menuOp_href + '?menu=' + data[0].id_menuPanel + '"></iframe>');
        }
    }).fail(function (e) {
        alert("Algo ah ocurrido");
        location.reload();
    })
}

function mostrarPanel(cad) {
    if ($("#accordionSidebar").hasClass('toggled')) {
        $("#sidebarToggleTop").click();
    }
    $("#menu" + cad).click();
}

function carga_contenido(href, tipo, codigoId, obj) {

    if ($("#nav-tabContent #" + href).length > 0) {
        return;
    }
    let ruta;
    if (tipo == "") {
        ruta = '../../submenu/' + href;
    } else {
        ruta = '../../submenu/' + tipo + "_" + href;
    }
    
    $("body .list-menu-options a").each(function (index) {
        $(this).removeClass("fontColor");
    });
    
    $("#submenu" + codigoId).addClass("fontColor");
    /*$.ajax({
     type: 'get',
     url: ruta,
     beforeSend: function (xhr) {
     
     },
     success: function (data, textStatus, jqXHR) {
     $("#nav-tabContent .tab-pane").each(function () {
     $(this).removeClass('show active');
     })
     //$("#nav-tabContent").append('<div class="tab-pane fade" id="" role="tabpanel" aria-labelledby="nav-home-tab">' + (data) + '</div>');
     $("#nav-tabContent").append("<div class='tab-pane fade show active' id='" + (href) + "' role='tabpanel' aria-labelledby='nav-home-tab'>" + (data) + "</div>");
     }
     }).done(function () {
     //carga_contenido($(i).click());
     }).fail(function (e) {
     
     
     })*/
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            href: ruta
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function (xhr) {
        },
        success: function (data, textStatus, jqXHR) {
            $("#nav-tabContent .tab-pane").each(function () {
                $(this).removeClass('show active');
            })
            $.ajax({
                type: 'get',
                url: ruta,
                beforeSend: function (xhr) {

                },
                success: function (data, textStatus, jqXHR) {
                    $("#nav-tabContent .tab-pane").each(function () {
                        $(this).removeClass('show active');
                    })
                    //$("#nav-tabContent").append('<div class="tab-pane fade" id="" role="tabpanel" aria-labelledby="nav-home-tab">' + (data) + '</div>');
                    $("#nav-tabContent").append("<div class='tab-pane fade show active' id='" + (href) + "' role='tabpanel' aria-labelledby='nav-home-tab'>" + (data) + "</div>");
                }
            }).done(function () {
                //carga_contenido($(i).click());
            }).fail(function (e) {
                alert("Algo ah ocurrido");
                location.reload();
            })
            //$("#nav-tabContent").append("<div class='tab-pane fade show active' id='" + (href) + "' role='tabpanel' aria-labelledby='nav-home-tab'><div src='" + (ruta) + "'></iframe></div>");
        }
    }).fail(function (e) {
        alert("Algo ah ocurrido");
        window.parent.location.reload();
    })
}
function doSearch(id_tabla, id_input)
{
    var tableReg = document.getElementById(id_tabla);
    var searchText = document.getElementById(id_input).value.toLowerCase();
    var cellsOfRow = "";
    var found = false;
    var compareWith = "";

    // Recorremos todas las filas con contenido de la tabla
    for (var i = 1; i < tableReg.rows.length; i++)
    {
        cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
        found = false;
        // Recorremos todas las celdas
        for (var j = 0; j < cellsOfRow.length && !found; j++)
        {
            compareWith = cellsOfRow[j].innerHTML.toLowerCase();
            // Buscamos el texto en el contenido de la celda
            if (searchText.length == 0 || (compareWith.indexOf(searchText) > -1))
            {
                found = true;
            }
        }
        if (found)
        {
            tableReg.rows[i].style.display = '';
        } else {
            // si no ha encontrado ninguna coincidencia, esconde la
            // fila de la tabla
            tableReg.rows[i].style.display = 'none';
        }
    }
}







function sortTable(f, n, idtable) {
    var rows = $('#' + idtable + ' tbody  tr').get();

    rows.sort(function (a, b) {

        var A = getVal(a);
        var B = getVal(b);

        if (A < B) {
            return -1 * f;
        }
        if (A > B) {
            return 1 * f;
        }
        return 0;
    });

    function getVal(elm) {
        var v = $(elm).children('td').eq(n).text().toUpperCase();
        if ($.isNumeric(v)) {
            v = parseInt(v, 10);
        }
        return v;
    }

    $.each(rows, function (index, row) {
        $('#' + idtable + '').children('tbody').append(row);
    });
}
function ordenarth(idth, idval, idtable) {
    $(idth).click(function () {
        idval *= -1;
        var n = $(this).prevAll().length;
        sortTable(idval, n, idtable);
    })
}
var f_sl = 1;
var f_nm = 1;
$("#sl").click(function () {
    f_sl *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_sl, n);
});
$("#nm").click(function () {
    f_nm *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_nm, n);
});