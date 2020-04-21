$(function() {

    $(".commitment-result-checkbox").change(function() {
        var urlCommitmentUpdateType = $('#url').attr('url-commitment-update-type');
        var commitmentId = $(this).attr('commitment-id');
        if(urlCommitmentUpdateType) {
            var result = 0;
            if($(this).is(":checked")) {
                result = 1;
            }
            apiPost(urlCommitmentUpdateType, {
                'cicsrftoken': $('#commitment-token-status').attr('csrf-token'),
                'commitment_id': commitmentId,
                'result': result
            }, function(res) {
                if(res) {
                    $('#commitment-token-status').attr('csrf-token', res)
                } else {
                    window.location.reload();
                }
            });
        }
    });

});