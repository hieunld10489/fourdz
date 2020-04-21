function postCloseConfirm(project_id) {
    bootbox.confirm({
        message: 'テーマを終了しますか？<br>（終了したテーマは元に戻せません）',
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
            var urlClose = $('#url').attr('url-close');
            var csrfClose = $('#token').attr('csrf-close');
            if (result && urlClose && csrfClose) {
                apiPost(urlClose, {
                    'cicsrftoken': csrfClose,
                    'project_id': project_id
                }, function(res) {
                    window.location.reload();
                });
            }
        }
    });
    return false;
}
