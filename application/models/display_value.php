<?php
class DisplayValue {

  var $scope = null;
  var $allow_field_names = array("patients" => array(), "visits"=> array());

  function __construct($scope){
    $this->scope = $scope;
    $this->set_allow_fields();
  }

  /*
  [patients] => array (pat_id,.., p_dynamic_field2 )
  [visits] => Array ( visit_id, pat_id,  v_dynamic_field6 )
  */
  function set_allow_fields(){

    $patient_fields = array();
    $visit_fields = array();

    $fields = Field::cache_all();
    $mapper_fields = array();
    foreach($fields as $field) {
      $mapper_fields[$field->id()] = $field;
    }

    foreach($this->scope->display_fields as $field_id) {
      if($field_id == Patient::ALL_FIELD || $field_id == Visit::ALL_FIELD)
        continue;
      $display_field = $mapper_fields[$field_id];

      if($display_field->is_patient_field())
        $patient_fields[] = $display_field->internal_code();
      else
        $visit_fields[] = $display_field->internal_code();
    }
    $this->allow_field_names =  array("patients" => $patient_fields , "visits" => $visit_fields);
  }

  //array(fieldid1, ..., fieldidn)
  function allow_all_patient_fields(){
    return in_array(Patient::ALL_FIELD, $this->scope->display_fields);
  }

  function allow_all_visit_fields() {
    return in_array(Visit::ALL_FIELD, $this->scope->display_fields);
  }

  function patients($patients=array()){
    foreach($patients as &$patient)
      $patient = $this->patient($patient);
    return $patients;
  }

  function patient($patient){
    $result_patient = array();

    if(!$this->allow_all_patient_fields()){
      $patient_field_names = $this->allow_field_names["patients"];
      $result_patient = AppHelper::slice_array($patient, $patient_field_names);
    }
    else
      $result_patient = $patient;

    if(!isset($patient["list_visits"]))
      return $result_patient;

    if(!$this->allow_all_visit_fields()){
      $visits = $this->visits($patient["list_visits"]);
      $result_patient['list_visits'] = $visits;
    }
    else {
      $result_patient['list_visits'] = $patient["list_visits"];
    }

    return $result_patient;
  }

  function visits($visits) {
    foreach($visits as &$visit)
      $visit = $this->visit($visit);

    return $visits;
  }

  function visit($visit) {
    if(!$this->allow_all_visit_fields()){
      $visit_field_names = $this->allow_field_names["visits"];
      $visit = AppHelper::slice_array($visit, $visit_field_names);
    }
    return $visit;
  }
}
