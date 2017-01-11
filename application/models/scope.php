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
    // ILog::d("params", $params);
    // ILog::d("mapper", $field_mappers,1,1);

    foreach($params as $param_field_code => $value){
      //virtual field we dont care because FieldTransformer will take care of it.
      if(!isset($field_mappers[$param_field_code]))
         continue;

      $param_field = $field_mappers[$param_field_code];

      if(in_array($param_field->id(), $allow_fields))
        continue;

      if($param_field->is_patient_field() && in_array(Patient::ALL_FIELD, $allow_fields) )
        continue;

      if($param_field->is_visit_field() && in_array(Visit::ALL_FIELD, $allow_fields))
        continue;

      return false;
    }

    return true;
  }

  function has_searchable_access($params){
    return $this->has_fields_access($params, $this->searchable_fields);
  }

  function has_updatable_access($params){
    return $this->has_fields_access($params, $this->updatable_fields);
  }

  function searchable_fields_code_message(){
    return implode(", ", Field::map_field_codes($this->searchable_fields));
  }

  function updatable_fields_code_message(){
    return implode(", ", Field::map_field_codes($this->updatable_fields));
  }


  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('name');
    $this->form_validation->set_rules('name', 'Name', "trim|required|{$code_uniqueness}");
    return true;
  }
}
