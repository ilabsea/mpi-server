<?php
class ApiAccessController extends ApiController {

  function before_action() {
    parent::before_action();

    if($this->skip_authenticate()){
      $this->restrict_field_access();
      return true;
    }

    $access_token = $this->access_token();
    if(!$this->oauth->authenticate_token($access_token)) {
      $this->access_log(401, "Unauthorized: ".$this->oauth->error_message());
      return $this->render_unauthorized($this->oauth->errors);
      exit;
    }
    $this->restrict_field_access();
  }

  function require_internal_app(){
    if($this->oauth->application->is_internal_app())
      return ;

    $errors = array("error"=>401,
                    "error_description" => "Unauthorized: you are not allow to acces this endpoint");
    return $this->render_unauthorized($errors);
  }

  function restrict_field_access(){
    if($this->skip_restrict_field_access())
      return;

    //$this->oauth->scope = Scope::find(2);

    $action = strtolower($this->router->fetch_method());
    if($action == "index" || $action == "show")
      $this->authorize_searchable_access();
    else if ($action == "udpate" || $action == "create")
      $this->authorize_updatable_access();

  }


  function skip_restrict_field_access() {
    return false;
  }

  function skip_authenticate(){
    return false;
  }

  function after_action($status){
    parent::after_action($status);
    $this->access_log($status);
  }

  function access_log($status, $description=''){
    $params = AppHelper::is_post_request() ? $_POST : $_GET;
    $api_access_log = new ApiAccessLog();

    $attrs = array( "ip" => $this->oauth->ip_address(),
                    "status" => $status,
                    "status_description" => $description,
                    "params" => $params,
                    "http_verb" => AppHelper::request_type(),
                    "url" => $_SERVER['REQUEST_URI'],
                    "action" => $this->router->fetch_class(). "/".$this->router->fetch_method(),
    );

    if($this->oauth->application){
      $attrs["application_id"] = $this->oauth->application->id();
      $attrs["application_name"] = $this->oauth->application->name;
    }
    else{
      $attrs["application_id"] = 0;
      $attrs["application_name"] = "unknown";
    }

    $api_access_log->set_attributes($attrs);
    $api_access_log->save();
  }

  function access_token(){
    log_message("info", "Token is: " . $_SERVER['HTTP_TOKEN'] );
    // log_message("info", "Server params: " . json_encode($_SERVER));
    return isset($_SERVER["HTTP_TOKEN"]) ? $_SERVER["HTTP_TOKEN"] : null;
  }

  function authorize_updatable_access(){
    $params = $_POST;
    if(!$this->oauth->scope->has_updatable_access($params)){
      $allow_fields = $this->oauth->scope->updatable_fields_code_message();
      $errors = array(
        "error" => 'Unauthorized',
        "error_description" => "POST request error: You can only access these fields: ({$allow_fields})"
      );
      return $this->render_unauthorized($errors);
      exit;
    }
    return true;
  }

  function authorize_searchable_access(){
    $params = $_GET;
    if(!$this->oauth->scope->has_searchable_access($params)){
      $allow_fields = $this->oauth->scope->searchable_fields_code_message();
      $errors = array(
        "error" => 'Unauthorized',
        "error_description" => "GET request error: You can only access these fields: ({$allow_fields})"
      );
      return $this->render_unauthorized($errors);
      exit;
    }
    return true;
  }
}
