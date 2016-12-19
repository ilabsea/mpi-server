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

  //api/patients/create
  function create(){
    $params = $this->patient_params();
    $patient = PatientModule::enroll($params);
    $this->render_json($patient);
  }

  function update($pat_id){
    $params = $_POST;
    $patient = Patient::find_by(array("pat_id" => $pat_id));

    if(!$patient)
      throw new RecordNotFoundException("Invalid patient number: {$pat_id}");

    $filter_patients = FieldTransformer::apply_to_patient($params);
    $patient->update_attributes($filter_patients);

    $visits = Patient::visits(array("'{$pat_id}'"));

    $dynamic_value = new DynamicValue();

    $dynamic_patients = $dynamic_value->result(array($patient));
    $dynamic_visits =  $dynamic_value->result($visits, null);
    $dynamic_fields = Field::dynamic_fields();

    $patient_json = array();
    $patient_json['patient'] = $dynamic_patients[0];
    $patient_json['patient']["visit_lists"] = $dynamic_visits;

    return $this->render_json($patient_json);
  }

  function catch_exception($exception) {
    $type = get_class($exception);
    if($type == 'RecordNotFoundException')
      return $this->render_bad_request(array("error"=>404,
                                             "error_description" => $exception->getMessage()));
  }

  function patient_params() {
    $param_list = array_merge(Patient::fingerprint_fields(),
                              array( "pat_gender", "pat_dob", "pat_age", "date_create",
                                     "pat_register_site","new_pat_id", "pat_version"));
    return $this->filter_params($param_list);
  }
}
