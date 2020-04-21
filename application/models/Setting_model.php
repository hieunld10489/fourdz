<?php

class Setting_model extends Base_Model
{
    public $table_name = 'user_settings';

    public $primary_key = 'user_id';

    /*
     */
    public function get($value)
    {
        $query = $this->db->get_where($this->table_name, [$this->primary_key => $value]);
        return $query->row_array();
    }

    public function insert($user_id)
    {
        $data = [
            'user_id' => $user_id,
            'name' => $this->input->post('name'),
            'week_start_monday' => $this->input->post('week_start_monday'),
            'zone_rate_red' => $this->input->post('zone_rate_red'),
            'zone_rate_green' => $this->input->post('zone_rate_green'),
            'created' => date(DATE_UI_DB_FORMAT),
            'updated' => date(DATE_UI_DB_FORMAT),
        ];
        return $this->db->insert($this->table_name, $data);
    }

    public function update($user_id)
    {
        $data = [
            'name' => $this->input->post('name'),
            'week_start_monday' => $this->input->post('week_start_monday'),
            'zone_rate_red' => $this->input->post('zone_rate_red'),
            'zone_rate_green' => $this->input->post('zone_rate_green'),
            'updated' => date(DATE_UI_DB_FORMAT)
        ];

        return $this->db->update($this->table_name, $data, [$this->primary_key => $user_id]);
    }
}
