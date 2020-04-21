<h2>今週のコミットメント</h2>

<div class="panel panel-default commitment-period">

    <div class="panel-heading">
        <h3 class="panel-title">期間</h3>
    </div>
    <div class="panel-body">
        <?=date(DATE_UI_FORMAT, $week_start)?> 〜 <?=date(DATE_UI_FORMAT, $week_end)?>
    </div>
</div>

<div class="commitments">
<?php echo form_open('commitment/status/'.h($measure_id)) ?>

    <div class="row">

        <div class="col-xs-7 col-md-9">
            <strong>コミットメント</strong>
        </div>
        <div class="col-xs-5 col-md-3">
            <strong>結果</strong>
        </div>

    </div>

    <hr>
    <?php foreach ($commitments as $commitment) : ?>
    <div class="row commitment">

        <div class="col-xs-7 col-md-9">
            <div class="commitment-name">
                <a href="<?=site_url('commitment/update/'.h($commitment['id']))?>">
                    <?=h($commitment['title'])?>
                </a>
            </div>
        </div>
        <div class="col-xs-5 col-md-3">
            <select class="form-control" name="status_commitment_<?=h($commitment['id'])?>">
                <option value="0"  <?=($commitment['result'] ==  0) ? 'selected' : ''?>>-</option>
                <option value="10" <?=($commitment['result'] == 10) ? 'selected' : ''?>>○</option>
                <option value="20" <?=($commitment['result'] == 20) ? 'selected' : ''?>>△</option>
                <option value="30" <?=($commitment['result'] == 30) ? 'selected' : ''?>>×</option>
            </select>
        </div>

    </div>
    <hr>
    <?php endforeach; ?>

    <div class="row">

        <div class="col-xs-7 col-md-9"></div>
        <div class="col-xs-5 col-md-3">
            <p>
            <input type="submit" class="btn btn-default" value="結果を保存">
        </p>
        </div>

    </div>

</form>
</div>




<h2>来週のコミットメント</h2>

<div class="panel panel-default commitment-period">

    <div class="panel-heading">
        <h3 class="panel-title">期間</h3>
    </div>
    <div class="panel-body">
        <?=date(DATE_UI_FORMAT, $week_start + (60*60*24*7))?> 〜 <?=date(DATE_UI_FORMAT, $week_end + (60*60*24*7))?>
    </div>
</div>

<div class="commitments">

    <div class="row">

        <div class="col-xs-7 col-md-9">
            <strong>コミットメント</strong>
        </div>
        <div class="col-xs-5 col-md-3">
            <strong>結果</strong>
        </div>

    </div>

    <hr>
    <?php foreach ($commitments_next_week as $commitment) : ?>
    <div class="row commitment">

        <div class="col-xs-7 col-md-9">
            <div class="commitment-name">
                <a href="<?=site_url('commitment/update/'.h($commitment['id']))?>">
                    <?=h($commitment['title'])?>
                </a>
            </div>
        </div>
        <div class="col-xs-5 col-md-3">
            -
        </div>

    </div>
    <hr>
    <?php endforeach; ?>

</div>

<p>&nbsp;</p>

<p>
    <a href="<?=site_url('commitment/create/').h($measure_id)?>" class="btn btn-default">コミットメント新規作成</a>
</p>

<p>
    <a href="<?=site_url('commitment/more/').h($measure_id)?>" class="btn btn-default">過去のコミットメント</a>
</p>
