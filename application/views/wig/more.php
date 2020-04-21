<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/index.js?v=20170508_1"></script>
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

<div class="text-center">
    <div class="wig-header"><?= h($project_title) ?></div>
</div>

<?php if($wigs) : ?>
    <?php foreach ($wigs as $dataKey => $dataWig) : ?>
        <?php $wig = $dataWig['wig'];?>
        <?php $measures = isset($dataWig['measures']) ? $dataWig['measures'] : '';?>
        <div class="wig mar-top-10">
            <div class="header">
                <div class="title">最重要目標</div>
                <div><?= headerName($wig) ?></div>
            </div>
            <div class="body">
                <?= workingTieWrap($wig, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
            </div>
            <div class="footer">
                <div class="start-date">
                    <div>作成日：<?= viewDate($wig['created']) ?></div>
                    <div>終了日：<?= viewDate($wig['created']) ?></div>
                </div>
            </div>
        </div>
        <?php if($measures): ?>
            <?php foreach ($measures as $measureKey => $measureItem) : ?>
                <div class="measure">
                    <div class="header">
                        <div class="title"><span class="text-disabled">最重要目標 > </span>先行指標</div>
                        <div><?= headerName($measureItem) ?></div>
                    </div>
                    <div class="body">
                        <?= workingTieWrap($measureItem, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
                    </div>
                    <div class="footer">
                        <div class="start-date">
                            <div>作成日：<?= viewDate($measureItem['created']) ?></div>
                            <div>終了日：<?= viewDate($measureItem['closed']) ?></div>
                        </div>
                    </div>
                </div>
                <?php $commitments = isset($measureItem['commitments']) ? $measureItem['commitments'] : ''; ?>
                <?php if($commitments): ?>
                    <div class="commitment" style="background-color: white">
                        <div class="header">
                            最重要目標 > 先行指標 > <span class="text-blue">コミットメント</span>
                        </div>
                        <div class="body">
                            <?php foreach ($commitments as $commitmentsKey => $commitmentsKeyItem) : ?>
                                <div class="mar-top-10 kikan">期間 : <?= add_day($week_start_monday, $commitmentsKeyItem['start_monday'])?></div>
                                <div>
                                    <div class="pull-left commitment-result">
                                        <?= form_checkbox([
                                            'class' => 'commitment-result-checkbox',
                                            'disabled' => 'disabled',
                                            'value' => ($commitmentsKeyItem['result'] == ON),
                                            'checked' => ($commitmentsKeyItem['result'] == ON)
                                        ]); ?>
                                    </div>
                                    <div class="pull-left commitment-title">
                                        <?= $commitmentsKeyItem['title'] ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <div class="mar-top-10 mar-lr-10">
        <div class="alert alert-danger text-center">
            データがありません
        </div>
    </div>
<?php endif; ?>
<br>
