<?php
class Scope extends Imodel {
  var $id = null;
  var $name = '';

  var $searchable_fields = array();
  var $display_fields    = array();
  var $updatable_fields  = array();

  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function serialize_fields() {
    return array('searchable_fields', 'display_fields', 'updatable_fields');
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

  static function mapper() {
    $scopes = Scope::all();
    $result = [];
    foreach($scopes as $scope)
      $result[$scope->id] = $scope->name;
    return $result;
  }

  function has_read_access($params){

    $keys = array_keys($params);
    $keys[] = -1;
    $fields = Field::all(array("code" => $keys));

    $readable_fields = $this->readable_fields();

    foreach($fields as $field) {
      if(isset($readable_fields[$field->id]))
        continue;

      if($field->is_patient_field() && isset($readable_fields['patient.*']) )
        continue;

      if($field->is_visit_field() && isset($readable_fields['visit.*']))
        continue;
      return false;
    }

    return true;
  }

  function readable_fields_code(){
    $fields = Field::all(array("id" => $this->searchable_fields));
    $result = array();
    foreach($fields as $field)
      $result[] = $field->code;
    return $result;
  }

  function writeable_fields_code(){
    $fields = Field::all(array("id" => $this->updatable_fields));

    $result = array();
    foreach($fields as $field)
      $result[] = $field->code;
    return $result;
  }

  function has_write_access($params){
    $keys = array_keys($params);
    $fields = Field::all(array("code" => $keys));

    $writeable_fields = $this->writeable_fields();

    foreach($fields as $field) {
      if(isset($writeable_fields[$field->id]))
        continue;

      if($field->is_patient_field() && isset($writeable_fields['patient.*']) )
        continue;

      if($field->is_visit_field() && isset($writeable_fields['visit.*']))
        continue;
      return false;
    }

    return true;
  }

  function readable_fields(){
    $result = array();
    foreach($this->searchable_fields as $field){
      $result[$field] = $field;
    }
    return $result;
  }

  function writeable_fields(){
    $result = array();
    foreach($this->updatable_fields as $field){
      $result[$field] = $field;
    }
    return $result;
  }

  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('name');
    $this->form_validation->set_rules('name', 'Name', "trim|required|{$code_uniqueness}");
    return true;
  }
}
