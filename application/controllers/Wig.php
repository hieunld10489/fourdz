<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wig extends Gen_Controller {

     /**
     * 最重要目標一覧画面.
      * @param int $project_id 親スコアボードID
      */
    public function index($project_id)
    {
        // 正しいアクセスかどうか判定
        $aryProject = $this->check_project_id($project_id);

        $data['header_text'] = 'スコアボード⼀覧';

        // スコアボードの有効なWIG一覧を取得
        $data['wigs'] = $this->Wig_model->getAllRelation($project_id);
        $data['project_id'] = $project_id;
        $data['project_title'] = $aryProject['title'];

        $this->renderView($data);
    }

    /**
     * もっと見る画面.
     * @param int $project_id 親スコアボードID
     */
    public function more($project_id)
    {
        // 正しいアクセスかどうか判定
        $aryProject = $this->check_project_id($project_id);

        $data['header_text'] = '過去の最重要目標一覧';

        // スコアボードのWIG一覧を取得
        $data['wigs'] = $this->Wig_model->getAllRelation($project_id, true);
        $data['project_id'] = $project_id;
        $data['project_title'] = $aryProject['title'];
        $data['back_url'] = site_url('wig/index/' . intval($project_id));

        $this->renderView($data);
    }

     /**
     * WIG作業入力画面.
      * @param int $wig_id WIG ID
      */
    public function working($wig_id)
    {
        // 正しいアクセスかどうか判定
        $aryWig = $this->check_wig_id($wig_id);
        $project_id = $aryWig['project_id'];

        // スコアボードの有効なWIG一覧を取得
        $aryWig = $this->Wig_model->getWigRelation($wig_id);

        $data['wig'] = $aryWig;
        $data['header_text'] = '最重要目標実績';
        $data['project_id'] = $project_id;
        $data['wig_id'] = $wig_id;
        $data['back_url'] = site_url('wig/index/' . intval($project_id));

        $aryErrorList = $aryWigProsData = [];
        if($this->isPost()) {
            if($aryWig['type'] == ON) {
                $aryWigPros = $this->input->post('wig_pro');
                $nowValueBK = 0;
                $nowValue = 0;
                $nowValueFlag = false;
                foreach($aryWigPros as $aryWigProsItemKey => $aryWigProsItem) {
                    if($aryWigProsItem['tasebi'] && !$this->valid_date($aryWigProsItem['tasebi'])) {
                        $aryErrorList['date_error'] = '達成日format year error';
                    } else {
                        $dtTasebi = $aryWigProsItem['tasebi'] ? $aryWigProsItem['tasebi'] : null;

                        $aryWigProsData[] = [
                            'id' => $aryWigProsItem['id'],
                            'tasebi' => $dtTasebi
                        ];
                    }
                    if(!$nowValueFlag && $aryWigProsItem['tasebi']) {
                        $nowValue = $aryWigProsItem['tasedo'];
                    }

                    if(!$aryWigProsItem['tasebi']) {
                        $nowValueBK = $aryWigProsItem['tasedo'];
                    }

                    if($nowValueBK && $aryWigProsItem['tasebi']) {
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

            $this->Wig_model->update_by_fields(['now_value' => $nowValue], $wig_id);
            if($aryWigProsData) {
                $this->Wig_Pro_model->update_batch($aryWigProsData, $wig_id);
            }
            $this->redirectSuccess('wig/working/' . intval($wig_id), '正常に保存されました');
        } else {
            if($aryWig) {
                $aryWigPro = $this->Wig_Pro_model->getAllBy(['wig_id' => $aryWig['id']]);
                //update_by_fields
                if (!$this->input->post('wig_id')) {
                    $_POST['wig_id'] = $aryWig['id'];
                    $_POST['now_value'] = $aryWig['now_value'];
                    $_POST['wig_pro'] = $aryWigPro;
                }
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data);
        }
    }

    /**
     * WIG作成画面.
     * @param int $project_id 親スコアボードID
     */
    public function create($project_id)
    {
        // 正しいアクセスかどうか判定
        $this->check_project_id($project_id);

        $data['header_text'] = '最重要目標新規作成';
        $data['project_id'] = $project_id;
        $data['back_url'] = site_url('wig/index/' . intval($project_id));

        $aryErrorList = [];

        if($this->isPost()) {
            $_POST['type'] = isset($_POST['type']) ? ON : OFF;
            $aryRole = ['date' => ['field' => 'from_date|to_date', 'msg' => 'Format year error']];
            if($_POST['type'] == ON) {
                $aryRole['required'] = ['field' => 'what', 'msg' => '必須項目を入力して下さい'];
                if(isset($_POST['wig_pro']) && ($aryWigPros = $_POST['wig_pro'])) {
                    foreach($aryWigPros as $aryWigProsKey => $aryWigProsItem) {
                        // valid null
                        if(!$this->valid_exist($aryWigProsItem['title'])
                            || !$this->valid_exist($aryWigProsItem['tasedo'])
                            || !$this->valid_exist($aryWigProsItem['mokuhyoubi'])) {
                            $aryErrorList['required_error'] = '必須項目を入力して下さい';
                        } else {
                            // valid number
                            if(!$this->valid_numeric($aryWigProsItem['tasedo'])) {
                                $aryErrorList['number_item_error'] = '達成度(%)の入力は半角数字のみとなります。';
                            } else {
                                // valid number 0 ~ 100
                                if($aryWigProsItem['tasedo'] < 0 || $aryWigProsItem['tasedo'] > 100) {
                                    $aryErrorList['number_limit_item_error'] = '達成度(%)は0－100英数字内で入力してください';
                                }
                            }
                            // valid date
                            if(!$this->valid_date($aryWigProsItem['mokuhyoubi'])) {
                                $aryErrorList['date_item_error'] = '目標日format year error';
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
            $newWigId = $this->Wig_model->insert_id($project_id);
            if($_POST['type'] == ON && isset($aryWigPros)) {
                foreach($aryWigPros as $aryWigProsItem) {
                    $aryWigProsData[] = [
                        'wig_id' => $newWigId,
                        'title' => $aryWigProsItem['title'],
                        'mokuhyoubi' => $aryWigProsItem['mokuhyoubi'],
                        'tasedo' => $aryWigProsItem['tasedo'],
                    ];
                }
                if(isset($aryWigProsData)) {
                    $this->Wig_Pro_model->insert_batch($aryWigProsData);
                }
            }
            $this->redirectSuccess($data['back_url'], '正常に保存されました');
        } else {
            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data, 'wig/_form');
        }
    }

    /**
     * 最重要目標編集画面.
     * @param int $wig_id WIG ID
     */
    public function update($wig_id)
    {
        // 正しいアクセスかどうか判定
        $project_id = $this->Wig_model->get_parent_project_id($wig_id);
        // 正しいアクセスかどうか判定
        $this->check_project_id($project_id);

        $data['header_text'] = '最重要目標編集';
        $data['project_id'] = $project_id;
        $data['back_url'] = site_url('wig/index/' . intval($project_id));

        $aryErrorList = $aryWigProsDataInsert = $aryWigProsDataUpdate = $aryWigProsDataDelete = [];

        if($this->isPost()) {
            $_POST['type'] = isset($_POST['type']) ? ON : OFF;
            $aryRole = ['date' => ['field' => 'from_date|to_date', 'msg' => 'Format year error']];
            if($_POST['type'] == ON) {
                $aryRole['required'] = ['field' => 'what', 'msg' => '必須項目を入力して下さい'];
                if(isset($_POST['wig_pro']) && ($aryWigPros = $_POST['wig_pro'])) {
                    foreach($aryWigPros as $aryWigProsKey => $aryWigProsItem) {
                        // add delete wig pro value
                        if(isset($aryWigProsItem['delete']) && isset($aryWigProsItem['id'])) {
                            $aryWigProsDataDelete[] = $aryWigProsItem['id'];
                        }
                        // not delete
                        else {
                            // valid null
                            if(!$this->valid_exist($aryWigProsItem['title'])
                                || !$this->valid_exist($aryWigProsItem['tasedo'])
                                || !$this->valid_exist($aryWigProsItem['mokuhyoubi'])) {
                                $aryErrorList['required_error'] = '必須項目を入力して下さい';
                            } else {
                                // valid number
                                if(!$this->valid_numeric($aryWigProsItem['tasedo'])) {
                                    $aryErrorList['number_item_error'] = '達成度(%)の入力は半角数字のみとなります。';
                                } else {
                                    // valid number 0 ~ 100
                                    if($aryWigProsItem['tasedo'] < 0 || $aryWigProsItem['tasedo'] > 100) {
                                        $aryErrorList['number_limit_item_error'] = '達成度(%)は0－100英数字内で入力してください';
                                    }
                                }
                                // valid date
                                if(!$this->valid_date($aryWigProsItem['mokuhyoubi'])) {
                                    $aryErrorList['date_item_error'] = '目標日format year error';
                                }

                                if(!$aryErrorList) {
                                    //update
                                    if(!isset($aryWigProsItem['id'])) {
                                        $aryWigProsDataInsert[] = [
                                            'wig_id' => $this->input->post('wig_id'),
                                            'title' => $aryWigProsItem['title'],
                                            'mokuhyoubi' => $aryWigProsItem['mokuhyoubi'],
                                            'tasedo' => $aryWigProsItem['tasedo'],
                                        ];
                                    }
                                    //create
                                    else {
                                        $aryWigProsDataUpdate[] = [
                                            'id' => $aryWigProsItem['id'],
                                            'title' => $aryWigProsItem['title'],
                                            'mokuhyoubi' => $aryWigProsItem['mokuhyoubi'],
                                            'tasedo' => $aryWigProsItem['tasedo'],
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
                if(isset($_POST['wig_pro']) && ($aryWigPros = $_POST['wig_pro'])) {
                    foreach($aryWigPros as $aryWigProsKey => $aryWigProsItem) {
                        if(isset($aryWigProsItem['id'])) {
                            $aryWigProsDataDelete[] = $aryWigProsItem['id'];
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
            $wig_id = $this->input->post('wig_id');
            $this->check_wig_id($wig_id);
            $this->Wig_model->update($project_id);
            //insert
            if($aryWigProsDataInsert) {
                $this->Wig_Pro_model->insert_batch($aryWigProsDataInsert);
            }
            //update
            if($aryWigProsDataUpdate) {
                $this->Wig_Pro_model->update_batch($aryWigProsDataUpdate, $wig_id);
            }
            //delete
            if($aryWigProsDataDelete) {
                $this->Wig_Pro_model->delete_checked($aryWigProsDataDelete, $wig_id);
            }
            $this->redirectSuccess($data['back_url'], '正常に保存されました');
        } else {
            if (!$this->input->post('wig_id')) {
                // 現在のデータを初期値として設定する
                $aryWig = $this->Wig_model->get($project_id, true, $wig_id);
                if($aryWig) {
                    $aryWigPro = $this->Wig_Pro_model->getAllBy(['wig_id' => $aryWig['id']]);

                    $_POST['wig_id'] = $aryWig['id'];
                    $_POST['what'] = $aryWig['what'];
                    $_POST['type'] = $aryWig['type'];
                    $_POST['unit'] = $aryWig['unit'];
                    $_POST['from_date'] = $aryWig['from_date'];
                    $_POST['to_date'] = $aryWig['to_date'];
                    $_POST['from_value'] = $aryWig['from_value'];
                    $_POST['to_value'] = $aryWig['to_value'];
                    $_POST['wig_pro'] = $aryWigPro;
                }
            }

            if($aryErrorList) {
                $this->errorFlash($aryErrorList);
            }
            $this->renderView($data, 'wig/_form');
        }
    }

    /**
     * WIG終了処理.
     */
    public function close()
    {
        $wig_id = $this->input->post('id');
        if($wig_id) {
            $this->check_wig_id($wig_id);
            //close Wig
            $this->Wig_model->close($wig_id);
            //get Measure_model
            $aryMeasure = $this->Measure_model->getInAllBy('wig_id', [$wig_id], ['id']);
            $now = date(DATE_UI_DB_FORMAT);
            $aryCloseMeasure = $aryCloseCommitment = [];
            //create array data to update Measure_model
            foreach($aryMeasure as $aryMeasureItemKey => $aryMeasureItem) {
                $aryCloseMeasure[$aryMeasureItemKey]['id'] = $aryMeasureItem['id'];
                $aryCloseMeasure[$aryMeasureItemKey]['closed'] = $now;
                $aryCloseMeasure[$aryMeasureItemKey]['updated'] = $now;

                $aryCloseCommitment[$aryMeasureItemKey]['measure_id'] = $aryMeasureItem['id'];
                $aryCloseCommitment[$aryMeasureItemKey]['delete_flag'] = ON;
                $aryCloseCommitment[$aryMeasureItemKey]['updated'] = $now;
            }
            if($aryCloseMeasure) {
                // close Measure
                $this->Measure_model->update_batch($aryCloseMeasure, 'id');
                //close Commitment
                $this->Commitment_model->update_batch($aryCloseCommitment, 'measure_id');
            }

            $this->successFlash('正常に終了処理されました');
        } else {
            $this->errorFlash('最重要目標は存在しません');
        }
    }

    /**
     * 正常なアクセスかチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $project_id 親スコアボードID
     * @param boolean $is_closed
     * @return array
     */
    private function check_project_id($project_id, $is_closed = false) {
        if(!$this->valid_exist($project_id)) {
            $this->redirectError('project', 'スコアボードは存在しません');
        }
        // ログイン中ユーザがアクセス可能なスコアボードかどうか判定
        $aryProject = $this->Project_model->is_valid_relation($this->user['id'], $project_id, $is_closed);

        if (!$aryProject) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->redirectError('project', 'スコアボードは存在しません');
        }

        return $aryProject;
    }

    /**
     * 正常な更新かチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $wig_id 最重要目標 ID
     * @param boolean $is_closed
     * @return bool
     */
    private function check_wig_id($wig_id, $is_closed = false) {
        // 正当なユーザによるリクエストかチェック
        if(!$this->valid_exist($wig_id)) {
            $this->redirectError('project', '最重要目標は存在しません');
        }

        $aryWig = $this->Wig_model->is_valid_relation($this->user['id'], $wig_id, $is_closed);
        if (!$aryWig) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->redirectError('project', '最重要目標は存在しません');
        }

        return $aryWig;
    }
}
