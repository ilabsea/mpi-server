<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{
  var $display_value;

  function before_action(){
    parent::before_action();
    $scope = $this->oauth->scope;
    $scope = Scope::find(2);
    $this->display_value = new DisplayValue($scope);

    if($this->action_name() != "update_field"){
      $this->require_internal_app();
    }
  }

  function skip_authenticate(){
    return true;
  }

  //POST /api/patients/update_field
  function update_field($pat_id){
    // $params = $_GET;
    $params = $_GET;

    if(!isset($params['name']) || !isset($params['value'])) {
      $errors = array(
        "error" =>"invalid parameter name",
        "error_description" => "you must provide a valid parameter name"
      );
      return $this->render_bad_request($errors);
    }

    $name  = $params['name'];
    $value = $params['value'];

    $field_params = FieldTransformer::apply_to_patient(array($name => $value));

    if(isset($field_params["pat_id"]) || count($field_params) == 0){
      $errors = array(
        "error" =>"invalid parameter name",
        "error_description" => "you must provide a valid parameter name"
      );
      return $this->render_bad_request($errors);
    }

    $application = $this->oauth->application;
    $application = Application::find(2);

    $patient = Patient::update_field($pat_id, $name, $value, $application);
    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    $this->render_json($patient_json);
  }


  function index() {
    $params = $_GET;
    $filter_patients = FieldTransformer::apply_to_all($params);
    $filter_patients = AppHelper::merge_array($filter_patients['visits'], $filter_patients['patients']);
    //$filter_patients['pat_register_site'] = '0101';

    $paginate_patients = PatientModule::search_registersite_priority($filter_patients);
    $paginate_patients->records = $this->display_value->patients($paginate_patients->records);
    $this->render_json($paginate_patients);
  }

  //api/patients/create
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
  function create(){
    $params = $_POST;
    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = PatientModule::enroll($filter_patients);

    if($patient->has_error())
      $this->render_record_errors($patient);
    else{
      $patient_json = $patient->dynamic_value();
      $this->render_json($patient_json);
    }
  }

  //POST api/patients/sync one patient per time
  function sync(){
    $params = $_POST;
    $patient = PatientModule::synchronize($params);

    if($patient->has_error())
      $this->render_record_errors($patient);
    else{
      $patient_json = PatientModule::embed_dynamic_value($patient);
      $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
      $this->render_json($patient_json);
    }
  }

  function show($pat_id){
    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));
    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    $this->render_json($patient_json);
  }


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
  function update($pat_id){
    $params = $_POST;
    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));
    $patient->update_attributes($filter_patients);

    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    return $this->render_json($patient_json);
  }

  function catch_exception($exception) {
    parent::catch_exception();

    $type = get_class($exception);
    if($type == "RecordNotFoundException"){
      $errors = array("error" => "not found", "error_description" > " Pat_id is not found");
      return render_record_not_found($errors);
    }
    else if ($type == "FingerPrintRequireException"){
      $errors = array("error" => "bad request", "error_description" => "Fingerprint params values required");
      return render_bad_request($errors);
    }

  }

}
