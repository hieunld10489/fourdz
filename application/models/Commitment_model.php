<?php

class Commitment_model extends Base_Model
{

    public $table_name = 'commitments';

    public static $COMMITMENT_RESULT = [0 => '-', 10 => '○', 20=>'△', 30=>'×'];

    public function is_valid_relation($user_id, $commitment_id) {
        // コミットメントの親LM IDを取得する
        $query = $this->db->get_where($this->table_name, ['id' => $commitment_id, 'delete_flag' => 0]);
        $commitment = $query->row_array();
        $measure_id = $commitment['measure_id'];
        // LMの親WIG IDを取得する
        $query = $this->db->get_where('measures', ['id' => $measure_id]);
        $measure = $query->row_array();
        $wig_id = $measure['wig_id'];
        // WIGの親プロジェクトIDを取得する
        $query = $this->db->get_where('wigs', ['id' => $wig_id]);
        $wig = $query->row_array();
        $project_id = $wig['project_id'];
        // プロジェクトIDからユーザIDを取得する
        $query = $this->db->get_where('projects', ['id' => $project_id, 'user_id' => $user_id]);
        $project = $query->row_array();
        // 判定
        if ($project) {
            return [
                'measure_id' => $measure_id
                ,'wig_id' => $wig_id
                ,'project_id' => $project_id
            ];
        }

        log_message('info', 'measure invalid relation');
        return false;
    }

    public function get_parent_measure_id($commitment_id) {
        $query = $this->db->get_where('commitments', ['id' => $commitment_id, 'delete_flag' => 0]);
        $commitment = $query->row_array();
        return $commitment['measure_id'];
    }

    public function get($measure_id, $start_monday_time = null)
    {
        $where = array(
            'measure_id'  => $measure_id,
            'delete_flag' => 0
        );
        if ($start_monday_time) {
            $where['start_monday'] = date('Y-m-d', $start_monday_time);
        }
        $this->db->select('id, start_monday, title, result, created');
        $this->db->from('commitments');
        $this->db->where($where);
        $this->db->order_by('start_monday', 'DESC');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_by_id($commitment_id)
    {
        $where = array(
            'id' => $commitment_id,
            'delete_flag' => 0
        );
        $query = $this->db->get_where('commitments', $where);
        return $query->row_array();
    }

    public function get_current_week_sunday() {
        $now_time = time();
        $day_count = date('w', $now_time); // 今週の日曜日からの日数(0〜6)
        $current_week_sunday = strtotime("-{$day_count} day", $now_time); // 今週の日曜日
        return $current_week_sunday;
    }

    public function get_current_week_monday() {
        $now_time = time();
        $day_count = date('w', $now_time); // 今週の日曜日からの日数(0〜6)
        $current_week_monday = strtotime("-{$day_count} day", $now_time); // 今週の日曜日
        $current_week_monday += 60 * 60 * 24 * 1;
        return $current_week_monday;
    }

    public function get_current_week($isMonday = false) {
        $now_time = time();
        $day_count = date('w', $now_time); // 今週の日曜日からの日数(0〜6)
        if($isMonday && $day_count == 0) {
            $day_count = 7;
        }
        $current_week_monday = strtotime("-{$day_count} day", $now_time); // 今週の日曜日
        $current_week_monday += STR_TIME_ONE_DATE;
        return $current_week_monday;
    }

    public function get_next_week($currentValue) {
        $next_week_monday = $currentValue + (STR_TIME_ONE_DATE * 7);
        return $next_week_monday;
    }

    public function createDate($isMonday) {

        $current_week_monday = $this->get_current_week_monday(); // 今週頭の日曜日
        // 期間の候補を用意
        $mondayThisWeek = date(DATE_UI_FORMAT_2, $current_week_monday); // 今週頭の日曜日
        $mondayNextWeek = date(DATE_UI_FORMAT_2, $current_week_monday + (STR_TIME_ONE_DATE*7)); // 来週頭の日曜日

        if($isMonday) {
            $int_current_week_monday = $this->get_current_week(true);
            $int_next_week_monday = $this->get_next_week($int_current_week_monday);
            $thisWeek = date(DATE_UI_FORMAT, $int_current_week_monday) . '〜' . date(DATE_UI_FORMAT, $int_current_week_monday + (STR_TIME_ONE_DATE*6));
            $nextWeek = date(DATE_UI_FORMAT, $int_next_week_monday) . '〜' . date(DATE_UI_FORMAT, $int_next_week_monday + (STR_TIME_ONE_DATE*6));
        } else {
            $int_current_week_monday = $this->get_current_week();
            $int_next_week_monday = $this->get_next_week($int_current_week_monday);
            $thisWeek = date(DATE_UI_FORMAT, $int_current_week_monday - STR_TIME_ONE_DATE) . '〜' . date(DATE_UI_FORMAT, $int_current_week_monday + (STR_TIME_ONE_DATE * 5));
            $nextWeek = date(DATE_UI_FORMAT, $int_next_week_monday - STR_TIME_ONE_DATE) . '〜' . date(DATE_UI_FORMAT, $int_next_week_monday + (STR_TIME_ONE_DATE * 5));
        }

        $aryKikan[$mondayThisWeek] = $thisWeek;
        $aryKikan[$mondayNextWeek] = $nextWeek;

        return $aryKikan;
    }

    public function insert($measure_id)
    {
        $data = [
            'measure_id' => $measure_id,
            'title' => $this->input->post('name'),
            'start_monday' => $this->input->post('start_monday'),
            'created' => date(DATE_UI_DB_FORMAT),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];

        return $this->db->insert('commitments', $data);
    }

    public function update($commitment_id)
    {
        $data = [
            'title' => $this->input->post('name'),
            'start_monday' => $this->input->post('start_monday'),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];

        return $this->db->update('commitments', $data, ['id' => $commitment_id]);
    }

    public function delete($commitment_id) {
        $data = [
            'delete_flag' => ON,
            'updated' => date(DATE_UI_DB_FORMAT)];
        return $this->db->update('commitments', $data, ['id' => $commitment_id, 'delete_flag' => OFF]);
    }

    public function status($commitment_id, $result) {
        $data = [
            'result' => $result,
            'updated' => date(DATE_UI_DB_FORMAT)
        ];

        return $this->db->update('commitments', $data, ['id' => $commitment_id]);
    }

    public function getPassDeletedByUser($user_id)
    {
        $this->db->select('co.*');
        $this->db->from('commitments AS co');
        $this->db->join('measures me', 'me.id = co.measure_id' , 'left');
        $this->db->join('wigs w', 'w.id = me.wig_id' , 'left');
        $this->db->join('projects pr', 'pr.id = w.project_id' , 'left');
        $this->db->where([
            'pr.user_id' => $user_id,
            'co.delete_flag' => ON
        ]);
        $this->db->order_by('start_monday', 'DESC');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
