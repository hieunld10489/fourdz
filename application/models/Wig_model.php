<?php

class Wig_model extends Base_Model
{
    public $table_name = 'wigs';
    public $primary_key = 'id';

    /**
     * 指定WIG IDが、指定ユーザIDにぶら下がるものかどうかチェックする.
     * @param  int $user_id 対象ユーザID
     * @param  int $wig_id  対象WIG ID
     * @param  boolean $is_closed
     * @return array $project=関連がある []=関連がない
     */
    public function is_valid_relation($user_id, $wig_id, $is_closed = false) {
        $aryWigCondition = ['id' => $wig_id, 'closed' => null];
        $aryProjectsCondition = ['user_id' => $user_id, 'closed' => null];
        if($is_closed) {
            $aryWigCondition['closed !='] = null;
            $aryProjectsCondition['closed !='] = null;
        }

        // WIGの親プロジェクトIDを取得する
        $aryWig = $this->db->get_where($this->table_name, $aryWigCondition)->row_array();
        if(!$aryWig) {
            return [];
        }
        $aryProjectsCondition['id'] = $aryWig['project_id'];
        // プロジェクトIDからユーザIDを取得する
        $intProjectNum = $this->db->get_where('projects', $aryProjectsCondition)->num_rows();
        // 判定
        if (!$intProjectNum) {
            return [];
        }
        return [
            'project_id' => $aryWig['project_id'],
            'wig_id' => $wig_id
        ];
    }

    public function get_parent_project_id($wig_id, $is_close = false) {
        $query = $this->db->get_where('wigs', ['id' => $wig_id]);
        $wig = $query->row_array();
        return $wig['project_id'];
    }

    public function get($project_id, $is_active_only = false, $id = FALSE)
    {
        if ($id === FALSE) {
            $where = ['project_id' => $project_id];
            if ($is_active_only) {
                $where['closed'] = null;
            } else {
                $where['closed !='] = null;
            }
            $aryField = [
                'id'
                ,'what'
                ,'how_to'
                ,'unit'
                ,'type'
                ,'from_date'
                ,'from_value'
                ,'to_date'
                ,'to_value'
                ,'closed'
                ,'created'
            ];
            $this->db->select(implode(',', $aryField));
            $this->db->from('wigs');
            $this->db->where($where);
            $query = $this->db->get();
            return $query->result_array();
        }
        $query = $this->db->get_where('wigs', ['id' => $id]);
        return $query->row_array();
    }

    public function insert_id($project_id)
    {
        if($this->input->post('type')) {
            $data = [
                'project_id' => $project_id,
                'what' => $this->input->post('what'),
                'how_to' => $this->input->post('how_to'),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'type' => ON,
                'from_value' => 0,
                'to_value' => 100
            ];
        } else {
            $data = [
                'project_id' => $project_id,
                'what' => $this->input->post('what'),
                'how_to' => $this->input->post('how_to'),
                'unit' => $this->input->post('unit'),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'from_value' => $this->input->post('from_value'),
                'to_value' => $this->input->post('to_value')
            ];
        }

        $data['now_value'] = $data['from_value'];
        $data['created'] = date(DATE_UI_DB_FORMAT);
        $data['updated'] = date(DATE_UI_DB_FORMAT);
        $this->db->insert('wigs', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function update($project_id)
    {
        $data = [
            'what' => $this->input->post('what'),
            'how_to' => $this->input->post('how_to'),
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];

        $query = $this->db->get_where($this->table_name, ['id' => $this->input->post('wig_id')]);
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

        return $this->db->update($this->table_name, $data, ['id' => $this->input->post('wig_id')]);
    }

    public function close($wig_id) {
        $now = date(DATE_UI_DB_FORMAT);
        return $this->db->update('wigs', ['closed' => $now, 'updated' => $now], ['id' => $wig_id]);
    }

    public function getAllRelation($project_id, $is_pass = false, $wigs_id = null)
    {
        $where = ['wigs.project_id' => $project_id];
        if ($is_pass) {
            $where['wigs.closed !='] = null;
        } else {
            $where['wigs.closed'] = null;
        }

        if ($wigs_id) {
            $where['wigs.id'] = $wigs_id;
        }

        $intCurrentMonday = $this->Commitment_model->get_current_week();
        $intNextSunday = $intCurrentMonday+ (STR_TIME_ONE_DATE*13);

        $strFormDate = date('Y-m-d', $intCurrentMonday);
        $strToDate = date('Y-m-d', $intNextSunday);

        $aryField = [
            'wigs.id AS wig__id'
            ,'wigs.project_id AS wig__project_id'
            ,'wigs.what AS wig__what'
            ,'wigs.how_to AS wig__how_to'
            ,'wigs.from_date AS wig__from_date'
            ,'(TIMESTAMPDIFF(DAY, wigs.from_date, CURDATE())) AS wig__work_date'
            ,'(TIMESTAMPDIFF(DAY, wigs.from_date, wigs.to_date)) AS wig__diff_from_to'
            ,'wigs.to_date AS wig__to_date'
            ,'wigs.from_value AS wig__from_value'
            ,'wigs.to_value AS wig__to_value'
            ,'wigs.now_value AS wig__now_value'
            ,'wigs.type AS wig__type'
            ,'wigs.unit AS wig__unit'
            ,'wigs.created AS wig__created'

            ,'MAX(wig_pro_yotei.tasedo) AS wig__yotei_tasedo'
            ,'IFNULL(MIN(wig_pro_jisseki.tasedo), 100) AS wig__jisseki_tasedo,'

            ,'measures.id AS measures__id'
            ,'measures.wig_id AS measures__wig_id'
            ,'measures.what AS measures__what'
            ,'measures.how_to AS measures__how_to'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, CURDATE())) AS measures__work_date'
            ,'(TIMESTAMPDIFF(DAY, measures.from_date, measures.to_date)) AS measures__diff_from_to'
            ,'measures.from_date AS measures__from_date'
            ,'measures.to_date AS measures__to_date'
            ,'measures.from_value AS measures__from_value'
            ,'measures.to_value AS measures__to_value'
            ,'measures.now_value AS measures__now_value'
            ,'measures.type AS measures__type'
            ,'measures.unit AS measures__unit'
            ,'measures.created AS measures__created'
            ,'measures.closed AS measures__closed'

            ,'MAX(measure_pro_yotei.tasedo) AS measures__yotei_tasedo'
            ,'IFNULL(MIN(measure_pro_jisseki.tasedo), 100) AS measures__jisseki_tasedo,'

            ,'commitments.id AS commitments__id'
            ,'(commitments.start_monday > "'.$strFormDate.'") AS commitments__is_this_week'
            ,'commitments.measure_id AS commitments__measure_id'
            ,'commitments.title AS commitments__title'
            ,'commitments.start_monday AS commitments__start_monday'
            ,'commitments.result AS commitments__result'
            ,'commitments.created AS commitments__created'
            ,'commitments.delete_flag AS commitments__delete_flag'
        ];

        $this->db->select(implode(',', $aryField));
        $this->db->from('wigs');
        $this->db->join('wig_pro AS wig_pro_yotei', 'wig_pro_yotei.wig_id = wigs.id AND wig_pro_yotei.mokuhyoubi < CURDATE() ', 'left');
        $this->db->join('wig_pro AS wig_pro_jisseki', 'wig_pro_jisseki.wig_id = wigs.id AND wig_pro_jisseki.tasebi IS NULL', 'left');

        if ($is_pass) {
            $this->db->join('measures', 'measures.wig_id = wigs.id', 'left');
            $commitmentWhere = 'commitments.measure_id = measures.id';
        } else {
            $this->db->join('measures', 'measures.wig_id = wigs.id AND measures.closed IS NULL', 'left');
            $commitmentWhere = 'commitments.measure_id = measures.id AND commitments.start_monday >= "'.$strFormDate.'" AND commitments.start_monday <= "'.$strToDate. '" AND commitments.delete_flag = ' . OFF;
        }

        $this->db->join('measure_pro AS measure_pro_yotei', 'measure_pro_yotei.measure_id = measures.id AND measure_pro_yotei.mokuhyoubi < CURDATE()', 'left');
        $this->db->join('measure_pro AS measure_pro_jisseki', 'measure_pro_jisseki.measure_id = measures.id AND measure_pro_jisseki.tasebi IS NULL', 'left');

        $this->db->join('commitments', $commitmentWhere, 'left');
        $this->db->where($where);
        $this->db->group_by('wigs.project_id, wigs.id, measures.id, commitments.id');
        $this->db->order_by('wigs.created DESC');
        $aryTable = $this->db->get()->result_array();

        $aryResult = [];
        foreach($aryTable as $aryTableKey => $aryTableItem) {
            foreach($aryTableItem as $aryDataKey => $aryDataItem) {
                list($strTable, $strField) = explode('__', $aryDataKey);
                if($strTable == 'measures') {
                    if($aryTableItem['measures__id']) {
                        $aryResult[$aryTableItem['wig__id']][$strTable][$aryTableItem['measures__id']][$strField] = $aryDataItem;
                    }
                } elseif($strTable == 'commitments') {
                    if($aryTableItem['commitments__id']) {
                        if ($is_pass) {
                            $aryResult[$aryTableItem['wig__id']]['measures'][$aryTableItem['measures__id']][$strTable][$aryTableItem['commitments__id']][$strField] = $aryDataItem;
                        } else {
                            if($aryTableItem['commitments__is_this_week'] == 0) {
                                $aryResult[$aryTableItem['wig__id']]['measures'][$aryTableItem['measures__id']][$strTable]['this_week'][$aryTableItem['commitments__id']][$strField] = $aryDataItem;
                            } else {
                                $aryResult[$aryTableItem['wig__id']]['measures'][$aryTableItem['measures__id']][$strTable]['next_week'][$aryTableItem['commitments__id']][$strField] = $aryDataItem;
                            }
                        }
                    }
                } else {
                    $aryResult[$aryTableItem['wig__id']][$strTable][$strField] = $aryDataItem;
                }
            }
        }
        return $aryResult;
    }

    public function getWigRelation($wigs_id)
    {
        $where['wigs.closed'] = null;
        $where['wigs.id'] = $wigs_id;

        $aryField = [
            'wigs.id AS wig__id'
            ,'wigs.project_id AS wig__project_id'
            ,'wigs.what AS wig__what'
            ,'wigs.how_to AS wig__how_to'
            ,'wigs.from_date AS wig__from_date'
            ,'(TIMESTAMPDIFF(DAY, wigs.from_date, CURDATE())) AS wig__work_date'
            ,'(TIMESTAMPDIFF(DAY, wigs.from_date, wigs.to_date)) AS wig__diff_from_to'
            ,'wigs.to_date AS wig__to_date'
            ,'wigs.from_value AS wig__from_value'
            ,'wigs.to_value AS wig__to_value'
            ,'wigs.now_value AS wig__now_value'
            ,'wigs.type AS wig__type'
            ,'wigs.unit AS wig__unit'
            ,'wigs.created AS wig__created'

            ,'MAX(wig_pro_yotei.tasedo) AS wig__yotei_tasedo'
            ,'IF(MIN(wig_pro_jisseki.tasedo) > 0, 0, 100) AS wig__jisseki_tasedo,'
        ];

        $this->db->select(implode(',', $aryField));
        $this->db->from('wigs');
        $this->db->join('wig_pro AS wig_pro_yotei', 'wig_pro_yotei.wig_id = wigs.id AND wig_pro_yotei.mokuhyoubi < CURDATE() ', 'left');
        $this->db->join('wig_pro AS wig_pro_jisseki', 'wig_pro_jisseki.wig_id = wigs.id AND wig_pro_jisseki.tasebi IS NULL', 'left');
        $this->db->where($where);
        $this->db->group_by('wigs.id');
        $query = $this->db->get();

        $aryTable = $query->result_array();
        $aryResult = [];
        foreach($aryTable as $aryTableKey => $aryTableItem) {
            foreach($aryTableItem as $aryDataKey => $aryDataItem) {
                list($strTable, $strField) = explode('__', $aryDataKey);
                $aryResult[$strField] = $aryDataItem;
            }
        }

        return $aryResult;
    }

    public function get_by_id($wig_id) {
        // WIGの親プロジェクトIDを取得する
        $query = $this->db->get_where('wigs', ['id' => $wig_id]);
        return $query->row_array();
    }

    public function is_valid_relation_data($user_id, $wig_id, $is_closed = false) {
        // WIGの親プロジェクトIDを取得する
        $aryWigCondition = ['id' => $wig_id, 'closed' => null];
        if($is_closed) {
            $aryWigCondition['closed !='] = null;
        }

        $aryWig = $this->db->get_where($this->table_name, $aryWigCondition)->row_array();
        if(!$aryWig) {
            return [];
        }
        // プロジェクトIDからユーザIDを取得する
        $aryProject = $this->db->get_where('projects', [
            'id' => $aryWig['project_id'],
            'user_id' => $user_id
        ])->row_array();

        if (!$aryProject) {
            return [];
        }

        return [
            'user_id' => $aryProject['user_id'],
            'wig_id' => $aryWig['id'],
            'project_id' => $aryProject['id']
        ];
    }
}
