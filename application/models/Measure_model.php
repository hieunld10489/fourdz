<?php

class Measure_model extends Base_Model
{
    public $table_name = 'measures';
    public $primary_key = 'id';
    /**
     * 指定LM IDが、指定ユーザIDにぶら下がるものかどうかチェックする.
     * @param  int $user_id    対象ユーザID
     * @param  int $measure_id 対象LM ID
     * @return boolean         結果 true=関連がある false=関連がない
     */
    public function is_valid_relation($user_id, $measure_id) {
        // LMの親WIG IDを取得する
        $query = $this->db->get_where($this->table_name, array('id' => $measure_id));
        $measure = $query->row_array();
        $wig_id = $measure['wig_id'];
        // WIGの親プロジェクトIDを取得する
        $query = $this->db->get_where('wigs', array('id' => $wig_id));
        $wig = $query->row_array();
        $project_id = $wig['project_id'];
        // プロジェクトIDからユーザIDを取得する
        $query = $this->db->get_where('projects', array('id' => $project_id));
        $project = $query->row_array();
        // 判定
        if ($user_id == $project['user_id']) {
            return ['project_id' => $project_id];
        }

        log_message('info', 'measure invalid relation');
        return false;
    }

    public function get_parent_wig_id($measure_id) {
        $query = $this->db->get_where($this->table_name, array('id' => $measure_id));
        $measure = $query->row_array();
        return $measure['wig_id'];
    }

    public function get($wig_id, $is_active_only = false, $id = FALSE)
    {
        if ($id === FALSE) {
            $where = array(
                'wig_id' => $wig_id
            );
            if ($is_active_only) {
                $where['closed'] = null;
            } else {
                $where['closed !='] = null;
            }
            $this->db->select('id, what, how_to, from_date, from_value, to_date, to_value, closed, created');
            $this->db->from($this->table_name);
            $this->db->where($where);
            $query = $this->db->get();
            return $query->result_array();
        }
        $query = $this->db->get_where($this->table_name, array('id' => $id));
        return $query->row_array();
    }

    public function insert()
    {
        $data = array(
            'name' => $this->input->post('name')
        );

        return $this->db->insert('menus', $data);
    }

    public function insert_id($wig_id)
    {
        $data['wig_id'] = $wig_id;
        $data['what'] = $this->input->post('what');
        $data['how_to'] = $this->input->post('how_to');
        $data['from_date'] = $this->input->post('from_date');
        $data['to_date'] = $this->input->post('to_date');
        $data['created'] = date(DATE_UI_DB_FORMAT);
        $data['updated'] = date(DATE_UI_DB_FORMAT);
        if($this->input->post('type')) {
            $data['type'] = ON;
            $data['from_value'] = 0;
            $data['to_value'] = 100;
        } else {
            $data['unit'] = $this->input->post('unit');
            $data['from_value'] = $this->input->post('from_value');
            $data['to_value'] = $this->input->post('to_value');
        }
        $data['now_value'] = $data['from_value'];
        $this->db->insert($this->table_name, $data);
        return $this->db->insert_id();
    }

    public function update($wig_id)
    {
        $data = [
            'what' => $this->input->post('what'),
            'how_to' => $this->input->post('how_to'),
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];
        $query = $this->db->get_where($this->table_name, ['id' => $this->input->post('measure_id')]);
        $wig = $query->row_array();
        if($this->input->post('type')) {
            $data['type'] = ON;
            $data['from_value'] = 0;
            $data['to_value'] = 100;
            if($wig['type'] == OFF) {
                $data['now_value'] = $data['from_value'];
            }
        } else {
            $data['type'] = OFF;
            $data['unit'] = $this->input->post('unit');
            $data['from_value'] = $this->input->post('from_value');
            $data['to_value'] = $this->input->post('to_value');
            if($wig['type'] == ON) {
                $data['now_value'] = $data['from_value'];
            }
        }

        return $this->db->update($this->table_name, $data, ['id' => $this->input->post('measure_id')]);
    }

    public function getMeasuresWorking($measures_id)
    {
        $where['measures.closed'] = null;
        $where['measures.id'] = $measures_id;

        $aryField = [
            'measures.id AS measures__id'
            ,'measures.wig_id AS measures__wig_id'
            ,'measures.what AS measures__what'
            ,'measures.how_to AS measures__how_to'
            ,'measures.unit AS measures__unit'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, CURDATE())) AS measures__work_date'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, measures.to_date)) AS measures__diff_from_to'
            ,'measures.from_date AS measures__from_date'
            ,'measures.to_date AS measures__to_date'
            ,'measures.from_value AS measures__from_value'
            ,'measures.to_value AS measures__to_value'
            ,'measures.now_value AS measures__now_value'
            ,'measures.type AS measures__type'
            ,'measures.created AS measures__created'

            ,'MAX(measure_pro_yotei.tasedo) AS measures__yotei_tasedo'
            ,'IFNULL(MIN(measure_pro_jisseki.tasedo), 100) AS measures__jisseki_tasedo,'
        ];

        $this->db->select(implode(',', $aryField));
        $this->db->from('measures');
        $this->db->join('measure_pro AS measure_pro_yotei', 'measure_pro_yotei.measure_id = measures.id AND measure_pro_yotei.mokuhyoubi < CURDATE()', 'left');
        $this->db->join('measure_pro AS measure_pro_jisseki', 'measure_pro_jisseki.measure_id = measures.id AND measure_pro_jisseki.tasebi IS NULL', 'left');
        $this->db->where($where);
        $this->db->group_by('measures.id');
        $this->db->order_by('measures.updated DESC');
        $query = $this->db->get();

        $aryTable = $query->result_array();
        $aryResult = [];
        foreach($aryTable as $aryTableKey => $aryTableItem) {
            foreach($aryTableItem as $aryDataKey => $aryDataItem) {
                list($strTable, $strField) = explode('__', $aryDataKey);
                if($aryTableItem['measures__id']) {
                    $aryResult[$strField] = $aryDataItem;
                }
            }
        }

        return $aryResult;
    }

    public function getPassClosedByUser($user_id)
    {
        $where = [
            'pr.user_id' => $user_id,
            'measures.closed !=' => null
        ];

        $aryField = [
            'measures.id AS measures__id'
            ,'measures.wig_id AS measures__wig_id'
            ,'measures.what AS measures__what'
            ,'measures.how_to AS measures__how_to'
            ,'measures.unit AS measures__unit'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, CURDATE())) AS measures__work_date'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, measures.to_date)) AS measures__diff_from_to'
            ,'measures.from_date AS measures__from_date'
            ,'measures.to_date AS measures__to_date'
            ,'measures.from_value AS measures__from_value'
            ,'measures.to_value AS measures__to_value'
            ,'measures.now_value AS measures__now_value'
            ,'measures.type AS measures__type'
            ,'measures.created AS measures__created'
            ,'measures.closed AS measures__closed'

            ,'MAX(measure_pro_yotei.tasedo) AS measures__yotei_tasedo'
            ,'IFNULL(MIN(measure_pro_jisseki.tasedo), 100) AS measures__jisseki_tasedo,'
        ];

        $this->db->select(implode(',', $aryField));
        $this->db->from('measures');
        $this->db->join('measure_pro AS measure_pro_yotei', 'measure_pro_yotei.measure_id = measures.id AND measure_pro_yotei.mokuhyoubi < CURDATE()', 'left');
        $this->db->join('measure_pro AS measure_pro_jisseki', 'measure_pro_jisseki.measure_id = measures.id AND measure_pro_jisseki.tasebi IS NULL', 'left');
        $this->db->join('wigs w', 'w.id = measures.wig_id' , 'left');
        $this->db->join('projects pr', 'pr.id = w.project_id' , 'left');
        $this->db->where($where);
        $this->db->group_by('measures.id');
        $this->db->order_by('measures.closed', 'DESC');
        $this->db->order_by('measures.id', 'ASC');
        $query = $this->db->get();

        $aryTable = $query->result_array();
        $aryResult = [];
        foreach($aryTable as $aryTableKey => $aryTableItem) {
            foreach($aryTableItem as $aryDataKey => $aryDataItem) {
                list($strTable, $strField) = explode('__', $aryDataKey);
                if($aryTableItem['measures__id']) {
                    $aryResult[$aryTableKey][$strField] = $aryDataItem;
                }
            }
        }

        return $aryResult;
    }

    // 終了しているLMを複製して新規作成する.
    public function clone($from_measure_id)
    {
        // 元LMを取得
        $from_data = $this->get(0, false, $from_measure_id);
        // 複製
        $data = array();
        $data['wig_id']     = $from_data['wig_id'];
        $data['what']       = $from_data['what'];
        $data['how_to']     = $from_data['how_to'];
        $data['from_date']  = date(DATE_UI_DB_FORMAT);
        $data['from_value'] = $from_data['from_value'];
        $data['to_date']    = date(DATE_UI_DB_FORMAT, time() + (60*60*24*7));
        $data['to_value']   = $from_data['to_value'];
        $data['now_value']  = $from_data['from_value']; // fromと同じにする
        $data['type']       = $from_data['type'];
        $data['unit']       = $from_data['unit'];
        $data['closed']     = null;
        $data['created']    = date(DATE_UI_DB_FORMAT);
        $data['updated']    = date(DATE_UI_DB_FORMAT);
        $this->db->insert($this->table_name, $data);

        // 元LMのコミットメントを取得
        // $this->load->model('Commitment');
        // $this->Commitment->get($from_measure_id);
    }
}
