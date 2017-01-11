<?php
class FingerprintMatcher {
  static function match_fingerprints_with_patients($fingerprint_options, $patients) {
    if(count($fingerprint_options) == 0)
      throw new FingerPrintRequireException("You must provide fingerprint_options to value to this function");

    $result = array();
    foreach($patients as $patient){
      $patient = FingerprintMatcher::match_patient($patient, $fingerprint_options);
      if($patient)
        $result[] = $patient;
    }
    return $result;
  }

  static function first_match_fingerprints_with_patients($fingerprint_options, $patients) {
    if(count($fingerprint_options) == 0)
      throw FingerPrintRequireException("You must provide fingerprint_options to value to this function");

    foreach($patients as $patient){
      $patient = FingerprintMatcher::match_patient($patient, $fingerprint_options);
      if($patient)
        return $patient;
    }
    return false;
  }

  static function match_patient($patient, $fingerprint_options) {
    $sdk = GrFingerService::get_instance();
    foreach($fingerprint_options as $fingerprint_name => $fingerprint_value){

      if(trim($fingerprint_value) == "" || !$sdk->prepare($fingerprint_value))
        continue;

      $fp_patient = is_array($patient) ? $patient[$fingerprint_name] : $patient->$fingerprint_name;

      if(trim($fp_patient) !="" && $sdk->identify($fp_patient)){
        $pat_id = is_array($patient) ?  $patient["pat_id"] : $patient->pat_id;
        return $patient;
      }
    }
    return false;
  }

  static function match_fingerprint_with_patients($fingerprint_name, $fingerprint_value, $patients, &$result){
    if(trim($fingerprint_value) == "")
      return;

    $sdk = GrFingerService::get_instance();

    if(!$sdk->prepare($fingerprint_value))
      return false;

    foreach($patients as $patient) {
      $fp_patient = is_array($patient) ? $patient[$fingerprint_name] : $patient->$fingerprint_name;

      if(trim($fp_patient) !="" && $sdk->identify($fp_patient)){
        $pat_id = is_array($patient) ?  $patient["pat_id"] : $patient->pat_id;
        $result[$pat_id] = $patient;
      }
    }
  }

}
