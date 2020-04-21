<script type="text/javascript" src="<?= site_url() ?>assets/js/setting/index.js?ver=20170526_1"></script>

<div class="content-form">
    <?= form_open('setting/index', ['id' => 'form-data']) ?>
        <div class="mar-lr-5 pad-top-10 pad-bottom-10">
            <div>
                <label for="name">　ニックネーム<span class="required">*</span></label>
                <input class="form-control" type="text" id="name" name="name" value="<?=h($this->input->post('name'))?>" maxlength="40"/>
            </div>

            <div class="mar-top-20">
                <strong>　週のはじまり</strong>

                <?php
                    $weekStartMondayChecked = 'checked="checked"';
                    $weekStartMonday = $this->input->post('week_start_monday');
                ?>

                <div class="radio">
                    <label><input type="radio" name="week_start_monday" value="0" <?= ($weekStartMonday == 0) ? $weekStartMondayChecked : '' ?>> 日曜はじまり</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="week_start_monday" value="1"<?= ($weekStartMonday == 1) ? $weekStartMondayChecked : '' ?>>月曜はじまり</label>
                </div>
            </div>

            <div class="mar-top-20">
                <strong>　進捗に合わせたグラフの背景色</strong><br>
                <?php
                $zoneRateRed = h($this->input->post('zone_rate_red'));
                $zoneRateGreen = h($this->input->post('zone_rate_green'));
                ?>
                <div class="text-center">
                    <input id="zone_rate_red" name="zone_rate_red" value="<?= $zoneRateRed ?>" type="hidden">
                    <input id="zone_rate_green" name="zone_rate_green" value="<?= $zoneRateGreen ?>" type="hidden">
                    <div style="width:32%;display:inline-block;text-align:center;">
                        〜<span id="txt_zone_rate_red"><?= $zoneRateRed ?></span>%
                    </div>
                    <div style="width:32%;display:inline-block;text-align:center;"></div>
                    <div style="width:32%;display:inline-block;text-align:center;">
                        <span id="txt_zone_rate_green"><?=$zoneRateGreen?></span>%～
                    </div>
                </div>
                <div class="text-center">
                    <div style="width:32%;height:20px;display:inline-block;background-color:<?= COLOR_RED ?>;"></div>
                    <div style="width:32%;height:20px;display:inline-block;background-color:<?= COLOR_YELLOW ?>;"></div>
                    <div style="width:32%;height:20px;display:inline-block;background-color:<?= COLOR_GREEN ?>;"></div>
                </div>
                <div style="width:32%;display:inline-block;text-align:center;">
                    <button class="btn btn-default btn-sm" type="button" onclick="$('#popupChangeZoneRateRed').modal('show');">変更</button>
                </div>
                <div style="width:32%;display:inline-block;text-align:center;"></div>
                <div style="width:32%;display:inline-block;text-align:center;">
                    <button class="btn btn-default btn-sm" type="button" onclick="$('#popupChangeZoneRateGreen').modal('show');">変更</button>
                </div>
            </div>

            <div class="mar-top-20">
                <label for="category">　カテゴリー</label>
                <div id="category-list">
                    <?php $postCategory = $this->input->post('category');?>
                    <?php foreach($postCategory as $categoryKey => $categoryItem): ?>
                        <?php $row = $categoryKey + 1; ?>
                        <?php $clsHide = '' ?>
                        <?php if(isset($categoryItem['delete'])): ?>
                            <div id="category-item-<?= $row ?>" class="row mar-top-10 dis-none" row="<?= $row ?>">
                                <?php if(isset($categoryItem['id'])): ?>
                                    <input name="category[<?= $row ?>][id]" value="<?= $categoryItem['id'] ?>" type="hidden">
                                <?php endif; ?>
                                <input name="category[<?= $row ?>][delete]" value="true" type="hidden">
                                <input name="category[<?= $row ?>][title]" value="<?= $categoryItem['title'] ?>" type="hidden">
                                <div class="col-xs-9"><?= $categoryItem['title'] ?></div>
                                <div class="col-xs-3">
                                    <button class="btn btn-default btn-sm" type="button" onclick="deleteCategory($(this), 0)">削除</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div id="category-item-<?= $row ?>" class="row mar-top-10" row="<?= $row ?>">
                                <?php if(isset($categoryItem['id'])): ?>
                                    <input name="category[<?= $row ?>][id]" value="<?= $categoryItem['id'] ?>" type="hidden">
                                <?php endif; ?>
                                <input name="category[<?= $row ?>][title]" value="<?= $categoryItem['title'] ?>" type="hidden">
                                <div class="col-xs-9"><?= $categoryItem['title'] ?></div>
                                <div class="col-xs-3">
                                    <button class="btn btn-default btn-sm" type="button" onclick="deleteCategory($(this), <?= $row ?>)">削除</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-default btn-sm" type="button" onclick="addCategory()">カテゴリー追加</button>
            </div>
        </div>

        <div class="footer">
            <div class="footer-btn btn-save">
                <a class="btn-circle-lg" onclick="$('#form-data').submit()">
                    <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                    保存
                </a>
            </div>
        </div>
    <?= form_close() ?>

    <div class="modal fade" id="popupChangeZoneRateRed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <label for="input_zone_rate_red">閾値</label>
                    <input id="input_zone_rate_red" name="input_zone_rate_red" class="form-control" type="number" value="<?=h($this->input->post('zone_rate_red'))?>" min="1" max="100"/>
                </div>
                <div class="modal-footer">
                    <div class="text-center">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">キャンセル</button>
                        <button id="btnZoneRateRed" type="button" class="btn btn-primary btn-sm" data-dismiss="modal">確認</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popupChangeZoneRateGreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <label for="input_zone_rate_green">閾値</label>
                    <input id="input_zone_rate_green" name="input_zone_rate_green" class="form-control" type="number" value="<?=h($this->input->post('zone_rate_green'))?>" min="1" max="100"/>
                </div>
                <div class="modal-footer">
                    <div class="text-center">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">キャンセル</button>
                        <button id="btnZoneRateGreen" type="button" class="btn btn-primary btn-sm" data-dismiss="modal">確認</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

