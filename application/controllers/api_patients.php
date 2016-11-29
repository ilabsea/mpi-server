<?php
//name should be ApiPatients but can not find a way to map this to CI router
class Api_patients extends ApiAccessController{

  function index() {
    $params = $_GET;
    $this->render_json($params);
  }

  function skip_authenticate(){
    if($this->router->fetch_method() == "index")
      return true;
    return false;
  }

  //URL: patients/update/$id
  //params = $_POST
  function update($id){
    $this->authorize_read_access();
  }

}
