<?php
class FingerprintMatcher {
  static function match_fingerprints_with_patients($fingerprint_options, $patients) {
    if(count($fingerprint_options) == 0)
      return $patients;

    $result = array();
    foreach($fingerprint_options as $fingerprint_name => $fingerprint_value)
      FingerprintMatcher::match_fingerprint_with_patients($fingerprint_name, $fingerprint_value , $patients, $result);
    return $result;
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
