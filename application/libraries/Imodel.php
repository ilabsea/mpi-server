<?php
/**
 * The custom model
 * @author Sokha
 */
class Imodel extends CI_Model {
  public $errors = array();

  function __construct() {
    // $this->load->library('form_validation');
    // print_r($this->form_validation);
    parent::__construct();

  }

  static function primary_key() {
    return 'id';
  }

  function new_record() {
    return !$this->id();
  }

  function id() {
    $primary_key = static::primary_key();
    return $this->{$primary_key};
  }

  static function table_name() {
    return "";
  }
  static function class_name() {
    return "";
  }

  function load_other_model($model_name) {
    $CI =& get_instance();
    $CI->load->model($model_name);
    return $CI->$model_name;
  }

  function build($datas) {
    return $this->copy_attrs($datas);
  }

  function to_sql_attrs(){
    $datas = array();
    foreach($this as $field => $value) {
      if($field != static::primary_key() && $field !== "errors" )
        $data[$field] = $value;
    }
    return $data;
  }

  function copy_attrs($datas) {
    foreach ($datas as $key => $value) {
      if($key != static::primary_key() && $key !== 'errors')
        $this->{$key} = $value;
    }
    return $this;
  }

  function copy_raw_attrs($datas){
    foreach ($datas as $key => $value) {
      if($key != 'errors')
        $this->{$key} = $value;
    }
    return $this;
  }

  function insert(){
    if(!isset($this->created_at))
      $this->created_at = time();

    if(!isset($this->updated_at))
      $this->updated_at = time();

    $this->db->insert(static::table_name(), $this->to_sql_attrs());

    if($this->db->affected_rows() == 0)
      return false;
    else{
      $primary_key = static::primary_key();
      $this->{$primary_key} = $this->db->insert_id();
    }
  }

  function update() {
    $class_name = static::class_name();
    if(property_exists($class_name, 'updated_at'))
      $datas['updated_at'] = time();

    $this->db->where('id', $this->id());
    $this->db->update(static::table_name(), $this->to_sql_attrs());

    if($this->db->affected_rows() == 0)
      return false;
    else
      return $this;
  }

  function update_attributes($datas){
    $this->copy_attrs($datas);
    $class_name = static::class_name();

    if(property_exists($class_name, 'updated_at'))
      $datas['updated_at'] = time();

    $this->db->where('id', $this->id());
    $this->db->update(static::table_name(), $datas);
    if($this->db->affected_rows() == 0)
      return false;

    else{
      $this->copy_attrs($datas);
      return $this;
    }
  }

  static function find($id){
    $query = $this->db->get_where(static::table_name(), array($primary_key => $id));
    if($query->num_rows())
      return null;
    else {
      $result = $query->result();
      $record = $result[0];
      $class_name = static::class_name();
      $active_record = new $class_name;
      $active_record->copy_raw_attrs($record);
      return $active_record;
    }
  }

  function validate(){

    $this->validation_rules();
    if($this->form_validation->run() == false){
      $this->errors = $this->form_validation->errors();
      return false;
    }
    return true;
  }

  function save(){
    if(!$this->validate())
      return false;
    return $this->new_record() ? $this->insert() : $this->update();
  }
}
