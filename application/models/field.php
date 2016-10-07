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

  function validation_rules(){
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[field.code]');
    $this->form_validation->set_rules('type', 'Type', 'required');
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field";
  }

  static function class_name(){
    return Field;
  }
}
