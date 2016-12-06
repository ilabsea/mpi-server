<?php
class Test extends MpiController {
  var $skip_before_action = "*";

  function enroll_patient() {
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
  }
  function create_visit() {
    $params = array(
      "pat_id"=> "KH002100003273", "pat_age"=> "80", "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02"
    );

    $_POST = $params;

    $visit = new Visit($params);

    if($visit->save())
      return $this->render_json($visit->to_array());
    else
      return $this->render_bad_request($visit->get_errors());
  }

}
