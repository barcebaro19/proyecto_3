var redibujar = [];
var TOOLBOX = {
    show: true,
    // orient: 'vertical',
    feature: {
        mark: {
            show: true
        },
        dataView: {
            show: true,
            readOnly: false
        },
        magicType: {
            show: true,
            type: ['line', 'bar']
        },
        restore: {
            show: true
        },
        saveAsImage: {
            show: true
        }
    }
};
var TABLECONFG = {
    responsive: {
        details: false
    },
    language: {
        paginate: {
            previous: 'Anterior',
            next: 'Siguiente',
        },
        info: 'Pagina _START_ de _END_ de _TOTAL_ registros',
        search: 'Buscar:',
        lengthMenu: '_MENU_ registros'
    },
    // ordering:  false
};
$(function () {
    function resize() {
        setTimeout(function () {
            redibujar.map(function (a) {
                a.resize();
            });
        }, 100);
    }
    $(window).on("resize", resize);
    var menu = $('#sidebar ul a');
    menu.map(function (k, v) {
        if (v.href == location.href) {
            v.parentNode.classList.add('active');
        } else {
            v.parentNode.classList.remove('active');
        }
    });
});
function arrayToObjet(form) {
    return $(form).serializeArray().reduce(function (a, x) { a[x.name] = x.value; return a; }, {});
}
function parsestring(msj, separador) {
    if (separador === undefined) separador = '<hr>';
    if (typeof msj === 'string' || msj instanceof String) {
        return msj;
    } else if (msj instanceof Array || msj.constructor === Array) {
        return msj.join(separador);
    } else if (msj instanceof Object || msj.constructor === Object) {
        return Object.entries(msj).map(function (entry) { return entry.join(':'); }).join(separador);
    }
}
function graficar(idetiqueta, datos, tipo, tool) {
    redibujar.push(echarts.init(document.getElementById(idetiqueta))
        .setOption(opciones(datos, tipo, tool), true));
    function opciones(data, tipo, tool) {
        var serie = {
            name: data.nombre,
            type: tipo,
            data: data.datos,
            itemStyle: {
                normal: {
                    label: {
                        show: true,
                        position: 'top',
                        formatter: '{c}'
                    }
                }
            }
        };
        if (tipo === 'line') {
            serie.smooth = true;
            serie.itemStyle.normal.areaStyle = {
                type: 'default',
                color: data.colores[4]
            };
        } else if (tipo === 'bar') {
            serie.itemStyle.normal.color = function (params) {
                return data.colores[params.dataIndex];
            };
        } else if (tipo === 'pie') {
            serie.radius = [40, 55];
            serie.roseType = 'area';
            serie.itemStyle.emphasis = {
                label: {
                    show: true,
                    position: 'center',
                    textStyle: {
                        color: 'red',
                        fontSize: '18',
                        fontWeight: 'bold'
                    }
                }
            };
            serie.data = data.datos.map(function (v) {
                return {
                    value: v,
                    name: v
                };
            });
        } else if (tipo === 'radar') {
            serie.data = [{
                value: data.datos,
                name: data.nombre,
            }];
        } else if (tipo === 'funnel') {
            serie.data = data.datos.map(function (v) {
                return {
                    value: v,
                    name: v
                };
            });
        } else if (tipo === 'treemap') {
            serie.itemStyle.emphasis = {
                label: {
                    show: true
                }
            };
            serie.data = data.datos.map(function (v, i) {
                return {
                    name: v,
                    value: Math.round(v) // parseInt(v)
                };
            });
        } else if (tipo === 'map') {
            serie.mapType = 'world|Colombia';
            serie.roam = true;
            serie.itemStyle.emphasis = {
                label: {
                    show: true,
                }
            };
            serie.itemStyle.normal.label.formatter = '';
        }
        var option = {
            noDataLoadingOption: {
                text: 'Cargando',
                effect: 'ring'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    lineStyle: {
                        type: 'dashed',
                        width: 1
                    }
                }
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                data: data.label
            }],
            yAxis: [{
                type: 'value'
            }],
            series: [serie]
        };
        if (tipo === 'funnel') {
            option.xAxis = [];
            // option.xAxis[0].boundaryGap = false;
            // option.xAxis[0].splitLine = {show: false};
        } else if (tipo === 'pie' || tipo === 'treemap') {
            option.xAxis = [];
            option.calculable = false;
        } else if (tipo === 'radar') {
            option.xAxis = [];
            option.polar = [{
                indicator: data.label.map(function (v) {
                    return {
                        text: v,
                        max: 100
                    };
                }),
                radius: 60
            }];
        }
        if (data.titulo) {
            option.title = {
                x: 'center',
                subtext: data.titulo
            };
        }
        if (data.legend) {
            option.legend = {
                data: data.legend
            };
        }
        if (tool) {
            option.toolbox = tool;
        }
        return option;
    }
}