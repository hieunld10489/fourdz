$(function() {
    $('#btnZoneRateRed').click(function() {
        var greenVal = parseInt($('#input_zone_rate_green').val());
        var redVal = parseInt($('#input_zone_rate_red').val());
        var rateRedValue;
        if(!$.isNumeric(redVal)) {
            rateRedValue = greenVal-1;
        } else if(redVal < 0) {
            rateRedValue = greenVal-1;
        } else if(redVal >= greenVal) {
            rateRedValue = greenVal-1;
        } else {
            rateRedValue = redVal;
        }
        $('#zone_rate_red').val(rateRedValue);
        $('#txt_zone_rate_red').text(rateRedValue);
        $('#input_zone_rate_red').val(rateRedValue);
    });

    $('#btnZoneRateGreen').click(function() {
        var greenVal = parseInt($('#input_zone_rate_green').val());
        var redVal = parseInt($('#input_zone_rate_red').val());
        var rateGreenValue;

        if(!$.isNumeric(greenVal)) {
            rateGreenValue = redVal+1;
        } else if(greenVal < 0) {
            rateGreenValue = redVal+1;
        } else if(greenVal > 100) {
            rateGreenValue = 100;
        } else if(greenVal <= redVal) {
            rateGreenValue = redVal+1;
        } else {
            rateGreenValue = greenVal;
        }
        $('#zone_rate_green').val(rateGreenValue);
        $('#txt_zone_rate_green').text(rateGreenValue);
        $('#input_zone_rate_green').val(rateGreenValue);
    });
});

function addCategory() {
    bootbox.prompt("新規カテゴリー名", function(result) {
        if (result) {
            if(result.length > 50) {
                result = result.substring(0, 50);
            }
            var rowLength = $('.row').last().attr('row');

            if(!rowLength || typeof(rowLength) == 'undefined') {
                rowLength = 1;
            } else {
                rowLength = parseInt(rowLength) + 1;
            }
            var html =  '<div class="row mar-top-10" row="'+rowLength+'">'
                    + '<input name="category[' + rowLength + '][title]" value="'+result+'" type="hidden">'
                    + '<div class="col-xs-9">' + result + '</div>'
                    + '<div class="col-xs-3">'
                    +   '<button class="btn btn-default btn-sm" type="button" onclick="deleteCategory($(this), 0)">削除</button>'
                    + '</div>'
                + '</div>';

            $('#category-list').append(html);
        }
    });
}

function deleteCategory(me, num) {
    bootbox.confirm({
        message: 'カテゴリーを削除しますか？',
        buttons: {
            confirm: {
                label: '確認',
                className: 'btn-danger'
            },
            cancel: {
                label: 'キャンセル',
                className: 'btn-default'
            }
        },
        callback: function(result) {
            if (result) {
                if(num == 0) {
                    me.parent().parent().remove();
                } else {
                    me.parent().parent().append('<input name="category[' + num + '][delete]" value="true" type="hidden">');
                    me.parent().parent().hide();
                }
            }
        }
    });
}
