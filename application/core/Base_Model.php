<?php

/*
 * https://www.codeigniter.com/userguide3/database/index.html
 */

class Base_Model extends CI_Model {

    static public $model;
    static public $db;

    //User
    public static $ZONE_RATE_RED = 30;
    public static $ZONE_RATE_GREEN = 70;

    public function __construct()
    {
        parent::__construct();

        self::$model = $this->load->database();
        self::$db = $this->db;
    }

    public function class_name()
    {
        return __CLASS__;
    }

    public function getById($value)
    {
        $primary_key = 'id';
        if(isset($this->primary_key)) {
            $primary_key = $this->primary_key;
        }

        if($this->table_name) {
            self::$db->from($this->table_name);
            self::$db->where([$primary_key => $value]);
            $query = self::$db->get();
            return $query->row_array();
        }
        return [];
    }

    public function getAllBy($where = [], $field = [])
    {
        $strField = '*';
        if($field) {
            $strField = implode(',', $field);
        }
        $this->db->select($strField);
        $this->db->from($this->table_name);
        if($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getFirstBy($where = [], $field = [])
    {
        $strField = '*';
        if($field) {
            $strField = implode(',', $field);
        }
        $this->db->select($strField);
        $this->db->from($this->table_name);
        if($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getInAllBy($key, $where = [], $field = [])
    {
        $strField = '*';
        if($field) {
            $strField = implode(',', $field);
        }
        $this->db->select($strField);
        $this->db->from($this->table_name);
        if($where) {
            $this->db->where_in($key, $where);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insert_one($data) {
        return $this->db->insert($this->table_name, $data);
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table_name, $data);
    }

    public function update_by_fields($data = [], $id)
    {
        return $this->db->update($this->table_name, $data, [$this->primary_key => $id]);
    }

    public function update_batch($lsData = [], $key)
    {
        return $this->db->update_batch($this->table_name, $lsData, $key);
    }
}
