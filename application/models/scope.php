<?php
class Scope extends Imodel {
  var $id = null;
  var $name = '';
  var $searchable_fields = array();
  var $updatable_fields = array();

  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function serialize_fields() {
    return array('searchable_fields', 'updatable_fields');
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "scope";
  }

  static function class_name(){
    return 'Scope';
  }

  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('name');
    $this->form_validation->set_rules('name', 'Name', "trim|required|{$code_uniqueness}");
  }
}
