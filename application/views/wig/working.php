<script type="text/javascript" src="<?= site_url() ?>assets/js/wig/index.js?v=20170508_1"></script>
<script type="text/javascript" src="<?= site_url() ?>assets/js/more.js"></script>

<div class="wig mar-top-20">
    <div class="header">
        <div class="title">最重要目標</div>
        <div><?= headerName($wig) ?></div>
    </div>
    <?= form_open('wig/working/'.h($wig_id), ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
    <div class="body">
        <?= workingTieWrap($wig, $this->setting['zone_rate_green'], $this->setting['zone_rate_red']) ?>
        <br>
        <div class="text-center">
            <?php if($wig['type'] == ON) : ?>
                <table id="wig-pro" class="table">
                    <thead><tr><th class="text-left" colspan="3">項目</th></tr></thead>
                    <tbody>
                        <?php $wigPro = $this->input->post('wig_pro'); ?>
                        <?php if($wigPro) : ?>
                            <?php foreach ($wigPro as $wigProKey => $wigProItem) : ?>
                                <?php $row = $wigProKey + 1 ?>
                                <tr>
                                    <td class="text-left" width="50%">
                                        <?= $wigProItem['title'] ?>
                                    </td>
                                    <td class="text-center" width="20%">
                                        （<?= $wigProItem['tasedo'] ?>％）
                                    </td>
                                    <td>
                                        目標日：<?= viewDate($wigProItem['mokuhyoubi']) ?><br>
                                        <div class="pull-left" style="line-height: 34px;">達成日　</div>
                                        <div class="pull-left">
                                            <input name="wig_pro[<?= $row ?>][id]" type="hidden" value="<?= $wigProItem['id'] ?>">
                                            <input name="wig_pro[<?= $row ?>][tasebi]" type="date" value="<?= $wigProItem['tasebi'] ?>" class="form-control">
                                            <input name="wig_pro[<?= $row ?>][tasedo]" type="hidden" value="<?= $wigProItem['tasedo'] ?>">
                                        </div>
                                        <div class="clearfix"></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            <input type="hidden" name="wig_id" value="<?=h($wig_id)?>" />
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
