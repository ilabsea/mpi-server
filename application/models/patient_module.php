<?php
class PatientModule {
  static function patient_visits($pat_ids) {
    $pat_ids = is_array($pat_ids) ? $pat_ids : array($pat_ids);
    $pat_ids = implode(",", $pat_ids);
    $patient = new Patient();
    $patient->db->select("visit.visit_id,
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

    $patient->db->from("mpi_visit visit");
    $patient->db->join('mpi_service as service ', 'service.serv_id = visit.serv_id', 'LEFT');
    $patient->db->join('mpi_site as site ', 'site.site_code = visit.site_code', 'LEFT');
    $patient->db->where("visit.pat_id IN ({$pat_ids})", null );
    $query = $patient->db->get();

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
  # Filter site code to reduce number of patient
  static function search($params, $exclude_patient_ids = array()){
    $fingerprint_options = array();
    foreach($params as $field_name => $field_value) {
      if(Patient::is_fingerprint_field($field_name)){
        $fingerprint_options = array("name" => $field_name, "value" => $field_value);
        break;
      }
    }
    $patients = Patient::all_filter($params, $exclude_patient_ids);
    if(count($fingerprint_options) > 0)
      $patients = FingerprintMatcher::patients($fingerprint_options, $patients);

    $dynamic_value = new DynamicValue();
    $dynamic_results = $dynamic_value->result($patients, "patient");

    $patient_jsons = PatientModule::to_patient_json($dynamic_results);
    return $patient_jsons;
  }

  static function set_patient_last_test(&$patient_json) {
    $last_test_date = "";
    $last_test_result = "";

    foreach($patient_json["visits"] as $visit) {
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

  static function to_patient_json($matched_patients) {
    $pat_ids = array();
    foreach($matched_patients as $patient) {
      $pat_id = $patient['pat_id'];
      $pat_ids[] = "'{$pat_id}'";
    }

    $patient_visits = PatientModule::patient_visits($pat_ids);

    $results = array();
    foreach($matched_patients as $patient) {
      $pat_id = $patient['pat_id'];

      $patient_json = $patient; //PatientModule::to_json($patient);

      $patient_json['visits'] = isset($patient_visits[$pat_id]) ? $patient_visits[$pat_id] : array();
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
    $params["date_create"] = $params["date_create"] ? $params["date_create"] : Imodel::current_time();
    $params["pat_register_site"] = $params["pat_register_site"] ? $params["pat_register_site"] : "0201";
    $patient = new Patient($params);

    if($patient->save())
      return $patient;
    else
      return null;
  }

}
