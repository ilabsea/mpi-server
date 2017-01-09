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
      if(get_class($patient) != "Patient"){
        $patient_ar = new Patient();
        $patient = $patient_ar->copy_object($patient);
      }

      if($patient->has_fingerprint($fingerprint_name) && $sdk->identify($patient->$fingerprint_name))
        $result[$patient->pat_id] = $patient;
    }
  }
}
