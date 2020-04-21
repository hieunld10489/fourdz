<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Setting extends Gen_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 設定画面.
     */
    public function index()
    {
        $data['header_text'] = '設定';
        $data['back_url'] = site_url('project');

        $aryRole['required'] = ['field' => 'name', 'msg' => 'ニックネームを入力して下さい'];
        $aryErrorList = $this->validation_form($aryRole);

        $arySetting = $this->Setting_model->get($this->user['id']);

        if($this->isPost() && !$aryErrorList) {
            if(!$arySetting) {
                $this->Setting_model->insert($this->user['id']);
            } else {
                $this->Setting_model->update($this->user['id']);
            }
            //設定画面.
            if($aryCategory = $this->input->post('category')) {
                foreach($aryCategory as $aryCategoryItem) {
                    if(!isset($aryCategoryItem['id'])) {
                        $this->Category_model->insertInSetting($this->user['id'], $aryCategoryItem['title']);
                    }

                    if(isset($aryCategoryItem['delete']) && isset($aryCategoryItem['id'])) {
                        $this->Category_model->deleted($this->user['id'], $aryCategoryItem['id']);
                    }
                }
            }
            $this->redirectSuccess('setting/index', '正常に保存されました');
        } else {
            if($this->isPost()) {
                $_POST['category'] = $this->input->post('category');
            } else {
                if($arySetting) {
                    $_POST['name'] = $arySetting['name'];
                    $_POST['week_start_monday'] = $arySetting['week_start_monday'];
                    $_POST['zone_rate_red'] = $arySetting['zone_rate_red'];
                    $_POST['zone_rate_green'] = $arySetting['zone_rate_green'];
                } else {
                    $_POST["zone_rate_red"] = Setting_model::$ZONE_RATE_RED;
                    $_POST["zone_rate_green"] = Setting_model::$ZONE_RATE_GREEN;
                }
                $_POST["category"] = $this->Category_model->get($this->user['id'], FALSE, TRUE);
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }

            $this->renderView($data);
        }
    }
}
