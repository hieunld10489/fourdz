<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="<?= site_url() ?>assets/images/favicon.ico">
        <title><?= WEB_TITLE ?></title>
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/bootstrap-slider.min.css" type="text/css">
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/font-awesome.min.css" type="text/css">
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/common.css?v=201706051026" type="text/css">
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/working.css?v=201706051026" type="text/css">
        <link rel="stylesheet" href="<?= site_url() ?>assets/css/sidebar.css?v=201706051026" type="text/css">

        <script type="text/javascript" src="<?= site_url() ?>assets/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?= site_url() ?>assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= site_url() ?>assets/js/bootbox.min.js"></script>
        <script type="text/javascript" src="<?= site_url() ?>assets/js/bootstrap-slider.min.js"></script>
        <script type="text/javascript" src="<?= site_url() ?>assets/js/common.js?v=201706051026"></script>
        <script type="text/javascript" src="<?= site_url() ?>assets/js/form.js?v=201706051026"></script>
    </head>
    <body>
        <?php if (!isset($no_header) || $no_header !== true) : ?>
            <div id="top"></div>
            <div id="common-header">
                <?php if(isset($back_url) && $back_url): ?>
                    <div class="pull-left text-center" style="width: 11%;">
                        <a href="<?= $back_url ?>" class="btn-back">
                           <img src="<?= site_url('assets/images/back.png') ?>" width="28">
                        </a>
                    </div>
                    <div class="btn-title pull-left text-center" style="width: 78%">
                        <?= $header_text ?>
                    </div>
                    <div class="pull-left text-center" style="width: 11%;">
                        <a class="btn-menu">
                            <img src="<?= site_url('assets/images/menu.png') ?>" width="28">
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pull-left text-center" style="width: 13%;">
                        <img src="<?= site_url('assets/images/logo.png') ?>" style="max-width: 44px;">
                    </div>
                    <div class="btn-title pull-left text-center" style="width: 74%">
                        <?= $header_text ?>
                    </div>
                    <div class="pull-left text-center" style="width: 13%">
                        <a class="btn-menu">
                            <img src="<?= site_url('assets/images/menu.png') ?>" width="28">
                        </a>
                    </div
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div id="common-header-dummy"></div>
        <?php endif; ?>

        <div id="wrapper">
            <div id="page-content-wrapper">
                <?php if($message):?>
                    <div class="mar-top-10">
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                <?php if ($content) echo $content; ?>
            </div>
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li><a href="<?=site_url('project/index')?>">TOP</a></li>
                    <li><a href="<?=site_url('measure/more')?>">過去の先行指標</a></li>
                    <li><a href="<?=site_url('commitment/more')?>">過去のコミットメント</a><div class="line"></div></li>
                    <li><a href="<?=site_url('page/index/about')?>">4Dxとは</a></li>
                    <li><a href="<?=site_url('page/index/howto')?>">利用方法</a></li>
                    <li><a href="<?=site_url('page/index/term')?>">利用規約</a></li>
                    <li><a href="<?=site_url('page/index/company')?>">運用会社</a></li>
                    <li><a href="<?=site_url('page/index/copyright')?>">著作権について</a></li>
                    <li><a href="<?=site_url('page/index/qa')?>">よくある質問</a></li>
                    <li><a href="<?=site_url('setting/index')?>">設定</a></li>
                </ul>
            </div>
        </div>
        <div id="process-sidebar-wrapper"></div>
    </body>
</html>
