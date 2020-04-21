<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/index.js?v=20170530_1"></script>
<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/commitment_result_checkbox.js?v=20170530_1"></script>

<div class="frame">
    <div class="text-center">
        <div class="wig-header"><?= h($project_title) ?></div>
        <div class="head-button">
            <div class="pull-left" style="width: 12%">　</div>
            <div class="pull-left" style="width: 38%">
                <a class="btn-circle-lg" href="<?= site_url('wig/create/') . h($project_id) ?>">
                    <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                    <div>最重要目標新規作成</div>
                </a>
            </div>
            <div class="pull-left" style="width: 38%">
                <a class="btn-circle-lg" href="<?= site_url('wig/more/') . h($project_id) ?>">
                    <img src="<?= site_url('assets/images/icon_past.png') ?>"><br>
                    <div>過去の最重要目標</div>
                </a>
            </div>
            <div class="pull-left" style="width: 12%">　</div>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php if($wigs) : ?>
        <?php foreach ($wigs as $dataKey => $dataWig) : ?>
            <?php $wig = $dataWig['wig'];?>
            <?php $measures = isset($dataWig['measures']) ? $dataWig['measures'] : '';?>

            <div class="wig">
                <div class="header" wig-id="<?= $wig['id'] ?>">
                    <div class="title">最重要目標</div>
                    <div><?= headerName($wig) ?></div>
                </div>
                <div class="body" wig-id="<?= $wig['id'] ?>">
                    <!-- グラフ部分 -->
                    <?= workingTieWrap($wig, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
                </div>
                <div class="footer hidden" wig-id="<?= $wig['id'] ?>">
                    <div class="start-end">
                        <b class="text-grey">START：</b>
                        <?= viewDate($wig['created']) ?>　
                        <img src="<?= site_url('assets/images/icon_start_end.png') ?>" width="12">
                    </div>
                    <div class="footer-btn w-4i">
                        <div class="pull-left disable">
                            <a class="btn-circle-sm" href="<?= site_url('wig/update/') . h($wig['id']) ?>">
                                <img src="<?= site_url('assets/images/icon_edit.png') ?>"><br>
                                編集
                            </a>
                        </div>
                        <div class="pull-left">
                            <a class="btn-circle-lg" href="<?= site_url('wig/working/') . h($wig['id']) ?>">
                                <img src="<?= site_url('assets/images/icon_enter.png') ?>"><br>
                                実績を入力
                            </a>
                        </div>
                        <div class="pull-left">
                            <a class="btn-circle-lg" href="<?= site_url('measure/create/') . h($wig['id']) ?>">
                                <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                                先行指標新規作成
                            </a>
                        </div>
                        <div class="pull-left disable">
                            <a class="btn-circle-sm" onclick="wigCloseConfirm('<?= h($wig['id']) ?>', '<?= site_url('wig/close') ?>')">
                                <img src="<?= site_url('assets/images/icon_close.png') ?>"><br>
                                終了
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <?php if($measures): ?>
                <?php foreach ($measures as $measureKey => $measureItem) : ?>
                    <?php $commitments = isset($measureItem['commitments']) ? $measureItem['commitments'] : ''; ?>
                    <div class="measure">
                        <div class="header" measure-id="<?= $measureItem['id'] ?>">
                            <div class="title"><span class="text-disabled">最重要目標 > </span>先行指標</div>
                            <div><?= headerName($measureItem) ?></div>
                            <!-- グラフ部分 -->
                        </div>
                        <div class="body" measure-id="<?= $measureItem['id'] ?>">
                            <?= workingTieWrap($measureItem, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
                        </div>
                        <div class="footer hidden" measure-id="<?= $measureItem['id'] ?>">
                            <div class="start-end">
                                <b class="text-grey">START：</b>
                                <?= viewDate($measureItem['created']) ?>　
                                <img src="<?= site_url('assets/images/icon_start_end.png') ?>" width="12">
                            </div>
                            <div class="footer-btn w-3i">
                                <div class="pull-left disable">
                                    <a class="btn-circle-sm" href="<?= site_url('measure/update/') . h($measureItem['id']) ?>">
                                        <img src="<?= site_url('assets/images/icon_edit.png') ?>"><br>
                                        編集
                                    </a>
                                </div>
                                <div class="pull-left">
                                    <a class="btn-circle-lg" href="<?= site_url('measure/working/') . h($measureItem['id']) ?>">
                                        <img src="<?= site_url('assets/images/icon_enter.png') ?>"><br>
                                        実績を入力
                                    </a>
                                </div>
                                <div class="pull-left disable">
                                    <a class="btn-circle-sm" onclick="return measureCloseConfirm('<?= h($measureItem['id']) ?>', '<?= site_url('measure/close') ?>', '<?= intval($measureItem['type']) ?>')">
                                        <img src="<?= site_url('assets/images/icon_close.png') ?>"><br>
                                        終了
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <?php $commitmentThisWeek = isset($commitments['this_week']) ? $commitments['this_week'] : ''; ?>
                    <?php $commitmentNextWeek = isset($commitments['next_week']) ? $commitments['next_week'] : ''; ?>
                    <div class="commitment">
                        <div class="header" commitment-id="<?= $measureItem['id'] ?>">
                            最重要目標 > 先行指標 > <span class="text-blue">コミットメント</span>
                        </div>

                        <div class="footer hidden" commitment-id="<?= $measureItem['id'] ?>">
                            <?php if($commitments): ?>
                                <div class="body">
                                    <ul class="nav nav-tabs">
                                        <li class="this-week active" data-target="#this-week-tab-<?= $measureItem['id'] ?>">
                                            <a>今週</a>
                                        </li>
                                        <?php if($commitmentNextWeek): ?>
                                            <li class="next-week" data-target="#next-week-tab-<?= $measureItem['id'] ?>">
                                                <a>来週</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                    <?php
                                    if($this->setting['week_start_monday'] == ON) {
                                        $strThisDateFrom = date(DATE_UI_FORMAT, strtotime('monday this week'));
                                        $strThisDateTo = date(DATE_UI_FORMAT, strtotime('sunday this week'));
                                        $strNextDateFrom = date(DATE_UI_FORMAT,strtotime('monday next week'));
                                        $strNextDateTo = date(DATE_UI_FORMAT,strtotime('sunday next week'));
                                        $strThisDate = $strThisDateFrom . '～' . $strThisDateTo;
                                        $strNextDate = $strNextDateFrom . '～' . $strNextDateTo;
                                    } else {
                                        $strThisDateFrom = date(DATE_UI_FORMAT,strtotime('sunday previous week'));
                                        $strThisDateTo = date(DATE_UI_FORMAT,strtotime('saturday this week'));
                                        $strNextDateFrom = date(DATE_UI_FORMAT,strtotime('sunday this week'));
                                        $strNextDateTo = date(DATE_UI_FORMAT,strtotime('saturday next week'));
                                        $strThisDate = $strThisDateFrom . '～' . $strThisDateTo;
                                        $strNextDate = $strNextDateFrom . '～' . $strNextDateTo;
                                    }
                                    ?>
                                    <?php if($commitmentThisWeek): ?>
                                        <div id="this-week-tab-<?= $measureItem['id'] ?>" >
                                            <div style="padding: 0 15px;">
                                                <div class="kikan">期間 : <?= $strThisDate ?></div>
                                                <?php foreach ($commitmentThisWeek as $commitmentThisWeekKey => $commitmentThisWeekItem) : ?>
                                                    <div class="mar-top-5">
                                                        <div class="pull-left commitment-result">
                                                            <?= form_checkbox([
                                                                'class' => 'commitment-result-checkbox',
                                                                'name' => 'commitment_result',
                                                                'id' => 'commitment-' . $commitmentThisWeekItem['id'],
                                                                'commitment-id' => $commitmentThisWeekItem['id'],
                                                                'csrf-token' =>  $this->security->get_csrf_hash(),
                                                                'value' => ($commitmentThisWeekItem['result'] == 1),
                                                                'checked' => ($commitmentThisWeekItem['result'] == 1)
                                                            ]); ?>
                                                        </div>
                                                        <div class="pull-left commitment-title">
                                                            <a href="<?= site_url('commitment/update/' . $commitmentThisWeekItem['id']) ?>">
                                                                <?= h($commitmentThisWeekItem['title']) ?>
                                                            </a>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($commitmentNextWeek): ?>
                                        <div id="next-week-tab-<?= $measureItem['id'] ?>" style="display: none;">
                                            <div style="padding: 0 15px;">
                                                <div class="kikan">期間 : <?= $strNextDate ?></div>
                                                <?php foreach ($commitmentNextWeek as $commitmentNextWeekKey => $commitmentNextWeekItem2) : ?>
                                                    <div class="mar-top-5">
                                                        <div class="pull-left commitment-result">
                                                            <?= form_checkbox([
                                                                'class' => 'commitment-result-checkbox',
                                                                'name' => 'commitment_result',
                                                                'id' => 'commitment-' . $commitmentNextWeekItem2['id'],
                                                                'commitment-id' => $commitmentNextWeekItem2['id'],
                                                                'csrf-token' =>  $this->security->get_csrf_hash(),
                                                                'value' => ($commitmentNextWeekItem2['result'] == ON),
                                                                'checked' => ($commitmentNextWeekItem2['result'] == ON)
                                                            ]); ?>
                                                        </div>
                                                        <div class="pull-left commitment-title">
                                                            <a href="<?= site_url('commitment/update/' . $commitmentNextWeekItem2['id']) ?>">
                                                                <?= h($commitmentNextWeekItem2['title']) ?>
                                                            </a>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="footer-btn">
                                <a class="btn-circle-lg" href="<?= site_url('commitment/create/' . $measureItem['id']) ?>">
                                    <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                                    新規作成
                                </a>
                            </div>
                        </div>
                    </div>
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
</div>

<div id="url" class="hidden"
     url-commitment-update-type="<?= site_url('commitment/change_status') ?>"
     url-wig-close="<?= site_url('wig/close') ?>"
></div>
<div id="commitment-token-status" csrf-token="<?= $this->security->get_csrf_hash() ?>"></div>
