<div class="mar-top-20"></div>
<div class="frame">
    <?php if($projects) : ?>
        <?php foreach ($projects as $projectKey => $project) : ?>
            <div class="wig">
                <div class="header pad-top-10 pad-bottom-5">
                    <?=h($project['title'])?>
                </div>
                <div class="body text">
                    <?=nl2br(h($project['content']))?>
                </div>
                <div class="footer">
                    <div class="start-date">
                        カテゴリー :<?=h($project['category_title'])?><br>
                        作成日：<?= viewDate($project['created']) ?>
                        <?php if($project['closed']) : ?>
                            <br>
                            終了日：<?= viewDate($project['closed']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <br>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mar-top-10">
            <div class="alert alert-danger text-center">
                データがありません
            </div>
        </div>
    <?php endif; ?>
    <br>
    <br>
</div>
