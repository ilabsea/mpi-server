<?php
class Test extends MpiController {
  var $skip_before_action = "*";

  function enroll_patient() {
    $params = array(
      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "v_tb" => "1111",
      "p_dynamic_field1" => "10",
      "p_dynamic_field2" => "30",
      "pat_id" => "KH002100003000", //override
    );
    $patient = PatientModule::enroll($params);
    ILog::debug_message("Patient", $patient);
  }


  function enroll_visit() {
    $field = new Field(array("type" => "DateTime"));

    $params = array(
      "pat_id"=> "KH002100003273",

      "pat_age"=> "80", "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",

    );

    $_POST = $params;

    $visit = new Visit($params);

    if($visit->save())
      return $this->render_json($visit->to_array());
    else
      return $this->render_bad_request($visit->get_errors());
  }

}
