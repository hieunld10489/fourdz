$(function() {
    $(".btn-menu").click(function (e) {
        e.preventDefault();
        $("#process-sidebar-wrapper").show();
        $("#wrapper").toggleClass("toggled");
    });

    $("#process-sidebar-wrapper").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $("#process-sidebar-wrapper").hide();
    });


    // 各ページ固有の初期化処理がある場合は呼び出す
    if (typeof initialize_page == 'function') {
        initialize_page();
    }

    if($('.to-top').length > 0) {
        if($(window).height() >= $('.content-2x')[0].scrollHeight) {
            $('.to-top').hide();
        }
    }

    if($('.mokuji').length > 0) {
        $('.mokuji ul li a').click(function(){
            pr($(this).attr('data'));
            $(window).href = $(this).attr('data');
        });
    }

    bootbox.setDefaults({
        locale: "ja"
    });
});


// AJAXのPOSTリクエスト
function apiPost(requestPath, params, callBackFunc, obj) {
    var error500 = $('#error-url').attr('error-500');
    $.post(requestPath, params, function (data, textStatus, jqXHR) {
        if (textStatus == "success") {
            if (callBackFunc) {
                callBackFunc(data, obj);
            }
        } else {
            //window.location.replace(error500);
        }
    }).fail(function (jqXHR, textStatus, error) {

        //window.location.replace(error500);
    });
}

// AJAXのGETリクエスト
function apiLoad(requestPath, params, callBackFunc, obj) {
    $.get(requestPath, params, function (data, textStatus, jqXHR) {
        if (textStatus == "success") {
            if (callBackFunc) {
                callBackFunc(data, obj);
            }
        } else {
            //window.location.replace(error500);
        }
    }).fail(function (jqXHR, textStatus, error) {

        //window.location.replace(error500);
    });
}

function gotoBack() {
    var backButton = $("#header-back-button");
    if (backButton != null) {
        var url = backButton.attr("href");
        if (url != undefined) {
            location.href = backButton.attr("href");
        }
    }
}

function pr(v) {
    console.log(v);
}
