<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{
  //var $skip_before_action = array('index');

  function index() {
    $response = array("status" => "OK");
    $this->render_json($response);
  }
}
