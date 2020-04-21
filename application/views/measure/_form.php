<script type="text/javascript" src="<?= site_url() ?>assets/js/measure/form.js?ver=20170526_1"></script>

<div class="content-form">
    <div class="mar-lr-5 pad-top-10 pad-bottom-10">
        <?php if($this->input->post('measure_id')): ?>
            <?php echo form_open('measure/update/'.intval($this->input->post('measure_id')), ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
        <?php else: ?>
            <?php echo form_open('measure/create/'.intval($wig_id), ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
        <?php endif; ?>

        <?php if($this->input->post('measure_id')): ?>
            <input class="form-control" type="hidden" id="measure_id" name="measure_id" value="<?=h($this->input->post('measure_id'))?>"/>
        <?php endif; ?>

        <div class="mar-top-10">
            <input class="form-control" type="text" maxlength="64" id="what" name="what" value="<?=h($this->input->post('what'))?>" />
        </div>

        <div class="mar-top-10">
            <div class="mar-top-10">
                <div class="pull-left text-center" style="width: 43%"><label for="from_date">　いつから<span class="required">*</span></label></div>
                <div class="pull-left" style="line-height: 30px; width: 14%">　</div>
                <div class="pull-right text-center" style="width: 43%"><label for="to_date">　いつまでに<span class="required">*</span></label></div>
                <div class="clearfix"></div>

                <div class="pull-left text-center" style="width: 43%">
                    <input class="form-control text-center" style="min-width: 91%" type="date" id="from_date" name="from_date" value="<?=h($this->input->post('from_date'))?>" />
                </div>
                <div class="pull-left text-center" style="line-height: 30px; width: 14%">　～　</div>
                <div class="pull-left text-center" style="width: 43%">
                    <input class="form-control text-center" style="min-width: 89%;" type="date" id="to_date" name="to_date" value="<?=h($this->input->post('to_date'))?>" />
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="mar-top-10" style="display:none;">
            <div class="checkbox">
                <label>
                    <?php if($this->input->post('type') == ON) : ?>
                        <input id="type" name="type" type="checkbox" checked>
                    <?php else: ?>
                        <input id="type" name="type" type="checkbox" value="0">
                    <?php endif; ?>
                    <b>プロジェクト型</b>
                </label>
            </div>
        </div>

        <div id="type-0" class="<?= ($this->input->post('type') == ON) ? 'dis-none' : '' ?> mar-top-10">
            <div>
                <div class="pull-left" style="width: 65%"><label for="from_date">　X<span class="required">*</span></label></div>
                <div class="pull-left text-center" style="width: 2%">&nbsp</div>
                <div class="pull-left text-center" style="width: 22%"><label for="to_date">単位</label><span class="required">*</span></div>
                <div class="pull-right text-center"></div>
                <div class="clearfix"></div>

                <div class="pull-left text-center" style="width: 65%">
                    <input class="form-control" type="text" id="from_value" name="from_value" value="<?=h($this->input->post('from_value'))?>" />
                </div>
                <div class="pull-left text-center" style="width: 2%">&nbsp</div>
                <div class="pull-left text-center" style="width: 22%">
                    <input class="form-control" type="text" id="unit" name="unit" value="<?=h($this->input->post('unit'))?>" maxlength="10"/>
                </div>
                <div class="pull-left" style="line-height: 34px;width: 10%">&nbspから</div>
                <div class="clearfix"></div>
            </div>
            <div class="mar-top-10">
                <div class="pull-left" style="width: 65%"><label for="to_value">　Y<span class="required">*</span></label></div>
                <div class="pull-left text-center" style="width: 2%">&nbsp</div>
                <div class="pull-left text-center" style="width: 22%"><label for="re_unit">単位</label></div>
                <div class="pull-right text-center"></div>
                <div class="clearfix"></div>

                <div class="pull-left text-center" style="width: 65%">
                    <input class="form-control" type="text" id="to_value" name="to_value" value="<?=h($this->input->post('to_value'))?>" />
                </div>
                <div class="pull-left text-center" style="width: 2%">&nbsp</div>
                <div class="pull-left text-center" style="width: 22%">
                    <input class="form-control" type="text" id="re_unit" value="<?=h($this->input->post('unit'))?>" maxlength="10" disabled/>
                </div>
                <div class="pull-left" style="line-height: 34px;width: 10%">&nbspまで</div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div id="type-1" class="<?= ($this->input->post('type') == OFF) ? 'dis-none' : '' ?> mar-top-10">
            <table id="wig-pro" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">項目名<span class="required">*</span></th>
                        <th class="text-center">達成度(%)<span class="required">*</span></th>
                        <th class="text-center">目標日<span class="required">*</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($this->input->post('measure_pro')) : ?>
                        <?php foreach ($this->input->post('measure_pro') as $measureProKey => $measureProItem) : ?>
                            <?php $row = $measureProKey + 1 ?>

                            <?php if(isset($measureProItem['delete'])): ?>
                                <tr class="wig-pro-row dis-none" row="<?= $row ?>">
                                    <th class="wig-pro-delete text-center" scope="row" onclick="deleteWigProRow($(this), <?= (isset($measureProItem['id'])) ? $row : 0 ?>)">
                                        <img src="<?= site_url('assets/images/icon_delete.png') ?>"><br>
                                        <?php if(isset($measureProItem['id'])): ?>
                                            <input type="hidden" name="measure_pro[<?= $row ?>][id]" value="<?= $measureProItem['id'] ?>"/>
                                        <?php endif; ?>
                                        <input name="measure_pro[<?= $row ?>][delete]" value="true" type="hidden">
                                    </th>
                                    <td class="wig-pro-title">
                                        <input name="measure_pro[<?= $row ?>][title]" type="text" value="<?= $measureProItem['title'] ?>" maxlength="64" class="form-control">
                                    </td>
                                    <td class="wig-pro-tasedo">
                                        <input name="measure_pro[<?= $row ?>][tasedo]" type="text" value="<?= $measureProItem['tasedo'] ?>" maxlength="3" class="form-control">
                                    </td>
                                    <td class="wig-pro-mokuhyoubi">
                                        <input name="measure_pro[<?= $row ?>][mokuhyoubi]" type="date" value="<?= $measureProItem['mokuhyoubi'] ?>" class="form-control wig-date">
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr class="wig-pro-row" row="<?= $row ?>">
                                    <th class="wig-pro-delete text-center" scope="row" onclick="deleteWigProRow($(this), <?= (isset($measureProItem['id'])) ? $row : 0 ?>)">
                                        <img src="<?= site_url('assets/images/icon_delete.png') ?>"><br>
                                        <?php if(isset($measureProItem['id'])): ?>
                                            <input type="hidden" name="measure_pro[<?= $row ?>][id]" value="<?= $measureProItem['id'] ?>"/>
                                        <?php endif; ?>
                                    </th>
                                    <td class="wig-pro-title">
                                        <input name="measure_pro[<?= $row ?>][title]" type="text" value="<?= $measureProItem['title'] ?>" maxlength="64" class="form-control">
                                    </td>
                                    <td class="wig-pro-tasedo">
                                        <input name="measure_pro[<?= $row ?>][tasedo]" type="text" value="<?= $measureProItem['tasedo'] ?>" maxlength="3" class="form-control">
                                    </td>
                                    <td class="wig-pro-mokuhyoubi">
                                        <input name="measure_pro[<?= $row ?>][mokuhyoubi]" type="date" value="<?= $measureProItem['mokuhyoubi'] ?>" class="form-control wig-date">
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="wig-pro-row" row="1">
                            <th class="wig-pro-delete text-center" scope="row" onclick="deleteWigProRow($(this), '0')"><img src="<?= site_url('assets/images/icon_delete.png') ?>"><br></th>
                            <td class="wig-pro-title"><input name="measure_pro[1][title]" type="text" maxlength="64" class="form-control"></td>
                            <td class="wig-pro-tasedo"><input name="measure_pro[1][tasedo]" type="text" maxlength="3" class="form-control"></td>
                            <td class="wig-pro-mokuhyoubi"><input name="measure_pro[1][mokuhyoubi]" type="date" class="form-control wig-date"></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mar-top-10"></div>
            <div class="text-center">
                <a id="koumoku-tsuika" class="btn-circle-sm">
                    <img src="<?= site_url('assets/images/icon_add.png') ?>"><br>
                    項目追加
                </a>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-btn btn-save">
            <a class="btn-circle-lg" onclick="$('#form-data').submit()">
                <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                <?php if($this->input->post('measure_id')): ?>
                    保存
                <?php else: ?>
                    作成
                <?php endif; ?>
            </a>
        </div>
    </div>
    <?= form_close() ?>
</div>
<input id="img-url" type="hidden" src="<?= site_url('assets/images/icon_delete.png') ?>">
