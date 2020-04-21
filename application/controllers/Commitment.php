<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Commitment_model $Commitment_model
 * @property Measure_model $Measure_model
 * @property User_model $User_model
 */
class Commitment extends Gen_Controller {

    /**
     * 今週のコミットメント一覧画面.
     * @param int $measure_id 親LM ID
     */
    public function index($measure_id)
    {
        // 正しいアクセスかどうか判定
        $this->check_access_valid($measure_id);

        $current_week_sunday = $this->Commitment_model->get_current_week_sunday(); // 今週頭の日曜日
        $next_week_sunday = $current_week_sunday + (60 * 60 * 24 * 7); // 来週頭の日曜日

        // LMの今週と来週のコミットメント一覧を取得
        $data['commitments'] = $this->Commitment_model->get($measure_id, $current_week_sunday);
        $data['commitments_next_week'] = $this->Commitment_model->get($measure_id, $next_week_sunday);
        // 今週の期間を取得
        $week_start_day = $current_week_sunday;
        $user_settings = $this->User_model->get_user_settings($this->user['id']);
        if ($user_settings['week_start_monday'] === 1) {
            // もしユーザの週始まり設定が月曜日からなら、開始日を1日進める
            $week_start_day += 60 * 60 * 24;
        }
        $week_end_day = $week_start_day + (60 * 60 * 24 * 6); // 6日後

        $data['week_start_monday'] = $user_settings['week_start_monday'];
        $data['week_start'] = $week_start_day;
        $data['week_end']   = $week_end_day;
        $data['header_text'] = '今週のコミットメント';
        $data['measure_id'] = $measure_id;
        $data['wig_id'] = $this->Measure_model->get_parent_wig_id($measure_id);
        $data['back_url'] = site_url('measure/index/' . intval($data['wig_id']));

        $this->renderView($data);
    }

    /**
     * もっと見る画面.
     * @param int $measure_id 親LM ID
     */
    public function more()
    {
        // LMのコミットメント一覧を取得
        $commitments = $this->Commitment_model->getPassDeletedByUser($this->user['id']);

        $data['commitments'] = $commitments;
        $data['header_text'] = '過去のコミットメント一覧';
        $data['back_url'] = site_url('project');

        $this->renderView($data);
    }

    /**
     * コミットメント作成画面.
     * @param int $measure_id 親LM ID
     */
    public function create($measure_id)
    {
        // 正しいアクセスかどうか判定
        $aryValid = $this->check_access_valid($measure_id);

        $data['week_start_monday'] = $this->setting['week_start_monday'];
        $data['header_text'] = 'コミットメント新規作成';
        $data['measure_id'] = $measure_id;
        $data['back_url'] = site_url('wig/index/' . $aryValid['project_id']);

        $aryRole['required'] = ['field' => 'name', 'msg' => '必須項目を入力して下さい'];
        $aryRole['date'] = ['field' => 'start_monday', 'msg' => '期間Format year error'];
        $aryErrorList = $this->validation_form($aryRole);

        if($this->isPost() && !$aryErrorList) {
            $this->Commitment_model->insert($measure_id);
            $this->redirectSuccess('wig/index/' . $aryValid['project_id'], '正常に保存されました');
            exit;
        } else {
            $data['ary_kikan'] = $this->Commitment_model->createDate($data['week_start_monday']);

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }

            $this->renderView($data, 'commitment/_form');
        }
    }

    /**
     * コミットメント編集画面.
     * @param int $commitment_id コミットメントID
     */
    public function update($commitment_id)
    {
        // 正当なユーザによるリクエストかチェック
        $aryValid = $this->check_update_valid($commitment_id);

        $data['week_start_monday'] = $this->setting['week_start_monday'];

        $measure_id = $this->Commitment_model->get_parent_measure_id($commitment_id);
        $data['measure_id'] = $measure_id;
        $data['header_text'] = 'コミットメント編集';
        $data['commitment_id'] = $commitment_id;
        $data['back_url'] = site_url('wig/index/' . $aryValid['project_id']);

        $aryRole['required'] = ['field' => 'name', 'msg' => '必須項目を入力して下さい'];
        $aryRole['date'] = ['field' => 'start_monday', 'msg' => '期間Format year error'];
        $aryErrorList = $this->validation_form($aryRole);

        if($this->isPost() && !$aryErrorList) {
            $this->Commitment_model->update($commitment_id);
            $this->redirectSuccess('wig/index/' . $aryValid['project_id'], '正常に編集されました');
        } else {
            // 現在のデータを初期値として設定する
            $current_data = $this->Commitment_model->get_by_id($commitment_id);
            $current_week_sunday = strtotime($current_data['start_monday']); // データの頭の日曜日
            // 期間の候補を用意
            $mondayThisWeek = date(DATE_UI_FORMAT,strtotime('monday this week')); // 今週頭の日曜日
            $mondayNextWeek = date(DATE_UI_FORMAT,strtotime('monday next week')); // 来週頭の日曜日

            if($data['week_start_monday'] == ON) {
                $aryKikan[$mondayThisWeek] = date(DATE_UI_FORMAT,strtotime('monday this week')) . '〜' . date(DATE_UI_FORMAT,strtotime('sunday this week'));
                $aryKikan[$mondayNextWeek] = date(DATE_UI_FORMAT,strtotime('monday next week')) . '〜' . date(DATE_UI_FORMAT,strtotime('sunday next week'));
            } else {
                $aryKikan[$mondayThisWeek] = date(DATE_UI_FORMAT,strtotime('sunday previous week')) . '〜' . date(DATE_UI_FORMAT,strtotime('saturday this week'));
                $aryKikan[$mondayNextWeek] = date(DATE_UI_FORMAT,strtotime('sunday this week')) . '〜' . date(DATE_UI_FORMAT,strtotime('saturday next week'));
            }
            $data['ary_kikan'] = $this->Commitment_model->createDate($data['week_start_monday']);

            // フォームのデータが無い＝初回表示の場合
            if (!$this->input->post('start_monday')) {
                // 現在のデータを初期値として設定する
                $_POST['name'] = $current_data['title'];
                $_POST['start_monday'] = $current_data['start_monday'];
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }

            $this->renderView($data, 'commitment/_form');
        }
    }

    /**
     * コミットメント削除処理.
     */
    public function delete()
    {
        $commitment_id = $this->input->post('commitment_id');
        // 正当なユーザによるリクエストかチェック
        $validData = $this->check_update_valid($commitment_id);

        if($this->isPost()) {
            // コミットメントを削除してTOP画面に遷移
            $this->Commitment_model->delete($commitment_id);
            $this->redirectSuccess('wig/index/'.intval($validData['project_id']), '正常に削除されました');
        }
    }

    /**
     * コミットメントの結果を選択する
     */
    public function change_status()
    {
        $commitment_id = $this->input->post('commitment_id');
        $result = $this->input->post('result');
        if($commitment_id && in_array($result, [ON, OFF])) {
            // 正当なユーザによるリクエストかチェック
            $this->check_update_valid($commitment_id);

            $this->Commitment_model->status($commitment_id, $result);
            echo $this->security->get_csrf_hash();
            exit;
        }
    }

    /**
     * 正常なアクセスかチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $measure_id 親LM ID
     * @return bool
     */
    private function check_access_valid($measure_id) {
        if(!$this->valid_exist($measure_id)) {
            $this->errorFlash('コミットメントは存在しません');
            redirect('user/anonymous_login');
        }
        // ログイン中ユーザがアクセス可能なスコアボードかどうか判定
        $aryMeasure = $this->Measure_model->is_valid_relation($this->user['id'], $measure_id);

        if(!$aryMeasure) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->errorFlash('コミットメントは存在しません');
            redirect('user/anonymous_login');
            exit;
        }
        return $aryMeasure;
    }

    /**
     * 正常な更新かチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $commitment_id コミットメントID
     * @return array|bool
     */
    private function check_update_valid($commitment_id) {
        if(!$this->valid_exist($commitment_id)) {
            $this->errorFlash('コミットメントは存在しません');
            redirect('user/anonymous_login');
        }
        // ログイン中ユーザがアクセス可能なスコアボードかどうか判定
        $is_valid = $this->Commitment_model->is_valid_relation($this->user['id'], $commitment_id);
        if (!$is_valid) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->errorFlash('コミットメントは存在しません');
            redirect('user/anonymous_login');
            exit;
        }
        return $is_valid;
    }
}
