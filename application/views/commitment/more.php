<?php
    $week_start_monday = $this->setting['week_start_monday'];
    function add_day($settingWeekStartMonday, $day) {
        $week_start_day = strtotime($day);

        if($settingWeekStartMonday == ON) {
            return $day . ' 〜 ' . date(DATE_UI_FORMAT, strtotime('+6 day', $week_start_day));
        } else {
            return date(DATE_UI_FORMAT, strtotime('-1 day', $week_start_day)) . ' 〜 ' . date(DATE_UI_FORMAT, strtotime('+5 day', $week_start_day));
        }
    }
?>
<?php if($commitments): ?>
    <div class="mar-top-20 mar-lr-10">
        <ul class="list-group">
            <?php foreach ($commitments as $start_day => $commitment) : ?>
                <li class="list-group-item justify-content-between">
                    <div>
                        <strong><?= add_day($week_start_monday, $commitment['start_monday'])?></strong>
                    </div>
                    <div class="pull-left commitment-result">
                        <?= form_checkbox([
                            'class' => 'commitment-result-checkbox',
                            'disabled' => 'disabled',
                            'value' => ($commitment['result'] == ON),
                            'checked' => ($commitment['result'] == ON)
                        ]); ?>
                    </div>
                    <div class="pull-left commitment-title">
                        <?= h($commitment['title']) ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="clearfix"></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="mar-top-10 mar-lr-10">
        <div class="alert alert-danger text-center">
            データがありません
        </div>
    </div>
<?php endif; ?>
