/*jshint strict:false */
var offsetTop = 0;
var isTest = false;

Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
}

var urlPart = '/ajax/';

function loaderShow()
{
    $('#loader').show();
}

function loaderHide()
{
    $('#loader').hide();
}

function loadGantt(url)
{
    loaderShow();
    gantt.load(urlPart + url, 'json', function () {
        loaderHide();
    });
    setMarker();
}

function init()
{
    gantt.config.scale_unit = "day";
    gantt.config.step = 1;
    gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
    gantt.config.grid_width = 100;
    gantt.config.details_on_dblclick = false;
    gantt.config.drag_progress = false;
    gantt.config.drag_resize = false;
    gantt.config.drag_links = false;
    gantt.config.show_progress = false;

    $('<a />').attr({
        'href': '#',
        'class': 'btn btn-success',
        'id': 'fullscreen'
    }).css({
        'position': 'absolute'
    }).html('<span class="glyphicon glyphicon-resize-full"></span>').appendTo($('#wrapper'));
    getTasks();
    loadGantt('tasks');
}

function lagsAndAdvance()
{
    return true;
    gantt.templates.task_text = function (start, end, task)
    {
        var text = task.text;
        if (task.brigade) {
            text += "<span style='text-align:left;'> (Бригада " + task.brigade + ")</span>";
        }
        return text;
    };

    gantt.addTaskLayer(function (task) {
        if (task.open) {
            console.log(task);
            if (task.lag_start > 0) {
                var color = 'rgba(235,0,0,0.5)';
                var startDate = new Date(task.start_date);
                var endDate = new Date(task.real_start_date);
                var sizes = gantt.getTaskPosition(task, startDate, endDate);
                var el = $('<div />').addClass('indicator').css({
                    width: sizes.width,
                    left: sizes.left,
                    top: sizes.top,
                    height: sizes.height,
                    backgroundColor: color
                });
                return el[0];
            }
        }
        return;
    });
    gantt.addTaskLayer(function (task) {
        if (task.open) {
            if (task.lag_end > 0) {
                var color = 'rgba(235,0,0,0.5)';
                var startDate = new Date(task.end_date);
                var endDate = new Date(task.real_end_date);
                var sizes = gantt.getTaskPosition(task, startDate, endDate);
                var el = $('<div />').addClass('indicator').css({
                    width: sizes.width,
                    left: sizes.left,
                    top: sizes.top,
                    height: sizes.height,
                    backgroundColor: color
                });
                return el[0];
            }
        }
        return;
    });
    gantt.addTaskLayer(function (task) {
        if (task.open) {
            if (task.advance > 0) {
                var startDate = task.end_date;
                var endDate = startDate;
                startDate = endDate.addDays(0 - task.advance);
                var color = 'rgba(0,255,0,0.3)';
                var sizes = gantt.getTaskPosition(task, startDate, endDate);
                var el = $('<div />').addClass('indicator').css({
                    width: sizes.width,
                    left: sizes.left,
                    top: sizes.top,
                    height: sizes.height,
                    backgroundColor: color
                });

                return el[0];
            }
        }
        return;
    });
    offsetTop = $('.gantt_task_scale').offset().top;
}

function getTasks()
{
    gantt.config.columns = [
        {name: "text", label: "Проект/Задача", min_width: 300, resize:true, tree: true},
        {name: "start_date", label:"Начало план", align: "center", width: 110, resize:true},
        {name: "end_date", label:"Конец план", align: "center", width: 110, template: function(task) {
                return gantt.templates.date_grid(task.end_date, task);
            }, resize:true},
        {name: "duration", label:"Длительность", align: "center", width: 120, resize:true}
        // {name: "lag", label:"Отставание", align: "center", width: 120, resize:true},
        // {name: "advance", label:"Опережение", align: "center", width: 120, resize:true},
    ];

    gantt.config.autosize = true;
    gantt.config.layout = {
        css: "gantt_container",
        cols: [
            {
                width:500,
                min_width: 300,
                rows:[
                    {view: "grid", scrollX: "gridScroll", scrollable: true, scrollY: "scrollVer"},
                    {view: "scrollbar", id: "gridScroll", group:"horizontal"}
                ]
            },
            {resizer: true, width: 1},
            {
                rows:[
                    {view: "timeline", scrollX: "scrollHor", scrollY: "scrollVer"},
                    {view: "scrollbar", id: "scrollHor", group:"horizontal"}
                ]
            },
            {view: "scrollbar", id: "scrollVer"}
        ]
    };
    gantt.init("gantt-wrapper");
    lagsAndAdvance();
}

function setMarker() {
    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);

    var id = gantt.addMarker({
        start_date: new Date(),
        css: "today",
        title: date_to_str( new Date()),
        text: 'Сегодня'
    });
    setInterval(function(){
        var today = gantt.getMarker(id);
        today.start_date = new Date();
        today.title = date_to_str(today.start_date);
        gantt.updateMarker(id);
    }, 1000*60);
}

function setScaleConfig(level) {
    switch (level) {
        case "day":
            gantt.config.scale_unit = "day";
            gantt.config.step = 1;
            gantt.config.date_scale = "%d %M";
            gantt.templates.date_scale = null;
            gantt.config.min_column_width = 50;
            gantt.config.scale_height = 27;

            gantt.config.subscales = [];
            break;
        case "week":
            var weekScaleTemplate = function (date) {
                var dateToStr = gantt.date.date_to_str("%d %M");
                var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                return dateToStr(date) + " - " + dateToStr(endDate);
            };

            gantt.config.min_column_width = 30;
            gantt.config.scale_unit = "week";
            gantt.config.step = 1;
            gantt.templates.date_scale = weekScaleTemplate;

            gantt.config.scale_height = 50;

            gantt.config.subscales = [
                {unit: "day", step: 1, date: "%D"}
            ];
            break;
        case "month":
            gantt.config.scale_unit = "month";
            gantt.config.date_scale = "%F, %Y";
            gantt.templates.date_scale = null;

            gantt.config.min_column_width = 20;
            gantt.config.scale_height = 50;

            gantt.config.subscales = [
                {unit: "day", step: 1, date: "%j"}
            ];

            break;
        case "quarter":
            var scaleTemplate = function (date) {
                var dateToStr = gantt.date.date_to_str("%M");
                var endDate = gantt.date.add(gantt.date.add(date, 3, "month"), -1, "month");
                return dateToStr(date) + " - " + dateToStr(endDate);
            };

            gantt.config.scale_unit = "year";
            gantt.config.step = 1;
            gantt.config.date_scale = "%Y";
            gantt.templates.date_scale = null;

            gantt.config.min_column_width = 10;
            gantt.config.scale_height = 90;

            gantt.config.subscales = [
                {unit: "month", step: 3, template: scaleTemplate}
            ];

            break;
        case "year":
            gantt.config.scale_unit = "year";
            gantt.config.step = 1;
            gantt.config.date_scale = "%Y";
            gantt.templates.date_scale = null;

            gantt.config.min_column_width = 10;
            gantt.config.scale_height = 90;

            gantt.config.subscales = [
                {unit: "month", step: 1, date: "%M"}
            ];
            break;
    }
}

function toggleMode(toggle) {
    toggle.enabled = !toggle.enabled;
    if (toggle.enabled) {
        saveConfig();
        zoomToFit();
    } else {
        restoreConfig();
        gantt.render();
    }
}

var cachedSettings = {};
function saveConfig() {
    var config = gantt.config;
    cachedSettings = {};
    cachedSettings.scale_unit = config.scale_unit;
    cachedSettings.date_scale = config.date_scale;
    cachedSettings.step = config.step;
    cachedSettings.subscales = config.subscales;
    cachedSettings.template = gantt.templates.date_scale;
    cachedSettings.start_date = config.start_date;
    cachedSettings.end_date = config.end_date;
}

function restoreConfig() {
    applyConfig(cachedSettings);
}

function applyConfig(config, dates) {
    gantt.config.scale_unit = config.scale_unit;
    if (config.date_scale) {
        gantt.config.date_scale = config.date_scale;
        gantt.templates.date_scale = null;
    }
    else {
        gantt.templates.date_scale = config.template;
    }

    gantt.config.step = config.step;
    gantt.config.subscales = config.subscales;

    if (dates) {
        gantt.config.start_date = gantt.date.add(dates.start_date, -1, config.unit);
        gantt.config.end_date = gantt.date.add(gantt.date[config.unit + "_start"](dates.end_date), 2, config.unit);
    } else {
        gantt.config.start_date = gantt.config.end_date = null;
    }
}


function zoomToFit() {
    var project = gantt.getSubtaskDates(),
        areaWidth = gantt.$task.offsetWidth;

    for (var i = 0; i < scaleConfigs.length; i++) {
        var columnCount = getUnitsBetween(project.start_date, project.end_date, scaleConfigs[i].unit, scaleConfigs[i].step);
        if ((columnCount + 2) * gantt.config.min_column_width <= areaWidth) {
            break;
        }
    }

    if (i == scaleConfigs.length) {
        i--;
    }

    applyConfig(scaleConfigs[i], project);
    gantt.render();
}

// get number of columns in timeline
function getUnitsBetween(from, to, unit, step) {
    var start = new Date(from),
        end = new Date(to);
    var units = 0;
    while (start.valueOf() < end.valueOf()) {
        units++;
        start = gantt.date.add(start, step, unit);
    }
    return units;
}

//Setting available scales
var scaleConfigs = [
    // minutes
    {
        unit: "minute", step: 1, scale_unit: "hour", date_scale: "%H", subscales: [
            {unit: "minute", step: 1, date: "%H:%i"}
        ]
    },
    // hours
    {
        unit: "hour", step: 1, scale_unit: "day", date_scale: "%j %M",
        subscales: [
            {unit: "hour", step: 1, date: "%H:%i"}
        ]
    },
    // days
    {
        unit: "day", step: 1, scale_unit: "month", date_scale: "%F",
        subscales: [
            {unit: "day", step: 1, date: "%j"}
        ]
    },
    // weeks
    {
        unit: "week", step: 1, scale_unit: "month", date_scale: "%F",
        subscales: [
            {
                unit: "week", step: 1, template: function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M");
                    var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                }
            }
        ]
    },
    // months
    {
        unit: "month", step: 1, scale_unit: "year", date_scale: "%Y",
        subscales: [
            {unit: "month", step: 1, date: "%M"}
        ]
    },
    // quarters
    {
        unit: "month", step: 3, scale_unit: "year", date_scale: "%Y",
        subscales: [
            {
                unit: "month", step: 3, template: function (date) {
                    var dateToStr = gantt.date.date_to_str("%M");
                    var endDate = gantt.date.add(gantt.date.add(date, 3, "month"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                }
            }
        ]
    },
    // years
    {
        unit: "year", step: 1, scale_unit: "year", date_scale: "%Y",
        subscales: [
            {
                unit: "year", step: 5, template: function (date) {
                    var dateToStr = gantt.date.date_to_str("%Y");
                    var endDate = gantt.date.add(gantt.date.add(date, 5, "year"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                }
            }
        ]
    },
    // decades
    {
        unit: "year", step: 10, scale_unit: "year", template: function (date) {
            var dateToStr = gantt.date.date_to_str("%Y");
            var endDate = gantt.date.add(gantt.date.add(date, 10, "year"), -1, "day");
            return dateToStr(date) + " - " + dateToStr(endDate);
        },
        subscales: [
            {
                unit: "year", step: 100, template: function (date) {
                    var dateToStr = gantt.date.date_to_str("%Y");
                    var endDate = gantt.date.add(gantt.date.add(date, 100, "year"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                }
            }
        ]
    }
];

$(document).ready(function () {
    init();
    $('body').on('click', '#fullscreen', function () {
        if (!gantt.getState().fullscreen) {
            gantt.expand();
            $('#wrapper').addClass('fullscreen-mode').css({
                'position': 'absolute'
            });
        } else {
            gantt.collapse();
            $('#wrapper').removeClass('fullscreen-mode').css({
                'position': 'static'
            });
        }
        return false;
    });

    $(window).scroll(function () {
        fixOnScroll($('.gantt_task_scale'));
        fixOnScroll($('.gantt_grid_scale'));
    });

    function fixOnScroll(selector)
    {
        var element = $(selector);
        if ($(window).scrollTop() > offsetTop) {
            var top = $(window).scrollTop() - offsetTop;
            $(element).addClass('fixed').css({top: top});
        } else {
            $(element).removeClass('fixed');
        }

        var bottom =
            $('.gantt_layout_y').height() -
            (document.documentElement.clientHeight - $('.gantt_layout_y').offset().top)
            - $(window).scrollTop();
        if (bottom < 0) {
            bottom = 0;
        }
        $('.scrollHor_cell').css({
            bottom: bottom
        });
        $('.gridScroll_cell').css({
            bottom: bottom
        });
    }

    setScaleConfig($("input[name='scale']:checked").val());

    var els = document.querySelectorAll("input[name='scale']");
    for (var i = 0; i < els.length; i++) {
        els[i].onclick = function(e) {
            e = e || window.event;
            var el = e.target || e.srcElement;
            var value = el.value;
            setScaleConfig(value);
            gantt.render();
        };
    }
});
