<script type="text/javascript" src="<?= site_url() ?>assets/js/project/index.js?v=20170523_1"></script>
<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/commitment_result_checkbox.js?v=20170525_1"></script>

<div class="frame">
    <div class="text-center">
        <div class="project-header">
            My4DXテーマ⼀覧
            <div class="child">THEME LIST</div>
        </div>
    </div>

    <?php if($projects) : ?>
        <?php foreach($projects as $projectKey => $project) : ?>
            <div class="wig">
                <div class="start-end">
                    <b class="text-grey">START：</b>
                    <?= viewDate($project['created']) ?>　
                    <img src="<?= site_url('assets/images/icon_start_end.png') ?>" width="12">
                </div>
                <div class="header pad-top-10 pad-bottom-5">
                    <?=h($project['title'])?>
                </div>
                <div class="body text">
                    <?=nl2br(h($project['content']))?>
                    <div class="commitents">
                        <div class="index-commitments-title">今週のコミットメント</div>
                        <?php $target_comm_count = 0; ?>
                        <?php foreach ($project_commitments as $commitmentThisWeekKey => $commitmentThisWeekItem) : ?>
                            <?php if ($project['id'] != $commitmentThisWeekItem['project_id']) { continue; } else { $target_comm_count++; } ?>
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
                                    <?= h($commitmentThisWeekItem['title']) ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($target_comm_count == 0) : ?>
                            <div class="no-commitment">
                                （コミットメントが登録されていません）
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer">
                    <div class="footer-btn w-3i">
                        <div class="pull-left disable">
                            <a class="btn-circle-sm" href="<?=site_url('project/update/') . h($project['id']) ?>">
                                <img src="<?= site_url('assets/images/icon_edit.png') ?>"><br>
                                編集
                            </a>
                        </div>
                        <div class="pull-left">
                            <a class="btn-circle-lg" href="<?=site_url('wig/index/').h($project['id'])?>">
                                <img src="<?= site_url('assets/images/icon_list.png') ?>"><br>
                                スコアボード⼀覧
                            </a>
                        </div>
                        <div class="pull-left disable">
                            <a class="btn-circle-sm" onclick="return postCloseConfirm(<?= h($project['id']) ?>)">
                                <img src="<?= site_url('assets/images/icon_close.png') ?>"><br>
                                終了
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <br>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mar-lr-10 mar-top-10">
            <div class="alert alert-danger text-center">
                データがありません
            </div>
        </div>
    <?php endif; ?>
    <div class="head-button">
        <div class="pull-left" style="width: 12%">　</div>
        <div class="pull-left" style="width: 38%">
            <a class="btn-circle-lg" href="<?= site_url('project/create') ?>">
                <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                テーマ新規作成
            </a>
        </div>
        <div class="pull-left" style="width: 38%">
            <a class="btn-circle-lg" href="<?= site_url('project/more') ?>">
                <img src="<?= site_url('assets/images/icon_past.png') ?>"><br>
                過去のテーマ
            </a>
        </div>
        <div class="pull-left" style="width: 12%">　</div>
        <div class="clearfix"></div>
    </div>
</div>
<br>

<div id="url" class="hidden"
    url-commitment-update-type="<?= site_url('commitment/change_status') ?>"
    url-close="<?= site_url('project/close') ?>"></div>
<div id="token" csrf-close="<?= $this->security->get_csrf_hash() ?>"></div>
<div id="commitment-token-status" csrf-token="<?= $this->security->get_csrf_hash() ?>"></div>
