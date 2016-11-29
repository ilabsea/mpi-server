<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{

  function index() {
    $response = array("status" => "OK");
    $this->render_json($response);
  }

  //URL: patients/update/$id
  //params = $_POST
  function update($id){
    $this->authorize_read_access();
  }

}
