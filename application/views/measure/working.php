<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/index.js?v=20170508_1"></script>
<script type="text/javascript" src="<?= site_url() ?>assets/js/more.js"></script>

<div class="wig mar-top-20">
    <div class="header">
        <div class="title">先行指標</div>
        <div><?= headerName($measure) ?></div>
    </div>
    <?= form_open('measure/working/'.h($measure['id']), ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
    <div class="body">
        <?= workingTieWrap($measure, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
        <br>
        <div class="text-center">
            <?php if($measure['type'] == ON) : ?>
                <table id="wig-pro" class="table">
                    <thead><tr><th class="text-left" colspan="3">項目</th></tr></thead>
                    <tbody>
                        <?php $measurePro = $this->input->post('measure_pro'); ?>
                        <?php if($measurePro) : ?>
                            <?php foreach ($measurePro as $measureProKey => $measureProItem) : ?>
                                <?php $row = $measureProKey + 1 ?>
                                <tr>
                                    <td class="text-left" width="50%">
                                        <?= $measureProItem['title'] ?>
                                    </td>
                                    <td class="text-center" width="20%">
                                        （<?= $measureProItem['tasedo'] ?>％）
                                    </td>
                                    <td>
                                        目標日：<?= viewDate($measureProItem['mokuhyoubi']) ?><br>
                                        <div class="pull-left" style="line-height: 34px;">達成日　</div>
                                        <div class="pull-left">
                                            <input name="measure_pro[<?= $row ?>][id]" type="hidden" value="<?= $measureProItem['id'] ?>">
                                            <input name="measure_pro[<?= $row ?>][tasebi]" type="date" value="<?= $measureProItem['tasebi'] ?>" class="form-control">
                                            <input name="measure_pro[<?= $row ?>][tasedo]" type="hidden" value="<?= $measureProItem['tasedo'] ?>">
                                        </div>
                                        <div class="clearfix"></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <hr class="grey">
            <?php else: ?>
                <div class="row">
                    <div class="col-xs-3 text-right" style="line-height: 30px">
                        <label for="now-value">実績<span class="required">*</span></label></label>
                    </div>
                    <div class="col-xs-4">
                        <?php
                        if($this->input->post('now_value')) {
                            $intNowValue = h($this->input->post('now_value'));
                        } else {
                            $intNowValue = 0;
                        }
                        ?>
                        <input class="form-control" type="text" maxlength="50" id="now-value" name="now_value" value="<?= $intNowValue ?>"/>
                    </div>
                    <div class="col-xs-5 text-left">
                        <a id="add-val-button">
                            <img src="<?= site_url('assets/images/btn_up.png') ?>" width="35">
                        </a>
                        <a id="minus-val-button">
                            <img src="<?= site_url('assets/images/btn_down.png') ?>" width="35">
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <input type="hidden" name="measure_id" value="<?=h($measure['id'])?>" />
        </div>
    </div>
    <div class="footer">
        <div class="footer-btn btn-save">
            <a class="btn-circle-lg" onclick="$('#form-data').submit()">
                <img src="<?= site_url('assets/images/icon_storage.png') ?>"><br>
                保存
            </a>
        </div>
    </div>
    <?= form_close() ?>
</div>
<br>
