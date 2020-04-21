
/**
 * 終了確認ダイアログを表示し、Yesの場合は削除を実行する.
 * @param  {string}  targetName 対象の名前
 * @param  {Object}  formElem   削除を実行するフォーム要素
 * @param  {string}  action     操作の名前
 * @return {boolean}            false(固定)
 */
function closeConfirm(targetName, formElem, action) {
    if (!action) {
        action = '終了';
    }
    bootbox.confirm({
        message: targetName + 'を' + action + 'しますか？<br>（' + action + 'した' + targetName + 'は元に戻せません）',
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
        callback: function (result) {
            if (result) {
                formElem.submit();
            }
        }
    });
    /*
    // プロジェクト削除の確認ダイアログ(OnsenUIバージョン)
    ons.notification.confirm({
        title: '確認',
        message: targetName + 'を終了しますか？<br>（終了した' + targetName + 'は元に戻せません）',
        callback: function(answer) {
            if (answer) {
                formElem.submit();
            }
        }
    });
    */
    return false;
}
