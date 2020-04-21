<?php

/**
 * @property Commitment_model $Commitment_model
 * @property Measure_model $Measure_model
 * @property User_model $User_model
 * @property Category_model $Category_model
 * @property Page_model $Page_model
 * @property Project_model $Project_model
 * @property Wig_model $Wig_model
 * @property Setting_model $Setting_model
 * @property Wig_Pro_model $Wig_Pro_model
 * @property Measure_Pro_model $Measure_Pro_model
 */
class Gen_Controller extends CI_Controller {

    /**
     * ログイン中ユーザの情報
     * @var array
     */
    protected $user = null;

    public static $strController = null;
    public static $strAction = null;
    public static $strLayout = 'default';
    public static $strBackUrl = 'project/index';

    public function __construct()
    {
        parent::__construct();

        // ログイン中ユーザの情報を取得しておく
        $this->load->model('User_model');
        $this->load->model('Category_model');
        $this->load->model('Commitment_model');
        $this->load->model('Measure_model');
        $this->load->model('Page_model');
        $this->load->model('Project_model');
        $this->load->model('User_model');
        $this->load->model('Wig_model');
        $this->load->model('Setting_model');
        $this->load->model('Wig_Pro_model');
        $this->load->model('Measure_Pro_model');

        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('cookie');
        $this->load->helper('date');

        $this->load->library('form_validation');
        $this->load->library('session');

        self::$strController = $this->router->fetch_class();
        self::$strAction = $this->router->fetch_method();

        $this->user = $this->User_model->get_loggedin_user();
        if(isset($this->user['id'])) {
            $this->setting = $this->Setting_model->getById($this->user['id']);
            if(!$this->setting) {
                $this->setting = [
                    'zone_rate_red' => Setting_model::$ZONE_RATE_RED,
                    'zone_rate_green' => Setting_model::$ZONE_RATE_GREEN,
                ];
            }
        }

        if (!$this->user) {
            // もしログイン情報が取得できなかったら、匿名ログイン画面に飛ばす
            redirect('user/anonymous_login');
            exit;
        }
    }

    public function class_name()
    {
        return __CLASS__;
    }

    public function valid_error()
    {
        return false;
    }

    /**
     * フォームのvalidationで使われる日付フォーマットチェック.
     * "callback_valid_date"で使う。
     * @param  string  $str チェックする日付文字列
     * @return boolean      結果 true=正しいフォーマット false=不正
     */
    public function valid_date($str)
    {
        // validationで使うので、boolean型で返す！
        if ($this->isDate($str, 'Y-m-d')) {
            return true;
        } elseif($this->isDate($str, DATE_UI_FORMAT)) {
            return true;
        } elseif($this->isDate($str, 'd-m-Y')) {
            return true;
        }  elseif($this->isDate($str, 'd/m/Y')) {
            return true;
        }  else {
            return false;
        }
    }

    /**
     * フォームのvalidationで使われる日時フォーマットチェック.
     * "callback_valid_datetime"で使う。
     * @param  string  $str チェックする日時文字列
     * @return boolean      結果 true=正しいフォーマット false=不正
     */
    public function valid_datetime($str)
    {
        // validationで使うので、boolean型で返す！
        if ($this->isDate($str, DATE_UI_DB_FORMAT)) {
            return true;
        } elseif($this->isDate($str, 'Y/m/d H:i:s')) {
            return true;
        } elseif($this->isDate($str, 'd-m-Y H:i:s')) {
            return true;
        }  elseif($this->isDate($str, 'd/m/Y H:i:s')) {
            return true;
        }  else {
            return false;
        }
    }

    /**
     * フォームのvalidationで使われる日時フォーマットチェック.
     * "callback_valid_max_len"で使う。
     * @param  string  $str チェックする日時文字列
     * @param  string  $limit
     * @return boolean 結果 true=正しいフォーマット false=不正
     */
    public function valid_max_len($str, $limit = null)
    {
        if(!$limit) {
            $limit = FIELD_TITLE;
        }
        // validationで使うので、boolean型で返す！
        if(mb_strlen($str) > $limit) {
            return true;
        }
        return false;
    }

    public function valid_exist($str = '')
    {
        if(mb_strlen(trim($str)) > 0) {
            return true;
        }
        return false;
    }

    public function valid_numeric($str = 0)
    {
        if(mb_strlen(trim($str)) > 0 && is_numeric($str)) {
            return true;
        }
        return false;
    }

    public function renderView($aryData = [], $strName = null)
    {
        if(!$strName) {
            $strName = strtolower(self::$strController) . '/' . strtolower(self::$strAction);
        }

        if(!isset($aryData['back_url'])) {
            $aryData['back_url'] = site_url(self::$strBackUrl);
        }

        $this->template['content'] = $this->load->view($strName, $aryData, true);
        $this->template['message'] = $this->load->view('errors/message', [], true);

        $this->load->view('layout/' . self::$strLayout, $this->template);
    }

    public function isDate($strDate, $strFormat) {
        if(date($strFormat, strtotime($strDate)) == date($strDate)) {
            return true;
        } else {
            return false;
        }
    }

    public function validation_form($aryField = []) {
        $aryErrorList = [];
        if(!$_POST || !$aryField) return $aryErrorList;
        foreach($aryField as $aryFieldKey => $aryFieldItem) {
            $aryField = explode('|', $aryFieldItem['field']);
            $strMsg = $aryFieldItem['msg'];
            foreach($aryField as $aryFieldChildItem) {
                $strParam = $this->input->post($aryFieldChildItem);
                if($aryFieldKey == 'required') {
                    if(!$this->valid_exist($strParam) && !isset($aryErrorList[$aryFieldKey . '_error'])) {
                        $aryErrorList[$aryFieldKey . '_error'] = $strMsg;
                    }
                }

                if($aryFieldKey == 'number') {
                    if(!$this->valid_exist($strParam)) {
                        $aryErrorList['required_error'] = '必須項目を入力して下さい';
                    } elseif(!$this->valid_numeric($strParam) && !isset($aryErrorList[$aryFieldKey . '_error'])) {
                        $aryErrorList[$aryFieldKey . '_error'] = $strMsg;
                    }
                }

                if($aryFieldKey == 'date') {
                    if(!$this->valid_exist($strParam)) {
                        $aryErrorList['required_error'] = '必須項目を入力して下さい';
                    } elseif(!$this->valid_date($strParam) && !isset($aryErrorList[$aryFieldKey . '_error'])) {
                        $aryErrorList[$aryFieldKey . '_error'] = $strMsg;
                    }
                }
            }
        }

        return $aryErrorList;
    }

    public function errorFlash($error) {
        $this->session->set_flashdata('error', $error);
    }

    /**
     * フラッシュの成功メッセージ管理
     * @param array || string $errors
     */
    public function successFlash($success) {
        $this->session->set_flashdata('success', $success);
    }

    public function redirectSuccess($url, $msg) {
        $this->successFlash($msg);
        redirect($url);
        exit;
    }

    public function redirectError($url, $msg) {
        $this->errorFlash($msg);
        redirect($url);
        exit;
    }

    public function isPost() {
        return ($this->input->method() == 'post');
    }

    public function isGet() {
        return ($this->input->method() == 'get');
    }
}
