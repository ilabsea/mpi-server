<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{
  function skip_authenticate(){
    if($this->router->fetch_method() == "index")
      return true;
    return true;
  }

  function index() {
    $params = $_GET;
    $patients = PatientModule::search($params);
    $this->render_json($patients);
  }

  function create(){
    $params = $this->patient_params();
    $patient = PatientModule::enroll($params);
    $this->render_json($patient);
  }

  function update($id){
    $this->authorize_read_access();
  }

  function patient_params() {
    $param_list = array_merge(Patient::fingerprint_fields(),
                              array( "pat_gender", "pat_dob", "pat_age", "date_create", "pat_register_site","new_pat_id", "pat_version"));
    return $this->filter_params($param_list);
  }
}
