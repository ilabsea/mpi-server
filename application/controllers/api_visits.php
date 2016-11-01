<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_visits extends ApiAccessController{
  function index() {
    $response = array("status" => "visits/index");
    $this->render_json($response);
  }


}
