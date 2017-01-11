<?php
class PatientModule {
  static $errors = null;

  static function embed_dynamic_value($patient){
    $visits = Patient::visits(["'{$patient->pat_id}'"], "visit_id DESC");

    $dynamic_value = new DynamicValue();
    $dynamic_visits =  $dynamic_value->result($visits, "visit");

    $patient_json = array("patient" => $patient->dynamic_value());
    $patient_json["patient"]["list_visits"] = $dynamic_visits;
    return $patient_json;
  }

  static function patient_visits($pat_ids, $conditions = array()) {
    $pat_ids = is_array($pat_ids) ? $pat_ids : array($pat_ids);
    $pat_ids = implode(",", $pat_ids);
    $active_record = new AppModel();

    $active_record->db->select("visit.visit_id,
                          visit.pat_id,
                          visit.serv_id,
                          visit.site_code,
                          visit.ext_code,
                          visit.ext_code_2,
                          visit.visit_date,
                          visit.info,
                          visit.date_create,
                          visit.pat_age,
                          visit.refer_to_vcct,
                          visit.refer_to_oiart,
                          visit.refer_to_std,
                          service.serv_code,
                          site.site_name");

    $active_record->db->from("mpi_visit visit");
    $active_record->db->join('mpi_service as service ', 'service.serv_id = visit.serv_id', 'LEFT');
    $active_record->db->join('mpi_site as site ', 'site.site_code = visit.site_code', 'LEFT');
    $active_record->db->where("visit.pat_id IN ({$pat_ids})", null );

    foreach($conditions as $key => $value)
      $active_record->db->where($key, $value);

    $active_record->db->order_by("visit.visit_id", "DESC");
    $active_record->db->limit(10);


    $query = $active_record->db->get();

    $query_results = $query->result();
    $dynamic_value = new DynamicValue();
    $dynamic_results = $dynamic_value->result($query_results, 'visit');

    $rows = array();
    foreach($dynamic_results as $dynamic_result) {
      $pat_id = $dynamic_result['pat_id'];
      if(isset($rows[$pat_id]))
        $rows[$pat_id][] = $dynamic_result;
      else
        $rows[$pat_id] = array($dynamic_result);
    }
    return $rows;
  }

  static function search_registersite_priority($params){

    $search_with_site_code = $params;
    $site_code = isset($params['pat_register_site']) ? $params['pat_register_site'] : '0202';

    $paginate_patients = PatientModule::search($search_with_site_code);
    if(count($paginate_patients->records) == 0) {
      $search_without_site_code = $search_with_site_code;
      unset($search_without_site_code['pat_register_site']);
      $excludes = array("patient.pat_register_site" => $site_code);
      $paginate_patients = PatientModule::search($search_without_site_code, $excludes);
    }
    return $paginate_patients;
  }
  # Filter site code to reduce number of patient
  //pat_register_site > 0201
  private static function search($params, $excludes=array()){
    $params = Patient::allow_query_fields($params);
    $fingerprint_params = Patient::fingerprint_params($params, true);

    Patient::$select_fields = " patient.id, patient.pat_id, patient.pat_gender, patient.pat_age, patient.pat_dob,
                                patient.date_create, patient.pat_register_site, patient.visits_count,
                                patient.visit_positives_count,patient.new_pat_id";
    foreach($fingerprint_params as $fp_name =>$fp_value){
      Patient::$select_fields =  Patient::$select_fields . "," . "patient.$fp_name";
    }

    $active_record = new Patient();
    $active_record->db->select(Patient::$select_fields);

    Patient::build_conditions($params, $active_record);
    Patient::build_excludes_conditions($excludes);

    $active_record = Patient::where_filter($active_record);
    $query = $active_record->db->get();
    $active_record = null;
    $patients = $query->result();

    $patients = FingerprintMatcher::match_fingerprints_with_patients($fingerprint_params, $patients);
    $total_counts = count($patients);

    $visit_conditions = Patient::$conditions["visits"];
    $patients = PatientModule::embeded_dynamic_fields($patients, $visit_conditions);
    $paginator = new Paginator($total_counts, $patients);
    return $paginator;
  }

  static function embeded_dynamic_fields($patients, $visit_conditions=array()) {
    $dynamic_value = new DynamicValue();
    $dynamic_results = $dynamic_value->result($patients, "patient");
    $patients_json = PatientModule::to_patient_json($dynamic_results, $visit_conditions);
    return $patients_json;
  }

  static function set_patient_last_test(&$patient_json) {
    $last_test_date = "";
    $last_test_result = "";

    foreach($patient_json["list_visits"] as $visit) {
      $is_vcct = $visit["serv_id"] == Visit::VCCT;

      if($is_vcct && $last_test_date == ""){
        $last_test_date = $visit["visit_date"];
        $last_test_result = $visit["info"];
      }

      else if($is_vcct && strcmp($last_test_date, $visit["visit_date"]) < 0){
        $last_test_date = $visit["visit_date"];
        $last_test_result = $visit["info"];
      }
    }
    $patient_json["lastvcctdate"] = $last_test_date;
    $patient_json["lastvcctresult"] = $last_test_result;
  }

  static function to_patient_json($matched_patients, $visit_conditions=array()) {
    $pat_ids = array();
    foreach($matched_patients as $patient) {
      $pat_id = $patient['pat_id'];
      $pat_ids[] = "'{$pat_id}'";
    }

    $patient_visits = count($pat_ids) ? PatientModule::patient_visits($pat_ids, $visit_conditions) : array();

    $results = array();
    foreach($matched_patients as $patient) {
      $pat_id = $patient['pat_id'];

      $patient_json = $patient; //PatientModule::to_json($patient);

      $patient_json['list_visits'] = isset($patient_visits[$pat_id]) ? $patient_visits[$pat_id] : array();
      PatientModule::set_patient_last_test($patient_json);
      $results[] = $patient_json;
    }
    return $results;
  }

  static function to_json($patient){
    return array(
      "patientid" => $patient->pat_id,
      "gender" => $patient->pat_gender,
      "birthdate" => $patient->pat_dob,
      "sitecode" => $patient->pat_register_site,
      "createdate" => $patient->date_create,
      "age" => $patient->pat_age
    );
  }

  static function enroll($params){
    if(!AppHelper::present($params, "date_create"))
      $params["date_create"] = Imodel::current_time();
    if(!AppHelper::present($params, "pat_register_site"))
      $params["pat_register_site"] = "0201";

    $patient = new Patient($params);
    $patient->save();
    return $patient;
  }

  //array("pat_gender", "pat_age", "pat_dob", "pat_register_site", "date_create", "finger...", "visits" => array())
  function synchronize($params) {
    $patient_params = FieldTransformer::apply_to_patient($params);
    $data = array();
    $fingerprint_params = Patient::fingerprint_params($patient_params);

    if(!AppHelper::present($patient_params, "pat_register_site"))
      $patient_params["pat_register_site"] = "0201";

    $patient = Patient::find_by(array("pat_id" => $patient_params["pat_id"]));
    if($patient)
      return true;

    $sdk = GrFingerService::get_instance();
    $conditions = array("pat_gender" => $patient_params["pat_gender"],
                        "pat_register_site" => $patient_params["pat_register_site"]);

    foreach($fingerprint_params as $fingerprint_name => $fingerprint_value)
      $conditions[$fingerprint_name] = $fingerprint_value;

    $patients = Patient::all_filter($conditions);
    $fingerprint_options = Patient::fingerprint_params($patient_params);

    $patient = false;
    $patient = FingerprintMatcher::first_match_fingerprints_with_patients($fingerprint_options , $patients);

    //$active_record = new Patient();
    //$active_record->db->trans_begin();

    if (!$patient){
      $patient = new Patient($patient_params);
      if(!$patient->save()){
        PatientModule::$errors = $patient->get_errors();
        return false;
      }
    }

    if(isset($params['visits'])){
      // array("serv_id", "site_code", "ext_code", "pat_age", "ext_code_2", "info", "refer_to_vcct", "refer_to_oiart", "refer_to_std", "date_create", "visit_date")
      foreach($params["visits"] as $visit_params){
        $filter_visit_params  =  FieldTransformer::apply_to_visit($visit_params);
        $visit_params["pat_id"] = $patient->pat_id;
        $visit = new Visit($visit_params);
        if(!$visit->save()){
          PatientModule::$errors = $patient->get_errors();
          //$active_record->db->trans_rollback();
          return false;
        }
      }
    }
    //$active_record->db->trans_commit();
    return $patient;
  }

  //array("pat_id", "visits" => array())
  static function synchronize_update($patient_params) {
    $patient = Patient::find_by(array("pat_id" => $patient_params["pat_id"]));
    if(!$patient)
      return false;

    foreach($patient_params["visits"] as $visit_params){
      $visit_params["pat_id"] = $patient->pat_id;
      $visit = new Visit($visit_params);
      $visit->save();
    }
    $patients_json =  PatientModule::embeded_dynamic_fields($patients);
    return $patients_json;
  }
}
