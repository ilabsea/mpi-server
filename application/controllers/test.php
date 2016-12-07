<?php
class Test extends MpiController {
  var $skip_before_action = "*";

  function console(){

    $attrs = array("member_login" => "yourins", "site_code" => "0202", "member_pwd" => "123456");
    // $member = new Member($attrs);
    // $member->save();
    // ILog::debug_message("errors", $member->get_errors());

    $member = Member::find(30);
    $member = new Member($attrs);
    $member->set_attributes($attrs);
    $member->save();

    // $member->update_attributes($attrs);

    ILog::debug_message("errors", $member->get_errors(), 1, 1);
  }

  function patients() {
    $ids = array( 3683,3684,3685,3686,3687 );
    $records = Patient::all(array("id" => $ids));
    $dynamic_value = new DynamicValue($records);
    $result = $dynamic_value->result();
    ILog::debug_message("Patients", $result);

    $ids = array(2961,2962,2963,2964,2965,2966 );
    $records = Visit::all(array("visit_id" => $ids));
    $dynamic_value = new DynamicValue($records);
    $result = $dynamic_value->result();
    ILog::debug_message("Visits", $result, 1, 1);

  }

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

  //(patient_id => "", patient: json_format}
  //jsonformat = array("visits" => array(visit, ...))
  // serv_id, site_code, ext_code, ext_code_2, info, age, refer_to_vcct, refer_to_oiart
  // refer_to_std, date_create, visit_date
  function sync_update(){
    $visit = array(
      "pat_age"=> 30, "serv_id"=> "2", "visit_date"=> "2016-12-06",
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
    $pat_id = "KH001100000001";
    $visit_params = array();
    $services = array(1,2,3);

    $patient = Patient::find_by(array("pat_id" => $pat_id));
    if(!$patient)
      return false;

    for($i=0; $i< 5; $i++) {
      shuffle($services);
      $visit["serv_id"] = $services[0];
      $visit["pat_age"] = $visit["pat_age"] + $i * 2;
      $visit["pat_id"] = $pat_id;
      $visit_params[] = $visit;
    }
    Visit::bulk_insert($visit_params);  //raise rollback_exception
    $visits = Visit::all(array("pat_id" => $pat_id));
    $result = array("pat_id" => $pat_id, "visits" => array());
    foreach($visits as $visit) {
      $result["visits"][] = (new VisitDetailSerializer($visit))->attributes();
    }
    return $this->render_json($result);
  }

}
