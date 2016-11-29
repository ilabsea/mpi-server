<?php
class ApiAccessController extends ApiController {

  function before_action() {
    parent::before_action();
    $access_token = $this->access_token();

    if(!$this->oauth->authenticate_token($access_token)) {
      return $this->render_unauthorized($this->oauth->errors);
      exit;
    }
  }

  function after_action($status){
    parent::after_action($status);
    $this->access_log($status);
  }

  function access_log($status){
    $request_type = $_SERVER['REQUEST_METHOD'];
    if($request_type == 'POST')
      $params = $_POST;
    else if($request_type == 'GET')
      $params = $_GET;

    $api_access_log = new ApiAccessLog();

    $attrs = array(
      "application_id" => $this->oauth->application->id(),
      "application_name" => $this->oauth->application->name,
      "ip" => $this->oauth->ip_address(),
      "status" => $status,
      "params" => $params,
      "http_verb" => $request_type,
      "action" => $this->router->fetch_class(). "/".$this->router->fetch_method(),
      "url" => $_SERVER['REQUEST_URI']
    );

    $api_access_log->set_attributes($attrs);
    $api_access_log->save();
  }

  function access_token(){
    return isset($_SERVER["HTTP_TOKEN"]) ? $_SERVER["HTTP_TOKEN"] : null;
  }

  function authorize_write_access(){
    $params = $_POST;
    if(!$this->oauth->scope->has_write_access($params)){
      $allow_fields = implode(", ", $this->oauth->scope->writeable_fields_code());
      $errors = array(
        "error" => 'Unauthorized',
        "error_description" => "You can only access these fields: ({$allow_fields})"
      );
      return $this->render_unauthorized($errors);
      exit;
    }
    return true;
  }

  function authorize_read_access(){
    $params = $_GET;
    if(!$this->oauth->scope->has_read_access($params)){
      $allow_fields = implode(", ", $this->oauth->scope->readable_fields_code());
      $errors = array(
        "error" => 'Unauthorized',
        "error_description" => "You can only access these fields: ({$allow_fields})"
      );
      return $this->render_unauthorized($errors);
      exit;
    }
    return true;
  }
}
