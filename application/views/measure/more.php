<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/index.js?v=20170508_1"></script>

<?php if($measures) : ?>
    <div class="mar-top-20"></div>
    <?php foreach ($measures as $measure) : ?>
        <?php
        $graphValue = genGraph($measure, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']);

        $yoteiValue = $graphValue['yotei_value'];
        $yoteiPercent = $graphValue['yotei_percent'];
        $jissekiValue = $graphValue['jisseki_value'];
        $jissekiPercent = $graphValue['jisseki_percent'];

        $fromDate = $graphValue['from_date'];
        $toDate = $graphValue['to_date'];
        $fromValue = $graphValue['from_value'];
        $toValue = $graphValue['to_value'];

        $progressPercent = $graphValue['progress_percent'];
        $progressColor = $graphValue['progress_color'];
        $yoteiColor = $graphValue['yotei_color'];
        ?>

        <div class="wig">
            <div class="header pad-top-10 pad-bottom-5">
                <?= headerName($measure) ?>
            </div>
            <div class="body">
                <?= workingTieWrap($measure, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
            </div>
            <div class="footer">
                <div class="start-date">
                    作成日：<?= viewDate($measure['created']) ?>
                    <br>
                    終了日：<?= isset($measure['closed']) ? viewDate($measure['closed']) : '' ?>
                </div>
            </div>
        </div><br>
    <?php endforeach; ?>
<?php else: ?>
    <div class="mar-top-10 mar-lr-10">
        <div class="alert alert-danger text-center">
            データがありません
        </div>
    </div>
<?php endif; ?>
<br>
