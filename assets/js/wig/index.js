$(function() {

    processGraph($(".working-progress-value-now"), 'jisseki-per');
    processGraph($(".working-progress-value"), 'yotei-per');

    $(".wig .header, .wig .body").click(function (e) {
        openFooter('wig', $(this));
    });

    $(".measure .header, .measure .body").click(function (e) {
        openFooter('measure', $(this));
    });

    $(".commitment .header").click(function (e) {
        openFooter('commitment', $(this));
    });

    $(".this-week").click(function (e) {
        var dataTarget = $(this).data("target");

        if(dataTarget) {
            $(this).addClass("active");
            $(dataTarget).show();
        }
        if($(this).next().length) {
            $(this).next().removeClass("active");
            var dataNextTarget = $(this).next().data("target");
            if(dataNextTarget) {
                $(dataNextTarget).hide();
            }
        }
    });

    $(".next-week").click(function (e) {
        var dataTarget = $(this).data("target");

        if(dataTarget) {
            $(this).addClass("active");
            $(dataTarget).show();
        }
        if($(this).prev().length) {
            $(this).prev().removeClass("active");
            var dataPrevTarget = $(this).prev().data("target");
            if(dataPrevTarget) {
                $(dataPrevTarget).hide();
            }
        }
    });

});

function processGraph(element, attrKey) {
    if(element.length) {
        element.each(function(v, e){
            var me = $(e)
                , meParent = $(me.parent())
                , meWidth = me.width()
                , parentWidth = $(me.parent()).width()
                , jissekiPer = parseInt(meParent.attr(attrKey));

            if(jissekiPer >= 90) {
                me.css({right: ((meWidth-((meWidth)*0.75)))*(-1)});
                me.addClass("working-right-75");
            } else if(jissekiPer <= 10) {
                me.css({right: ((meWidth-((meWidth)*0.25)))*(-1)});
                me.addClass("working-right-25");
            } else {
                me.css({right: (meWidth/2)*(-1)});
            }
            meParent.width(meParent.attr(attrKey) + '%');
            me.show();
        });
    }
}

function openFooter (name, me) {
    var wigId = me.attr(name + '-id');
    if(wigId) {
        var eWigFooter = $($("." + name + " .footer["+name+"-id='"+wigId+"']")[0]);
        if(eWigFooter.length > 0) {
            if(eWigFooter.hasClass('hidden')) {
                //fa-chevron-circle-up
                eWigFooter.removeClass('hidden');
                eWigFooter.show();
            } else {
                eWigFooter.addClass('hidden');
                eWigFooter.hide();
            }
        }
    }
}

function wigCloseConfirm(id, url) {
    bootbox.confirm({
        message: '最重要目標を終了しますか？<br>（終了した最重要目標は元に戻せません）',
        buttons: {
            confirm: {
                label: '終了',
                className: 'btn-danger'
            },
            cancel: {
                label: 'キャンセル',
                className: 'btn-default'
            }
        },
        callback: function (result) {
            var csrfClose = $('#commitment-token-status').attr('csrf-token');
            if (result && id && url && csrfClose) {
                apiPost(url, {
                    'cicsrftoken': csrfClose,
                    'id': id
                }, function(res) {
                    window.location.reload();
                });
            }
        }
    });
    return false;
}

function measureCloseConfirm(id, url, type) {
    // ダイアログのボタン
    var buttonItems = {};

    // キャンセルボタン
    buttonItems.cancel = {
        label: 'キャンセル',
        className: 'btn-default',
        callback: function() {
            ;
        }
    };

    // 終了ボタン
    buttonItems.main = {
        label: '終了',
        className: 'btn-danger',
        callback: function(result) {
            var csrfClose = $('#commitment-token-status').attr('csrf-token');
            if (result && id && url && csrfClose) {
                apiPost(url, {
                    'cicsrftoken': csrfClose,
                    'id': id,
                    'clone': 'n'
                }, function(res) {
                    window.location.reload();
                });
            }
        }
    };

    // プロジェクト型ではない場合のみ、終了して複製ボタンを表示
    if (type !== 1) {
        buttonItems.clone = {
            label: '終了して複製',
            className: 'btn-danger',
            callback: function(result) {
                var csrfClose = $('#commitment-token-status').attr('csrf-token');
                if (result && id && url && csrfClose) {
                    apiPost(url, {
                        'cicsrftoken': csrfClose,
                        'id': id,
                        'clone': 'y'
                    }, function(res) {
                        window.location.reload();
                    });
                }
            }
        };
    }

    // ダイアログを表示
    bootbox.dialog({
        message: '先行指標を終了しますか？<br>（終了した先行指標は元に戻せません）',
        buttons: buttonItems
    });

    return false;
}
