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

  //  params = array("v_visit"=> "100", "v_visit_id" => "200");
  //  $allow_fields = array(field_id1, field_id2)
  function has_fields_access($params, $allow_fields){
    $field_mappers = Field::map_by_code();

    foreach($params as $param_field_code => $value){
      $param_field = $field_mappers[$param_field_code];
      if(in_array($param_field->id(), $allow_fields))
        continue;

      if($param_field->is_patient_field() && isset($allow_fields['patient.*']) )
        continue;

      if($param_field->is_visit_field() && isset($allow_fields['visit.*']))
        continue;

      return false;
    }

    return true;
  }

  function display_field_codes(){
    $field_mappers = Field::mapper();
    $result = array();

    foreach($this->display_fields as $field_id){
      $field_code = $field_mappers[$field_id];
      $result[$field_code] = $field_code;
    }

    return $result;
  }

  function has_searchable_access($params){
    return $this->has_fields_access($params, $this->searchable_fields);
  }

  function has_updatable_access($params){
    return $this->has_fields_access($params, $this->updatable_fields);
  }

  function searchable_fields_code_message(){
    return implode(", ", Field::map_file_codes($this->searchable_fields));
  }

  function updatable_fields_code_message(){
    return implode(", ", Field::map_file_codes($this->updatable_fields));
  }


  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('name');
    $this->form_validation->set_rules('name', 'Name', "trim|required|{$code_uniqueness}");
    return true;
  }
}
