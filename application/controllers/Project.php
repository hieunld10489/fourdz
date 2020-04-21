<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends Gen_Controller {

    /**
     * TOP画面.
     */
    public function index()
    {
        $data['header_text'] = 'My4DX	Personal';
        $data['back_url'] = '';
        // ユーザの有効なスコアボード一覧を取得
        $data['projects'] = $this->Project_model->get($this->user['id'], true);
        $data['project_commitments'] = $this->Project_model->get_commitments($this->user['id']);

        $this->renderView($data);
    }

    /**
     * もっと見る画面.
     */
    public function more()
    {
        // ユーザのスコアボード一覧を取得
        $data['projects'] = $this->Project_model->get($this->user['id'], false);
        $data['header_text'] = '過去のテーマ';

        $this->renderView($data);
    }

    /**
     * スコアボード作成画面.
     */
    public function create()
    {
        $data['header_text'] = 'テーマ新規作成';
        $aryRole = [
            'required' => ['field' => 'name', 'msg' => '必須項目を入力して下さい'],
            'number' => ['field' => 'category', 'msg' => 'カテゴリーの指定が正しくありません']
        ];
        $aryErrorList = $this->validation_form($aryRole);

        if($this->isPost() && !$aryErrorList) {
            $this->Project_model->insert($this->user['id']);
            $this->redirectSuccess('project', '正常に保存されました');
        } else {
            $data['categories'] = $this->Category_model->get($this->user['id'], FALSE, TRUE);
            $this->errorFlash($aryErrorList);
            $this->renderView($data, 'project/_form');
        }
    }

    /**
     * スコアボード編集画面.
     * @param $project_id
     */
    public function update($project_id)
    {
        //validation param
        $valid = $this->check_update_valid($project_id);
        $project_id = $valid['id'];
        $data['project_id'] = $project_id;
        $data['header_text'] = 'テーマ編集';

        //validation
        $aryRole = [
            'required' => ['field' => 'name|project_id', 'msg' => '必須項目を入力して下さい'],
            'number' => ['field' => 'category', 'msg' => 'カテゴリーの指定が正しくありません']
        ];
        $aryErrorList = $this->validation_form($aryRole);

        if($this->isPost() && !$aryErrorList) {
            // 正当なユーザによるリクエストかチェック
            $this->Project_model->update($this->user['id']);
            $this->redirectSuccess('project', '正常に保存されました');
            exit;
        } else {
            if (!$this->input->post('project_id')) {
                // 現在のデータを初期値として設定する
                $current_data = $this->Project_model->get($this->user['id'], false, $project_id);

                $_POST['name'] = $current_data['title'];
                $_POST['category'] = $current_data['category_id'];
                $_POST['content'] = $current_data['content'];
                $_POST['project_id'] = $current_data['id'];
            }
            $data['categories'] = [];
            if($_POST['category']) {
                $data['categories'] = $this->Category_model->getForUpdateProject($this->user['id'], $_POST['category']);
            }

            $this->errorFlash($aryErrorList);
            $this->renderView($data, 'project/_form');
        }
    }

    /**
     * スコアボード終了処理.
     */
    public function close()
    {
        $project_id = $this->input->post('project_id');
        if($project_id) {
            $this->check_update_valid($project_id);
            $now = date(DATE_UI_DB_FORMAT);
            //create array data to update Project_model
            $this->Project_model->update_by_fields(['closed' => $now, 'updated' => $now], $project_id);
            $aryCloseWig = $aryWigId = $aryCloseMeasure = $aryCloseCommitment = [];
            //get Wig_model
            $aryWig = $this->Wig_model->getAllBy(['project_id' => $project_id], ['id']);
            //create array data to update Wig_model
            foreach($aryWig as $wigItemKey => $wigItem) {
                $aryWigId[$wigItemKey] = $wigItem['id'];
                // add to array for delete Wig
                $aryCloseWig[$wigItemKey]['id'] = $wigItem['id'];
                $aryCloseWig[$wigItemKey]['closed'] = $now;
                $aryCloseWig[$wigItemKey]['updated'] = $now;
            }
            if($aryCloseWig) {
                // close Wig_model
                $this->Wig_model->update_batch($aryCloseWig, 'id');

                //get Measure_model
                $aryMeasure = $this->Measure_model->getInAllBy('wig_id', $aryWigId, ['id']);
                //create array data to update Measure_model
                foreach($aryMeasure as $aryMeasureItemKey => $aryMeasureItem) {
                    // add to array for delete Measure
                    $aryCloseMeasure[$aryMeasureItemKey]['id'] = $aryMeasureItem['id'];
                    $aryCloseMeasure[$aryMeasureItemKey]['closed'] = $now;
                    $aryCloseMeasure[$aryMeasureItemKey]['updated'] = $now;
                    // add to array for delete Commitment
                    $aryCloseCommitment[$aryMeasureItemKey]['measure_id'] = $aryMeasureItem['id'];
                    $aryCloseCommitment[$aryMeasureItemKey]['delete_flag'] = ON;
                    $aryCloseCommitment[$aryMeasureItemKey]['updated'] = $now;
                }
                if($aryCloseMeasure && $aryCloseCommitment) {
                    // close Measure
                    $this->Measure_model->update_batch($aryCloseMeasure, 'id');
                    // close Commitment
                    $this->Commitment_model->update_batch($aryCloseCommitment, 'measure_id');
                }
            }

            $this->successFlash('正常に終了処理されました');
        } else {
            $this->errorFlash('スコアボードは存在しません');
        }
    }

    /**
     * 正常な更新かチェックする.
     * 不正である場合は、ログイン画面に飛ばす.
     * @param int $project_id スコアボードID
     * @return bool
     */
    private function check_update_valid($project_id) {
        // 正当なユーザによるリクエストかチェック
        if(!$this->valid_exist($project_id)) {
            $this->redirectError('project', 'スコアボードは存在しません');
        }
        $aryProject = $this->Project_model->is_valid_relation($this->user['id'], $project_id);
        if(!$aryProject) {
            // もし不正なアクセスだったらログイン画面に飛ばす
            $this->redirectError('project', 'スコアボードは存在しません');
        }
        return $aryProject;
    }
}
