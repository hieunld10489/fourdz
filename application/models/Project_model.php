<?php

class Project_model extends Base_Model
{
    public $table_name = 'projects';

    public $primary_key = 'id';

    /**
     * 指定プロジェクトIDが、指定ユーザIDにぶら下がるものかどうかチェックする.
     * @param  int $user_id    対象ユーザID
     * @param  int $project_id 対象プロジェクトID
     * @param  boolean $is_closed
     * @return array $project=関連がある []=関連がない
     */
    public function is_valid_relation($user_id, $project_id, $is_closed = false) {
        // プロジェクトIDからユーザIDを取得する
        $aryCondition = ['id' => $project_id, 'user_id' => $user_id, 'closed' => null];
        if($is_closed) {
            $aryCondition['closed !='] = null;
        }

        $aryProject = $this->db->get_where($this->table_name, $aryCondition)->row_array();
        // 判定
        if ($aryProject) {
            return $aryProject;
        }
        return [];
    }

    public function get_parent_user_id($project_id) {
        $aryProject = $this->db->get_where($this->table_name, ['id' => $project_id])->row_array();
        if($aryProject) {
            return [];
        }
        return $aryProject['user_id'];
    }

    /*
     * $is_active_only
     */
    public function get($user_id, $is_active_only = FALSE, $id = FALSE)
    {
        if ($id === FALSE) {
            $where = ['projects.user_id' => $user_id];
            if ($is_active_only) {
                $where['closed'] = null;
            } else {
                $where['closed !='] = null;
            }
            $aryField = [
                'projects.id'
                ,'projects.title'
                ,'categories.id as category_id'
                ,'categories.title as category_title'
                ,'content'
                ,'closed'
                ,'projects.created'
            ];

            $this->db->select(implode(',', $aryField));
            $this->db->from($this->table_name);
            $this->db->join('categories', 'projects.category_id = categories.id');
            $this->db->where($where);
            $this->db->order_by('projects.updated DESC');
            $query = $this->db->get();
            return $query->result_array();
        }
        $query = $this->db->get_where($this->table_name, ['id' => $id]);
        return $query->row_array();
    }

    /*
     * プロジェクトのコミットメント一覧を取得.
     */
    public function get_commitments($user_id)
    {
        $where = ['projects.user_id' => $user_id];
        $where['projects.closed'] = null;
        $where['wigs.closed'] = null;
        $where['measures.closed'] = null;
        $where['commitments.delete_flag'] = 0;
        $where['commitments.start_monday'] = date('Y-m-d', $this->get_current_week_monday());
        $aryField = [
             'projects.id as project_id'
            ,'commitments.id'
            ,'commitments.start_monday'
            ,'commitments.title'
            ,'commitments.result'
            ,'commitments.created'
        ];

        $this->db->select(implode(',', $aryField));
        $this->db->from($this->table_name);
        $this->db->join('wigs', 'projects.id = wigs.project_id');
        $this->db->join('measures', 'wigs.id = measures.wig_id');
        $this->db->join('commitments as commitments', 'measures.id = commitments.measure_id');
        $this->db->where($where);
        $this->db->order_by('commitments.start_monday', 'DESC');
        $this->db->order_by('commitments.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insert($user_id)
    {
        $data = array(
            'user_id' => $user_id,
            'title' => $this->input->post('name'),
            'category_id' => $this->input->post('category'),
            'content' => $this->input->post('content'),
            'created' => date(DATE_UI_DB_FORMAT),
            'updated' => date(DATE_UI_DB_FORMAT)
        );

        return $this->db->insert($this->table_name, $data);
    }

    public function get_current_week_monday() {
        $now_time = time();
        $day_count = date('w', $now_time); // 今週の日曜日からの日数(0〜6)
        $current_week_monday = strtotime("-{$day_count} day", $now_time); // 今週の日曜日
        $current_week_monday += 60 * 60 * 24 * 1;
        return $current_week_monday;
    }

    public function update($user_id)
    {
        $data = [
            'user_id' => $user_id,
            'title' => $this->input->post('name'),
            'category_id' => $this->input->post('category'),
            'content' => $this->input->post('content'),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];
        $where = ['id' => $this->input->post('project_id')];

        return $this->db->update($this->table_name, $data, $where);
    }

    public function close($project_id) {
        $now = date(DATE_UI_DB_FORMAT);
        $data = ['closed' => $now, 'updated' => $now];

        return $this->db->update($this->table_name, $data, ['id' => $project_id]);
    }
}
