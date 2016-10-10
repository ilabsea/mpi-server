<?php
/**
 * The custom model
 * @author Sokha
 */
class Imodel extends CI_Model {
  const PER_PAGE = 20;
  protected $_errors = array();
  private $_changes = array();

  function __construct() {
    // $this->load->library('form_validation');
    // print_r($this->form_validation);
    parent::__construct();
  }

  function get_errors() {
    return $this->_errors;
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
    foreach($this as $key => $value) {
      if($key != static::primary_key() && !$this->exclude_field($key) )
        $data[$key] = $value;
    }
    return $data;
  }

  function copy_attrs($datas) {
    foreach ($datas as $key => $value) {
      if($key != static::primary_key() && !$this->exclude_field($key))
        $this->{$key} = $value;
    }
    return $this;
  }

  function copy_raw_attrs($datas){
    foreach ($datas as $key => $value) {
      if(!$this->exclude_field($key) )
        $this->{$key} = $value;
    }
    return $this;
  }

  static function all($conditions, $page=1, $order_by=null ){
    $limit = Imodel::PER_PAGE;
    $offset = $page < 2 ? 0 : ($page-1) * $limit;


    $class_name = static::class_name();
    $active_record = new $class_name;

    $active_record->db->where($conditions);
    $active_record->db->from(static::table_name());
    $active_record->db->limit($limit);
    $active_record->db->offset($offset);

    if($order_by)
      $active_record->db->order_by($order_by);

    $query = $active_record->db->get();
    $records = array();

    // ILog::debug_message($active_record->db);

    foreach( $query->result() as $record){
      $active_record = new $class_name;
      $active_record->copy_raw_attrs($record);
      $records[] = $active_record;
    }
    return $records;
  }

  static function find($id){
    $class_name = static::class_name();
    $active_record = new $class_name;

    $primary_key = static::primary_key();
    $conditions = array($primary_key => $id);
    $query = $active_record->db->get_where(static::table_name(), $conditions);

    if($query->num_rows() == 0)
      return null;
    else {
      $result = $query->result();
      $record = $result[0];
      $active_record->copy_raw_attrs($record);
      return $active_record;
    }
  }

  function current_time() {
    return date("Y-m-d H:i:s");
  }

  function exclude_field($field_name){
    return in_array($field_name, array("_errors", "_changes"));
  }

  function insert(){
    if(!isset($this->created_at))
      $this->created_at = $this->current_time();

    if(!isset($this->updated_at))
      $this->updated_at = $this->current_time();

    $this->db->insert(static::table_name(), $this->to_sql_attrs());

    if($this->db->affected_rows() == 0)
      return false;
    else{
      $primary_key = static::primary_key();
      $this->{$primary_key} = $this->db->insert_id();
      return true;
    }
  }

  function update() {
    $class_name = static::class_name();
    if(property_exists($class_name, 'updated_at'))
      $datas['updated_at'] = $this->current_time();

    $this->db->where('id', $this->id());
    $this->db->update(static::table_name(), $this->to_sql_attrs());

    if($this->db->affected_rows() == 0)
      return false;
    else
      return $this;
  }

  function delete(){
    $this->db->delete(static::table_name(), array('id' => $this->id()));
    if($this->db->affected_rows() == 0)
      return false;
    else
      return true;
  }

  function update_attributes($datas){
    $this->copy_attrs($datas);
    $class_name = static::class_name();

    if(property_exists($class_name, 'updated_at'))
      $datas['updated_at'] = $this->current_time();

    $this->_changes = array();

    foreach($datas as $key => $value){
      $this->_changes[] = array($key => [$this->$key, $value]);
      $this->$key = $value;
    }

    if(!$this->validate()){
      ILog::debug_message($this->get_errors());
      return false;
    }

    $this->db->where('id', $this->id());
    $this->db->update(static::table_name(), $datas);
    ILog::debug_message($this->db);

    if($this->db->affected_rows() == 0)
      return null;
    else{
      return $this;
    }
  }

  function validate(){
    $this->validation_rules();
    if($this->form_validation->run() == false && $this->new_record()){
      $this->_errors = $this->form_validation->errors();
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
