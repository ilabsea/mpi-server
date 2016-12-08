<?php
class PatientModule {
  static function fingerprint_name($params){
    foreach(Patient::fingerprint_fields() as $fingerprint_name){
      if(isset($params[$fingerprint_name]))
        return $fingerprint_name;
    }
    return null;
  }
  static function patient_visits($patient_ids) {
    $patient_ids = is_array($patient_ids) ? $patient_ids : array($patient_ids);
    $patient = new Patient();

    $patient->db->select("visit.visit_id AS visitid,
                          visit.pat_id AS patientid,
                          visit.serv_id AS serviceid,
                          visit.site_code AS sitecode,
                          visit.ext_code AS externalcode,
                          visit.ext_code_2 AS externalcode2,
                          visit.visit_date AS visitdate,
                          visit.info,
                          visit.date_create,
                          visit.pat_age AS age,
                          visit.refer_to_vcct,
                          visit.refer_to_oiart,
                          visit.refer_to_std,
                          service.serv_code,
                          site.site_name as sitename");

    $patient->db->from("mpi_visit visit");
    $patient->db->join('mpi_service as service ', 'service.serv_id = visit.serv_id', 'LEFT');
    $patient->db->join('mpi_site as site ', 'site.site_code = visit.site_code', 'LEFT');
    $patient->db->where_in('visit.pat_id', $patient_ids);
    $query = $patient->db->get();
    $rows = array();

    foreach($query->result() as $obj) {
      $patient_id = $obj->patientid;
      if(isset($rows[$patient_id]))
        $rows[$patient_id][] = $obj;
      else
        $rows[$patient_id] = array($obj);
    }
    return $rows;
  }

  # params = {p_is_referral: 10, date_visit: '2015-10-10', fingerprint_r1: 'fpvalu1', sitecode: '0202', pat_gender: 1}
  static function patients($params, $exclude_patient_ids = array()) {
    $conditions = array();

    foreach($params as $name => $value) {
      if(Patient::is_fingerprint_field($name))
        continue;

      if($name == 'pat_gender'){
        $gender_key = "( pat_gender = " . intval($params['pat_gender']) . " OR " . " pat_gender is NULL ) ";
        $conditions[$gender_key] = null;
      }

      else
        $conditions[$name] = $value;
    }

    if(count($exclude_patient_ids) > 0) { //[1,2,4,3,10]
      $pat_ids = implode(",", $exclude_patient_ids);  // "(1,2,4,3,'10')"
      $key = "(pat_id NOT IN ({$pat_ids}))";
      $conditions[$key] = null;
    }

    $patients = Patient::all($conditions);
    return $patients;
  }

  # Filter site code to reduce number of patient
  static function search($params){
    $fingerprint_name = PatientModule::fingerprint_name($params);
    $fingerprint_value = $params[$fingerprint_name];
    $fingerprint_sdk = GrFingerService::instance();
    $fingerprint_sdk->prepare($fingerprint_value);

    $patients = PatientModule::patients($params); //filter sitecode [1,2,3,4,100]
    $result = PatientModule::identify_patients($patients);

    if(count($result) >0)
      return PatientModule::to_patient_json();
    else // should filter without code and except patients list above. return empty for now
      return array();
  }

  static function exclude_previous_patients($patients) {
    $exclude_patient_ids = array();
    foreach($patients as $patient){
      $pat_id = $patient->id();
      $exclude_patient_ids[] = "'{$pat_id}'";
    }
    return $exclude_patient_ids;
  }

  static function identify_patients($patients){
    $result = array();
    $fingerprint_sdk = GrFingerService::instance();

    foreach ($patients as $patient){
      if(!$patient->has_fingerprint($fingerprint_name))
        continue;
      if($fingerprint_sdk->identify($patient->$fingerprint_name))
        $result[] =$patient;
    }
    return $result;
  }

  static function set_patient_last_test(&$patient_json) {
    $last_test_date = "";
    $last_test_result = "";
    foreach($patient_json["visits"] as $visit) {
      if($visit->serviceid != 1)
        continue;

      if($last_test_date == ""){
        $last_test_date = $visit->visitdate;
        $last_test_result = $visit->info;
      }

      else if(strcmp($last_test_date, $visit->visitdate) < 0){
        $last_test_date = $visit->visitdate;
        $last_test_result = $visit->info;
      }
    }
    $patient_json["lastvcctdate"] = $last_test_date;
    $patient_json["lastvcctresult"] = $last_test_result;
  }

  static function to_patient_json($matched_patients) {
    $patient_ids = array();
    foreach($matched_patients as $patient) {
      $patient_ids[] = $patient->id();
    }

    $patient_visits = PatientModule::patient_visits($patient_ids);
    $results = array();
    foreach($matched_patients as $patient) {
      $patient_id = $patient->id();
      $patient_json = $patient->to_json();
      $patient_json['visits'] = $patient_visits[$patient_id];
      PatientModule::set_patient_last_test($patient_json);
      $results[] = $patient_json;
    }
    return $results;
  }

  static function enroll($params){
    $params["date_create"] = isset($params["date_create"]) ? $params["date_create"] : Imodel::current_time();
    $params["pat_register_site"] = isset($params["pat_register_site"]) ? $params["pat_register_site"] : "0201";
    $patient = new Patient($params);

    if($patient->save())
      return $patient;
    else
      return null;
  }

}
