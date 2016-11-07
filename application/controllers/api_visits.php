<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_visits extends ApiAccessController{
  function index() {
    $response = array("status" => "success", "endpoint" => "api/visits/index");
    $this->render_json($response);
  }

  function create(){
    $visit = Visit::find(27);
    $visit->set_attribute("info", "positive");
    $visit->update();

    $visit = new Visit();
    $attrs = array( "pat_id" => "KH002100000001",
                    "serv_id" => 2,
                    "site_code" => "0202",
                    "ext_code" => "003690",
                    "ext_code_2" => "020202069",
                    "visit_date" => $visit->current_date(),
                    "date_create" => $visit->current_time(),
                    "refer_to_vcct" => 1,
                    "refer_to_oiart" => 0,
                    "refer_to_std" => 1,
                    "info" => "Positive");

    $visit->set_attributes($attrs);
    $result = $visit->save();
    $this->render_json($visit);
  }
}
