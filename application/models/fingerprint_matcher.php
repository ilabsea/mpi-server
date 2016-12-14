<?php
class FingerprintMatcher {
  static function match_fingerprints_with_patients($fingerprint_options, $patients) {
    if(count($fingerprint_options) == 0)
      return $patients;

    $result = array();
    foreach($fingerprint_options as $fingerprint_name => $fingerprint_value)
      $this->match_fingerprint_with_patients($fingerprint_name, $fingerprint_value , $patients, $result);

    return $result;
  }

  static function match_fingerprint_with_patients($fingerprint_name, $fingerprint_value, $patients, &$result){
    $sdk = GrFingerService::get_instance();
    $sdk->prepare($fingerprint_value);

    foreach($patients as $patient) {
      if($patient->has_fingerprint($fingerprint_name) && $sdk->identify($patient->$fingerprint_name))
        $result[$patient->pat_id] = $patient;
    }
  }
}
