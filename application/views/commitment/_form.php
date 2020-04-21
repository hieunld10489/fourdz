<div class="content-form">
    <div class="mar-lr-5 pad-top-10 pad-bottom-10">
        <?php if(isset($commitment_id)): ?>
            <?= form_open('commitment/update/'.intval($commitment_id)); ?>
        <?php else: ?>
            <?= form_open('commitment/create/'.intval($measure_id)) ?>
        <?php endif; ?>
            <p>
                <label for="name">　コミットメント名 <span class="required">*</span></label>
                <input class="form-control" type="text" maxlength="128" id="name" name="name" value="<?=h($this->input->post('name'))?>" />
            </p>

            <p>
                <label for="start_monday">　期間 <span class="required">*</span></label>
                <select class="form-control" id="start_monday" name="start_monday">
                    <?php foreach($ary_kikan as $aryKikanKey => $aryKikanItem): ?>
                        <option value="<?=h($aryKikanKey)?>" <?=($aryKikanKey==$this->input->post('start_monday')) ? 'selected' : '' ?>>
                            <?= $aryKikanItem ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <div class="text-center">
                <?php if(isset($commitment_id)): ?>
                    <input class="btn btn-primary" type="submit" name="submit" value="保存" />
                <?php else: ?>
                    <input class="btn btn-primary" type="submit" name="submit" value="作成" />
                <?php endif; ?>
            </div>
        <?= form_close() ?>

        <?php if(isset($commitment_id)): ?>
            <hr>
            <div class="text-center">
                <div class="pull-left" style="width: 50%">
                    <a class="btn btn-default" href="<?=site_url('commitment/create/').intval($measure_id) . '?name=' . urlencode($this->input->post('name'))?>">同じコミットメント名で新規作成</a>
                </div>
                <div class="pull-left" style="width: 50%">
                    <?= form_open('commitment/delete'); ?>
                        <input type="hidden" name="commitment_id" value="<?=h($commitment_id)?>" />
                        <button class="btn btn-danger" onclick="return closeConfirm('コミットメント', this.parentNode, '削除')">削除</button>
                    <?= form_close() ?>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
