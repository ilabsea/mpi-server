<?php
//name should be ApiOauth but can not find a way to map this to CI router
class Api_oauth extends ApiController {

  function before_action() {
    parent::before_action();

    $this->load->model("application");
    $this->load->model("scope");
  }

  function token(){
    $params = $_POST;
    if($this->oauth->strategy_token($params)){
      $this->access_log(200, "Authorized and issue access token");
      return $this->render_json($this->oauth->application_token->to_json());
    }
    $this->access_log(401, "Unauthorized: " . $this->oauth->error_message());
    return $this->render_unauthorized($this->oauth->errors);
  }

  function access_log($status, $description){
    $params =$_POST;
    $api_access_log = new ApiAccessLog();

    $attrs = array(
      "ip" => $this->oauth->ip_address(),
      "status" => $status,
      "status_description" => $description,
      "params" => $params,
      "http_verb" => "POST",
      "action" => $this->router->fetch_class(). "/".$this->router->fetch_method(),
      "url" => $_SERVER['REQUEST_URI']
    );

    if($this->oauth->application){
      $attrs["application_id"] = $this->oauth->application->id();
      $attrs["application_name"] = $this->oauth->application->name;
    }

    $api_access_log->set_attributes($attrs);
    $api_access_log->save();
  }
}
