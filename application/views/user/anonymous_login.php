<?php if(isset($message)):?>
    <div class="mar-top-10">
        <?= $message ?>
    </div>
<?php endif; ?>

<div style="width:1px; height:1px;">
    <?= form_open('user/anonymous_login', array('name' => 'anonymous')) ?>
        <input id="udid" type="hidden" name="udid">
    <?= form_close() ?>
</div>

<script type="text/javascript">
    function anonymousSubmit(udid) {
        if (udid != null) {
            document.anonymous.elements["udid"].value = udid;
        }
        document.anonymous.submit();
    }
</script>

<button type="button" name="button" onclick="anonymousSubmit('test');">test</button>
