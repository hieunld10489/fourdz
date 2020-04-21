<?php if($this->input->post('project_id')): ?>
    <?php echo form_open('project/update/'.intval($project_id), ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
    <input type="hidden" name="project_id" value="<?=h($project_id)?>">
<?php else: ?>
    <?php echo form_open('project/create/', ['id' => 'form-data', 'class' => 'form-horizontal']) ?>
<?php endif; ?>
    <div class="content-form">
        <div class="mar-lr-5 pad-top-10 pad-bottom-10">
            <div class="mar-top-10">
                <label for="name">テーマ名 <span class="required">*</span></label>
                <input class="form-control" type="text" name="name" maxlength="64" value="<?= h($this->input->post('name')) ?>"/>
            </div>
            <div class="mar-top-10">
                <label for="category">カテゴリー <span class="required">*</span></label>
                <select class="form-control" name="category">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= h($category['id']) ?>" <?= ($category['id'] == $this->input->post('category')) ? 'selected' : '' ?>>
                            <?= h($category['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mar-top-10">
                <label for="content">メモ</label>
                <textarea class="form-control" name="content" maxlength="1000" rows="8"><?=h($this->input->post('content'))?></textarea>
            </div>
        </div>
        <div class="footer">
            <div class="footer-btn btn-save">
                <a class="btn-circle-lg" onclick="$('#form-data').submit()">
                    <img src="<?= site_url('assets/images/icon_new.png') ?>"><br>
                    <?php if($this->input->post('project_id')): ?>
                        保存
                    <?php else: ?>
                        作成
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>

<?= form_close() ?>