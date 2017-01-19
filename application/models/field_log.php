<?php
class FieldLog extends Imodel {
  var $id = null;
  var $field_name = '';
  var $field_code = '';
  var $modified_at = '';
  var $modified_by = '';

  var $modified_fields = array();

  var $created_at = null;
  var $updated_at = null;

  function before_save(){
    parent::before_save();
    $this->set_attribute("modified_at", AppModel::current_time());
  }

  static function timestampable() {
    return true;
  }

  static function serialize_fields() {
    return array('modified_fields');
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field_log";
  }

  static function class_name(){
    return 'FieldLog';
  }
}
