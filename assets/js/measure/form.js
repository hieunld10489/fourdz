$(function() {
    $("#type").click(function () {
        if ($(this).is(':checked')) {
            $('#type-1').show();
            $('#type-0').hide();
        } else {
            $('#type-1').hide();
            $('#type-0').show();
        }
    });

    $("#koumoku-tsuika").click(function () {
        var lastRow = $('.wig-pro-row').last().attr('row');

        if(!lastRow || typeof(lastRow) == 'undefined') {
            lastRow = 1;
        } else {
            lastRow = parseInt(lastRow) + 1;
        }

        var html =
            '<tr class="wig-pro-row" row="' + lastRow + '">'
            +'<th class="wig-pro-delete text-center" scope="row" onclick="deleteWigProRow($(this), '+0+')"><img src="'+$('#img-url').attr('src')+'"><br></th>'
                +'<td class="wig-pro-title"><input name="measure_pro['+lastRow+'][title]" type="text" maxlength="64" class="form-control"></td>'
                +'<td class="wig-pro-tasedo"><input name="measure_pro['+lastRow+'][tasedo]" type="text" maxlength="3" class="form-control"></td>'
                +'<td class="wig-pro-mokuhyoubi"><input name="measure_pro['+lastRow+'][mokuhyoubi]" type="date" class="form-control wig-date"></td>'
            +'</tr>';

        $('#wig-pro tbody').append(html);
    });

    $("#unit").change(function() {
        $("#re_unit").val($(this).val());
    });
});

function deleteWigProRow(me, row) {
    if(parseInt(row) > 0) {
        me.parent().append('<input name="measure_pro[' + row + '][delete]" value="true" type="hidden">');
        me.parent().hide();
    } else {
        me.parent().remove();
    }
}
