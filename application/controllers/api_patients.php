<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{
  var $display_value;

  function before_action(){
    parent::before_action();
    $this->display_value = new DisplayValue($this->oauth->scope);
  }


  function index() {
    $params = $_GET;
    $filter_patients = FieldTransformer::apply_to_all($params, true);
    $paginate_patients = PatientModule::search($filter_patients);

    $paginate_patients->records = $this->display_value->patients($paginate_patients->records);
    $this->render_json($paginate_patients);
  }

  //api/patients/create
  function create(){
    $params = $_POST;
    //mock
    // $params = array(
    //   "p_pat_register_site" => "0101",
    //   "p_pat_age" => 60,
    //   "p_pat_gender" => 1,
    //   "p_is_referred" => false,
    //   "p_pat_dob" => "2012-10-10",
    //
    //   "dyn_full_name_p" => "Nisay",
    //   "p_is_referral" => "False",
    //   "v_tb" => "1111",
    //   "p_dynamic_field1" => "10",
    //   "p_dynamic_field2" => "30",
    //   "p_pat_id" => "KH002100003000", //override
    // );

    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = PatientModule::enroll($filter_patients);

    if($patient->has_error())
      $this->render_record_errors($patient);
    else{
      $patient_json = $patient->dynamic_value();
      $this->render_json($patient_json);
    }
  }

  function sync(){
    $params = $_POST;
    
    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = PatientModule::synchronize($filter_patients);

    if($patient->has_error())
      $this->render_record_errors($patient);
    else{
      $patient_json = $patient->dynamic_value();
      $this->render_json($patient_json);
    }
  }

  function show($pat_id){
    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));
    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    $this->render_json($patient_json);
  }

  function update($pat_id){
    $params = $_POST;

    //mock test
    // $params = array(
    //   "p_pat_register_site" => "0101",
    //   "p_pat_age" => 40,
    //   "p_pat_gender" => 1,
    //   "p_is_referred" => false,
    //
    //   "p_is_referral" => "False",
    //   "p_dynamic_field1" => "1000",
    //   "p_dynamic_field2" => "70",
    //   "p_Fdafdafdafds" => "300",
    //   "dyn_full_name_p" => "Updated Nisay"
    // );
    //
    // $pat_id = "KH001100000001";

    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));
    $patient->update_attributes($filter_patients);

    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    return $this->render_json($patient_json);
  }

}
