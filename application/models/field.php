<?php
/**
* @autoloaded
*/
class Field extends Imodel {
  var $id = null;
  var $name = '';
  var $code = '';
  var $is_encrypted = 0;
  var $type = "";
  var $soft_delete = false;
  var $created_at = null;
  var $updated_at = null;

  static function types() {
    return array(
      "Boolean" => "Boolean",
      "String" => "String",
      "Integer" => "Integer",
      "Float" => "Float"
    );
  }

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field";
  }

  static function class_name(){
    return 'Field';
  }

  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('code');
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('code', 'Code', "trim|required|{$code_uniqueness}");
    $this->form_validation->set_rules('type', 'Type', 'required');
  }
}
