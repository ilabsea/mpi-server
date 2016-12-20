<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{
  function index() {
    $params = $_GET;
    $filter_patients = FieldTransformer::apply_to_all($params, true);
    $patients = PatientModule::search($filter_patients);
    $this->render_json($patients);
  }

  //api/patients/create
  function create(){
    $params = $_POST;
    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = PatientModule::enroll($filter_patients);
    $this->render_json($patient);
  }

  function show($pat_id){
    $patient_json = PatientModule::search_by_pat_id($pat_id);
    $this->render_json($patient_json);
  }

  function update($pat_id){
    $params = $_POST;

    //mock test
    // $params = array(
    //   "p_pat_register_site" => "0101",
    //   "p_pat_age" => 30,
    //   "p_pat_gender" => 1,
    //   "p_is_referred" => false,
    //
    //   "p_is_referral" => "False",
    //   "p_dynamic_field1" => "1000",
    //   "p_dynamic_field2" => "70",
    //   "p_Fdafdafdafds" => "300"
    // );
    //
    // $pat_id = "KH001100000001";

    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));

    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient->update_attributes($filter_patients);

    $patient_json = PatientModule::search_by_pat_id($pat_id);
    return $this->render_json($patient_json);
  }

}
