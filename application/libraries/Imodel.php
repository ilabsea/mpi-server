<?php
class Imodel extends CI_Model {
  const PER_PAGE = 100;
  protected $_errors = array();
  protected $_changes = array();
  protected $_not_sql_fields = array('_errors', '_changes', '_not_sql_fields');

  // define your table primary key name here
  static function primary_key() {
    return 'id';
  }

  // define the name of your table
  static function table_name() {
    throw new Exception("You must overide this method to return your database table name");
  }

  //define your class name php older version
  static function class_name() {
    throw new Exception("You must overide this method to return your model name");
  }

  //if you have fields in model that are not part of db table field list them down here
  static function accessible_fields(){
    return array();
  }

  static function serialize_fields(){
    return array();
  }
  #Callback
  function before_validate() {}
  function before_create() {}
  function after_create(){}
  function before_update() {}
  function after_update() {}
  function after_destroy(){}

  function __construct($attrs = array()) {
    parent::__construct();
    $this->load->library('form_validation');
    $this->set_attributes($attrs);
  }

  function get_errors() {
    return $this->_errors;
  }

  function get_changes() {
    return $this->_changes();
  }

  function new_record() {
    return !$this->id();
  }

  function id() {
    $primary_key = static::primary_key();
    return $this->{$primary_key};
  }

  function load_other_model($model_name) {
    $CI =& get_instance();
    $CI->load->model($model_name);
    return $CI->$model_name;
  }

  function current_time() {
    return date("Y-m-d H:i:s");
  }

  function current_date() {
    return date("Y-m-d");
  }

  //create create uniqueness validation for this shi**y framework
  function uniqueness_field($field_name) {
    $table_name = static::table_name();
    $unique_validator = "is_unique[{$table_name}.{$field_name}]";
    $escape_unique_validator = "";

    if($this->new_record())
      return $unique_validator;

    $class_name = static::class_name();

    $record = $class_name::find($this->id());
    return $this->$field_name == $record->$field_name ? $escape_unique_validator : $unique_validator;
  }


  static function timestampable() {
    return false;
  }

  function exclude_field($field_name){
    $fields = array_merge($this->_not_sql_fields, static::accessible_fields());
    return in_array($field_name, $fields);
  }

  function set_attribute($field, $value){
    if($this->exclude_field($field) || $field == static::primary_key())
      return;

    $this->_changes[$field] = array($this->$field, $value);
    $this->$field = $value;
  }

  function set_attributes($datas) {
    $this->_changes = array();
    foreach($datas as $field => $value)
      $this->set_attribute($field, $value);
  }

  function get_attributes($fields = null) {
    if($fields == nil)
      return $this->sql_attributes();
    else {
      $attributes = array();
      foreach($fields as $field) {
        if($this->exclude_field($field) || $field == static::primary_key() )
          continue;
        $attributes[$field] = static::is_serialized($field) ? serialize($value) : $value;
      }
      return $attributes;
    }
  }

  function sql_attributes(){
    $attributes = array();
    foreach ($this as $field => $value) {
      if($this->exclude_field($field) || $field == static::primary_key() )
        continue;
      $attributes[$field] = static::is_serialized($field) ? serialize($value) : $value;
    }
    return $attributes;
  }

  function copy_object($record) {
    foreach($record as $field => $value) {
      if($this->exclude_field($field))
         continue;
      $field_value = static::is_serialized($field) ? unserialize($value) : $value;
      $this->$field = $field_value;
    }
    return $this;
  }

  static function is_serialized($field) {
    return in_array($field, static::serialize_fields());
  }

  static function count($conditions=array()){
    $class_name = static::class_name();
    $active_record = new $class_name;

    foreach($conditions as $field => $value){
      if(is_array($value))
        $active_record->db->where_in($field, $value);
      else
        $active_record->db->where($field, $value);
    }

    $active_record->db->from(static::table_name());
    $count = $active_record->db->count_all_results();
    ILog::debug_message("db query", $active_record->db->queries);
    return $count;
  }


  static function all($conditions=array(), $page=null, $order_by=null ){
    $limit = Imodel::PER_PAGE;

    $class_name = static::class_name();
    $active_record = new $class_name;

    foreach($conditions as $field => $value){
      if(is_array($value))
        $active_record->db->where_in($field, $value);
      else
        $active_record->db->where($field, $value);
    }

    $active_record->db->from(static::table_name());

    if($page) {
      $offset = $page < 2 ? 0 : ($page-1) * $limit;
      $active_record->db->limit($limit);
      $active_record->db->offset($offset);
    }

    if($order_by)
      $active_record->db->order_by($order_by);

    $query = $active_record->db->get();
    $records = array();

    foreach( $query->result() as $record){
      $active_record = new $class_name;
      $active_record->copy_object($record);
      $records[] = $active_record;
    }
    return $records;
  }

  static function find_by($conditions){
    $class_name = static::class_name();
    $active_record = new $class_name;
    $active_record->db->from(static::table_name());

    $active_record->db->where($conditions);
    $active_record->db->limit(1);
    $active_record->db->order_by("id DESC");

    $query = $active_record->db->get();

    if($query->num_rows() == 0)
      return null;

    $result = $query->result();

    $find_record = new $class_name;
    $record = $result[0];

    $find_record->copy_object($record);
    return $find_record;
  }

  static function find($id){
    $class_name = static::class_name();
    $active_record = new $class_name;
    $primary_key = static::primary_key();

    $active_record->db->from(static::table_name());

    if(is_array($id)){
      if(count($id) == 0)
        return array();
      $active_record->db->where_in($primary_key, $id);
    }
    else
      $active_record->db->where(array($primary_key => $id));

    $query = $active_record->db->get();

    if($query->num_rows() == 0)
      return null;

    $result = $query->result();

    if(is_array($id)){
      $find_records = array();
      foreach($result as $record) {
        $find_record = new $class_name;
        $find_record->copy_object($record);
        $find_records[] = $find_record;
      }
      return $find_records;
    }
    else {
      $find_record = new $class_name;
      $record = $result[0];
      $find_record->copy_object($record);
      return $find_record;
    }
  }

  function insert(){
    if(static::timestampable()) {
      $this->created_at = $this->current_time();
      $this->updated_at = $this->current_time();
    }
    $this->before_create();

    $this->db->insert(static::table_name(), $this->sql_attributes());

    if($this->db->affected_rows() == 0)
      return false;
    else{
      $primary_key = static::primary_key();
      $this->{$primary_key} = $this->db->insert_id();
      $this->after_create();
      return true;
    }
  }

  function update() {
    $class_name = static::class_name();
    if(static::timestampable())
      $this->set_attribute('updated_at', $this->current_time());

    $this->before_update();

    $this->db->where($this->primary_key(), $this->id());
    $this->db->update(static::table_name(), $this->change_attributes());

    if($this->db->affected_rows() == 0)
      return false;
    else{
      $this->after_update();
      return $this;
    }
  }

  function delete(){
    $this->db->delete(static::table_name(), array('id' => $this->id()));
    if($this->db->affected_rows() == 0)
      return false;
    else{
      $this->after_destroy();
      return true;
    }
  }

  function update_attributes($datas){

    $this->set_attributes($datas);
    $class_name = static::class_name();

    if(static::timestampable())
      $this->set_attribute('updated_at', $this->current_time());

    if(!$this->validate()){
      return false;
    }

    $this->before_update();

    $this->db->where($this->primary_key(), $this->id());
    $this->db->update(static::table_name(), $this->change_attributes());

    if($this->db->affected_rows() == 0)
      return null;
    else{
      $this->after_update();
      return $this;
    }
  }

  function change_attributes() {
    $attrs = array();
    foreach($this->_changes as $field => $change)
      $attrs[$field] = $change[1];
    return $attrs;
  }

  /* validator requires data from $_POST */
  function set_data_to_validate(){
    foreach($this as $field => $value) {
      if($this->exclude_field($field))
        continue;
      $_POST[$field] = $value;
    }
  }

  //redefine on child model
  function validation_rules() {
    return false;
  }

  function validate(){
    $this->set_data_to_validate();

    if($this->validation_rules() && $this->form_validation->run() == false){
      $this->_errors = $this->form_validation->errors();
      return false;
    }
    return true;
  }

  function save($validate = true){
    $this->before_validate();

    if($validate && !$this->validate())
      return false;
    return $this->new_record() ? $this->insert() : $this->update();
  }
}
