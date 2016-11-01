<?php
class FieldValue extends Imodel {
  var $id = null;
  var $field_id = null;
  var $field_type = null;

  var $field_owner_id = null;
  var $field_owner_type = null;

  var $value = null;

  var $created_at = null;
  var $updated_at = null;


  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field_value";
  }

  static function class_name(){
    return 'FieldValue';
  }
}
