<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_visits extends ApiAccessController{
  function index() {
    $response = array("status" => "success", "endpoint" => "api/visits/index");
    $this->render_json($response);
  }

  function create(){
    $params = $this->visit_params();
    $visit = new Visit($params);
    if($visit->save())
      return $this->render_json($visit->to_array());
    else
      return $this->render_bad_request($visit->get_errors());
  }

  function visit_params() {
    $params = $this->filter_params(array(
      "pat_id", "pat_age", "serv_id", "visit_date", "site_code", "ext_code", "ext_code_2", "",
       "refer_to_vcct", "refer_to_oiart", "refer_to_std", "info", "vcctnumber", "vcctsite"
    ));
  }
}
