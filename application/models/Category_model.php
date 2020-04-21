<?php

class Category_model extends Base_Model
{

    public $table_name = 'categories';

    public function get($user_id, $id = FALSE, $isDelete = FALSE)
    {
        if($isDelete) {
            $condition['deleted'] = null;
        }

        $condition['user_id'] = $user_id;
        if ($id === FALSE) {
            $this->db->select('id, title, created');
            $this->db->from('categories');
            $this->db->where($condition);
            $query = $this->db->get();
            return $query->result_array();
        }
        $condition['id'] = $id;
        $query = $this->db->get_where('categories', $condition);
        return $query->row_array();
    }

    public function insert()
    {
        return $this->db->insert('menus', ['name' => $this->input->post('name')]);
    }

    public function insertInSetting($user_id, $title)
    {
        return $this->db->insert($this->table_name, [
            'user_id' => $user_id,
            'title' => mb_substr($title, 0, 50),
            'created' => date(DATE_UI_DB_FORMAT),
            'updated' => date(DATE_UI_DB_FORMAT)]);
    }

    public function deleted($user_id, $id)
    {
        return $this->db->update($this->table_name, [
            'deleted' => date(DATE_UI_DB_FORMAT),
            'updated' => date(DATE_UI_DB_FORMAT)
        ], ['id' => $id, 'user_id' => $user_id]);
    }

    public function getForUpdateProject($user_id, $category_id = FALSE)
    {
        $condition['deleted'] = null;
        $condition['user_id'] = $user_id;
        $this->db->select('id, title, created');
        $this->db->from('categories');
        $this->db->where($condition);
        if($category_id) {
            $this->db->or_where(['id' => $category_id]);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
}
