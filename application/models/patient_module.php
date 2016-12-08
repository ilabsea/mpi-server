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

  }

  # Filter site code to reduce number of patient
  static function search($params){
    $fingerprint_name = PatientModule::fingerprint_name($patient_params);
    $fingerprint_value = $params[$fingerprint_name];

    $patients = Patient::all_filter($params);
    if($fingerprint_value)
      return PatientModule::identify_patients($patients, $fingerprint_name, $fingerprint_value);
    return $result;
  }

  static function identify_patients($patients, $fingerprint_name, $fingerprint_value){
    $result = array();
    $fingerprint_sdk = GrFingerService::instance();
    $fingerprint_sdk->prepare($fingerprint_value);

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
