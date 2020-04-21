<?php

class Wig_Pro_model extends Base_Model
{
    public $table_name = 'wig_pro';
    public $primary_key = 'id';

    public function update_batch($data, $wig_id)
    {
        $this->db->from($this->table_name)->where(['wig_id' => $wig_id]);
        $aryData = $this->db->get()->num_rows();
        if($aryData == count($data)) {
            $this->db->update_batch($this->table_name, $data, $this->primary_key);
            return $this->db->affected_rows() > 0;
        }
    }

    function delete_checked($aryId = [], $wig_id)
    {
        $this->db->from($this->table_name)->where_in('id', implode(',', $aryId))->where(['wig_id' => $wig_id]);
        $aryData = $this->db->get()->result_array();
        if($aryData) {
            $this->db->from($this->table_name)->where_in('id', implode(',', $aryId))->where(['wig_id' => $wig_id]);
            $this->db->delete($this->table_name);
            return $this->db->affected_rows() > 0;
        }
    }
}
