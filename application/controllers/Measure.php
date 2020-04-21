<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Measure extends Gen_Controller {

    public function create($wig_id)
    {
        // 正しいアクセスかどうか判定
        $aryValid = $this->check_access_valid($wig_id);

        $data['header_text'] = '先行指標新規作成';
        $data['wig_id'] = $aryValid['wig_id'];
        $data['project_id'] = $aryValid['project_id'];
        $data['project_id'] = $aryValid['project_id'];
        $data['back_url'] = site_url('wig/index/' . intval($data['project_id']));
        // 正しいアクセスかどうか判定
        $aryErrorList = [];
        if($this->isPost()) {
            $_POST['type'] = isset($_POST['type']) ? ON : OFF;
            $aryRole = ['date' => ['field' => 'from_date|to_date', 'msg' => 'Format year error']];
            if($_POST['type'] == ON) {
                if(isset($_POST['measure_pro']) && ($aryMeasurePros = $_POST['measure_pro'])) {
                    foreach($aryMeasurePros as $aryMeasureProsKey => $aryMeasureProsItem) {
                        // valid null
                        if(!$this->valid_exist($aryMeasureProsItem['title'])
                            || !$this->valid_exist($aryMeasureProsItem['tasedo'])
                            || !$this->valid_exist($aryMeasureProsItem['mokuhyoubi'])) {
                            $aryErrorList['required_error'] = '必須項目を入力して下さい';
                        } else {
                            // valid number
                            if(!$this->valid_numeric($aryMeasureProsItem['tasedo'])) {
                                $aryErrorList['number_item_error'] = '達成度(%)の入力は半角数字のみとなります。';
                            } else {
                                // valid number 0 ~ 100
                                if($aryMeasureProsItem['tasedo'] < 0 || $aryMeasureProsItem['tasedo'] > 100) {
                                    $aryErrorList['number_limit_item_error'] = '達成度(%)は0－100英数字内で入力してください';
                                }
                            }
                        }
                        // valid date
                        if(!$this->valid_date($aryMeasureProsItem['mokuhyoubi'])) {
                            $aryErrorList['date_item_error'] = '目標日format year error';
                        }
                    }
                } else {
                    $aryErrorList['required_error'] = '必須項目を入力して下さい';
                }
            } else {
                $aryRole['required'] = ['field' => 'what|unit', 'msg' => '必須項目を入力して下さい'];
                $aryRole['number'] = ['field' => 'from_value|to_value', 'msg' => 'XとYの入力は半角数字のみとなります。'];

                $paramFromValue = $this->input->post('from_value');
                $paramToValue = $this->input->post('to_value');
                if(($paramFromValue && $paramToValue) && $paramToValue == $paramFromValue ) {
                    $aryErrorList['number_compare_error'] = 'XとYが同じ値です。';
                }

            }
            $aryErrorListRole = $this->validation_form($aryRole);
            if($aryErrorListRole) {
                $aryErrorList = array_merge($aryErrorListRole, $aryErrorList);
            }

            if(!$aryErrorList) {
                $paramFromDate = $this->input->post('from_date');
                $paramToDate = $this->input->post('to_date');

                if($paramToDate < $paramFromDate) {
                    $aryErrorList['date_compare_error'] = '「いつから」は「いつまで」より過去の日付を指定してください';
                }
            }
        } else {
            $_POST['from_date'] = date('Y-m-d');
            $_POST['to_date'] = date('Y-m-d', strtotime('+7 day'));
        }

        if($this->isPost() && !$aryErrorList) {
            $newWigId = $this->Measure_model->insert_id($wig_id);
            if($_POST['type'] == ON && isset($aryMeasurePros)) {
                foreach($aryMeasurePros as $aryMeasureProsItem) {
                    $aryMeasureProsData[] = [
                        'measure_id' => $newWigId,
                        'title' => $aryMeasureProsItem['title'],
                        'mokuhyoubi' => $aryMeasureProsItem['mokuhyoubi'],
                        'tasedo' => $aryMeasureProsItem['tasedo'],
                    ];
                }

                if(isset($aryMeasureProsData)) {
                    $this->Measure_Pro_model->insert_batch($aryMeasureProsData);
                }
            }
            $this->redirectSuccess($data['back_url'], '正常に保存されました');
        } else {
            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data, 'measure/_form');
        }
    }

    public function update($measure_id) {
        // 正しいアクセスかどうか判定
        $aryValid = $this->measure_valid($measure_id);

        $data['header_text'] = '先行指標編集';
        $data['wig_id'] = $aryValid['wig_id'];
        $data['measure_id'] = $aryValid['measure_id'];
        $data['back_url'] = site_url('wig/index/' . intval($aryValid['project_id']));

        $aryErrorList = $aryMeasureProsDataInsert = $aryMeasureProsDataUpdate = $aryMeasureProsDataDelete = [];

        if($this->isPost()) {
            $_POST['type'] = isset($_POST['type']) ? ON : OFF;
            $aryRole = ['date' => ['field' => 'from_date|to_date', 'msg' => 'Format year error']];
            if($_POST['type'] == ON) {
                $aryRole['required'] = ['field' => 'what', 'msg' => '必須項目を入力して下さい'];
                if(isset($_POST['measure_pro']) && ($aryMeasurePros = $_POST['measure_pro'])) {
                    foreach($aryMeasurePros as $aryMeasureProsKey => $aryMeasureProsItem) {
                        // add delete wig pro value
                        if(isset($aryMeasureProsItem['delete']) && isset($aryMeasureProsItem['id'])) {
                            $aryMeasureProsDataDelete[] = $aryMeasureProsItem['id'];
                        }
                        // not delete
                        else {
                            if(!$this->valid_exist($aryMeasureProsItem['title'])
                                || !$this->valid_exist($aryMeasureProsItem['tasedo'])
                                || !$this->valid_exist($aryMeasureProsItem['mokuhyoubi'])) {
                                $aryErrorList['required_error'] = '必須項目を入力して下さい';
                            } else {
                                // valid number
                                if(!$this->valid_numeric($aryMeasureProsItem['tasedo'])) {
                                    $aryErrorList['number_item_error'] = '達成度(%)の入力は半角数字のみとなります。';
                                } else {
                                    // valid number 0 ~ 100
                                    if($aryMeasureProsItem['tasedo'] < 0 || $aryMeasureProsItem['tasedo'] > 100) {
                                        $aryErrorList['number_limit_item_error'] = '達成度(%)は0－100英数字内で入力してください';
                                    }
                                }
                                // valid date
                                if(!$this->valid_date($aryMeasureProsItem['mokuhyoubi'])) {
                                    $aryErrorList['date_item_error'] = '目標日Format year error';
                                }
                                if(!$aryErrorList) {
                                    //update
                                    if(!isset($aryMeasureProsItem['id'])) {
                                        $aryMeasureProsDataInsert[] = [
                                            'measure_id' => $this->input->post('measure_id'),
                                            'title' => $aryMeasureProsItem['title'],
                                            'mokuhyoubi' => $aryMeasureProsItem['mokuhyoubi'],
                                            'tasedo' => $aryMeasureProsItem['tasedo'],
                                        ];
                                    } else {
                                        $aryMeasureProsDataUpdate[] = [
                                            'id' => $aryMeasureProsItem['id'],
                                            'title' => $aryMeasureProsItem['title'],
                                            'mokuhyoubi' => $aryMeasureProsItem['mokuhyoubi'],
                                            'tasedo' => $aryMeasureProsItem['tasedo'],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $aryErrorList['required_error'] = '必須項目を入力して下さい';
                }
            } else {
                $aryRole['required'] = ['field' => 'what|unit', 'msg' => '必須項目を入力して下さい'];
                $aryRole['number'] = ['field' => 'from_value|to_value', 'msg' => 'XとYの入力は半角数字のみとなります。'];

                $paramFromValue = $this->input->post('from_value');
                $paramToValue = $this->input->post('to_value');
                if(($paramFromValue && $paramToValue) && $paramToValue == $paramFromValue ) {
                    $aryErrorList['number_compare_error'] = 'XとYが同じ値です。';
                }

                //delete if changing [type 1 -> 0]
                if(isset($_POST['measure_pro']) && ($aryMeasurePros = $_POST['measure_pro'])) {
                    foreach($aryMeasurePros as $aryMeasureProsKey => $aryMeasureProsItem) {
                        if(isset($aryMeasureProsItem['id'])) {
                            $aryMeasureProsDataDelete[] = $aryMeasureProsItem['id'];
                        }
                    }
                }
            }
            $aryErrorListRole = $this->validation_form($aryRole);
            if($aryErrorListRole) {
                $aryErrorList = array_merge($aryErrorListRole, $aryErrorList);
            }

            if(!$aryErrorList) {
                $paramFromDate = $this->input->post('from_date');
                $paramToDate = $this->input->post('to_date');

                if($paramToDate < $paramFromDate) {
                    $aryErrorList['date_compare_error'] = '「いつから」は「いつまで」より過去の日付を指定してください';
                }
            }
        }

        if($this->isPost() && !$aryErrorList) {
            // 正当なユーザによるリクエストかチェック
            $measure_id = $this->input->post('measure_id');
            $this->Measure_model->update($measure_id);
            //insert
            if($aryMeasureProsDataInsert) {
                $this->Measure_Pro_model->insert_batch($aryMeasureProsDataInsert);
            }
            //update
            if($aryMeasureProsDataUpdate) {
                $this->Measure_Pro_model->update_batch($aryMeasureProsDataUpdate, $measure_id);
            }
            //delete
            if($aryMeasureProsDataDelete) {
                $this->Measure_Pro_model->delete_checked($aryMeasureProsDataDelete, $measure_id);
            }
            $this->redirectSuccess($data['back_url'], '正常に保存されました');
        } else {
            if (!$this->input->post('measure_id')) {
                // 現在のデータを初期値として設定する
                $aryMeasure = $this->Measure_model->get($data['wig_id'], true, $measure_id);
                if($aryMeasure) {
                    $aryMeasurePro = $this->Measure_Pro_model->getAllBy(['measure_id' => $measure_id]);

                    $_POST['measure_id'] = $aryMeasure['id'];
                    $_POST['what'] = $aryMeasure['what'];
                    $_POST['type'] = $aryMeasure['type'];
                    $_POST['unit'] = $aryMeasure['unit'];
                    $_POST['from_date'] = $aryMeasure['from_date'];
                    $_POST['to_date'] = $aryMeasure['to_date'];
                    $_POST['from_value'] = $aryMeasure['from_value'];
                    $_POST['to_value'] = $aryMeasure['to_value'];
                    $_POST['measure_pro'] = $aryMeasurePro;
                }
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data, 'measure/_form');
        }
    }

    /**
     * もっと見る画面.
     * @param int $wig_id 親WIG ID
     */
    public function more()
    {
        // WIGのLM一覧を取得
        $data['measures'] = $this->Measure_model->getPassClosedByUser($this->user['id']);
        $data['header_text'] = '過去の先行指標一覧';
        $data['back_url'] = site_url('project');
        $this->renderView($data);
    }

    /**
     * LM作業入力画面.
     * @param int $measure_id LM ID
     */
    public function working($measure_id)
    {
        // 正しいアクセスかどうか判定
        // スコアボードの有効なWIG一覧を取得
        $aryValid = $this->measure_valid($measure_id);
        $project_id = $aryValid['project_id'];
        $measure_id = $aryValid['measure_id'];

        $aryMeasure = $this->Measure_model->getMeasuresWorking($measure_id);

        $data['measure'] = $aryMeasure ? $aryMeasure : [];
        $data['header_text'] = '先行指標実績';
        $data['back_url'] = site_url('wig/index/' . intval($project_id));

        $aryErrorList = $aryMeasureProData = [];
        if($this->isPost()) {
            if($aryMeasure['type'] == ON) {
                $aryMeasurePro = $this->input->post('measure_pro');
                $nowValueBK = 0;
                $nowValue = 0;
                $nowValueFlag = false;
                foreach($aryMeasurePro as $aryMeasureProItem) {
                    if($aryMeasureProItem['tasebi'] && !$this->valid_date($aryMeasureProItem['tasebi'])) {
                        $aryErrorList['date_error'] = '達成日format year error';
                    } else {
                        $dtTasebi = $aryMeasureProItem['tasebi'] ? $aryMeasureProItem['tasebi'] : null;

                        $aryMeasureProData[] = [
                            'id' => $aryMeasureProItem['id'],
                            'tasebi' => $dtTasebi
                        ];
                    }
                    if(!$nowValueFlag && $aryMeasureProItem['tasebi']) {
                        $nowValue = $aryMeasureProItem['tasedo'];
                    }

                    if(!$aryMeasureProItem['tasebi']) {
                        $nowValueBK = $aryMeasureProItem['tasedo'];
                    }

                    if($nowValueBK && $aryMeasureProItem['tasebi']) {
                        $nowValue = $nowValueBK;
                        $nowValueFlag = true;
                    }
                }
            } else {
                $aryRole['number'] = ['field' => 'now_value', 'msg' => '実績は数字のみ入力可能です'];
                $aryErrorList = $this->validation_form($aryRole);
            }
        }

        if($this->isPost() && !$aryErrorList) {
            if($this->input->post('now_value')) {
                $nowValue = $this->input->post('now_value');
            }
            $this->Measure_model->update_by_fields(['now_value' => $nowValue], $measure_id);
            if($aryMeasureProData) {
                $this->Measure_Pro_model->update_batch($aryMeasureProData, $measure_id);
            }
            $this->redirectSuccess('measure/working/' . intval($measure_id), '正常に保存されました');
        } else {
            if($aryMeasure) {
                $aryMeasurePro = $this->Measure_Pro_model->getAllBy(['measure_id' => $measure_id]);
                //update_by_fields
                if (!$this->input->post('measure_id')) {
                    $_POST['measure_id'] = $aryMeasure['id'];
                    $_POST['now_value'] = $aryMeasure['now_value'];
                    $_POST['measure_pro'] = $aryMeasurePro;
                }
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data);
        }
    }

    /**
     * 先行指標終了処理.
     */
    public function close()
    {
        $measure_id = $this->input->post('id');
        $clone_flg = $this->input->post('clone');
        if($measure_id) {
            $this->measure_valid($measure_id);
            $now = date(DATE_UI_DB_FORMAT);
            // close Measure
            $this->Measure_model->update_by_fields(['closed' => $now, 'updated' => $now], $measure_id);
            //close Commitment
            $aryCloseCommitment[0]['measure_id'] = $measure_id;
            $aryCloseCommitment[0]['delete_flag'] = ON;
            $aryCloseCommitment[0]['updated'] = $now;
            $this->Commitment_model->update_batch($aryCloseCommitment, 'measure_id');

            if ($clone_flg == 'y') {
                // 先行指標とコミットメントの複製
                $this->Measure_model->clone($measure_id);
            }

            $this->successFlash('正常に終了処理されました');
        } else {
            $this->errorFlash('先行指標は存在しません');
        }
    }

    /**
     * 正常なアクセスかチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $wig_id 親WIG ID
     * @param boolean $is_closed
     * @return bool
     */
    private function check_access_valid($wig_id, $is_closed = false) {
        // ログイン中ユーザがアクセス可能なスコアボードかどうか判定
        if(!$this->valid_exist($wig_id)) {
            $this->redirectError('project', '最重要目標は存在しません');
        }
        $aryWig = $this->Wig_model->is_valid_relation_data($this->user['id'], $wig_id, $is_closed);

        if(!$aryWig) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->redirectError('project', '最重要目標は存在しません');
        }
        return $aryWig;
    }

    /**
     * 正常なアクセスかチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $measure_id
     * @param boolean $is_closed
     * @return bool
     */
    private function measure_valid($measure_id, $is_closed = false) {
        // ログイン中ユーザがアクセス可能なスコアボードかどうか判定
        $aryMeasure = $this->Measure_model->getById($measure_id);
        if(!$aryMeasure) {
            $this->redirectError('project', '先行指標は存在しません');
        }

        // 正しいアクセスかどうか判定
        $aryValid = $this->check_access_valid($aryMeasure['wig_id'], $is_closed);
        if ($aryValid) {
            $aryValid['measure_id'] = $measure_id;
            return $aryValid;
        } else {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->redirectError('project', '先行指標は存在しません');
        }
    }
}
