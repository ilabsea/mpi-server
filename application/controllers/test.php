<?php
class Test extends MpiController {
  var $skip_before_action = "*";

  function enroll() {
    $params = array(
      "pat_register_site" => "0202",
      "pat_age" => 30,
      "pat_gender" => 1,
      "is_referred" => false,
      "p_is_referral" => "True",
      "v_tb" => "120",
      "pat_id" => "KH002100003000",
      "p_is_referral" => 1
    );
    $patient = PatientModule::enroll($params);
    ILog::debug_message("Patient", $patient);
    assert($patient, "Patient");
  }
}
