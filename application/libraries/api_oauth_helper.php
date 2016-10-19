<?php
class ApiOauthHelper {
  var $errors = array();
  var $application = null;
  var $scope = null;
  var $application_token = null;
  var $params = array();

  function __construct($request){
    $this->params = $request;
  }

  function issue_token(){
    $conditions = array("api_key" => $this->params["client_id"],
                        "api_secret" => $this->params["client_secret"]);

    if($this->params['grant_type'] != "client_credentials") {
      $this->errors = array("error" => "invalid grant type",
                            "error_description" => "require params grant_type to be set with value of client_credentials");
      return false;
    }

    $application = Application::find_by($conditions);


    if(!$application){
      $this->errors = array("error" => "invalid application",
                            "error_description" => "This is no applicaiton with this credential ");
      return false;
    }

    $app_name = $application->name;
    $ip = $this->params['ip_address'];

    if(!$application->ok()) {
      $this->errors = array("error" => "invalid application",
                            "error_description" => "Application <{$app_name}> has been disabled");
      return false;
    }
    else if($application->accessible_by_ip($ip)) {
      $this->errors = array("error" => "invalid application",
                            "error_description" => "Application <{$app_name}> is not allowed by <{$ip}>");
      return false;
    }


    $this->application = $application;
    $this->scope = Scope::find($application->scope_id);

    $this->application_token = ApplicationToken::generate($application);

    return true;
  }

  function authenticate_token(){
    $this->errors = array();

    if(!isset($this->params['access_token'])) {
      $this->errors = array("error" => "invalid token",
                            "error_description" => "access token is required");
      return false;
    }

    $application_token = ApplicationToken::find_by( array("token" => $this->params['access_token']) );

    if(!$application_token){
      $this->errors = array("error" => "invalid token",
                            "error_description" => "incorrect access token");
      return false;
    }

    if($application_token->expired()){
      $this->errors = array("error" => "invalid token",
                            "error_description" => "access token is expired");
      return false;
    }

    $application = Application::find($application_token->application_id);

    if(!$application->ok()){
      $app_name = $application->name;
      $this->errors = array("error" => "invalid token",
                            "error_description" => "Application <{$app_name}> has been disabled");
      return false;
    }

    $this->application_token = $application_token;
    $this->application = $application;
    $this->scope = Scope::find($application->scope_id);
    return true;
  }

  function current_applicaiton_token(){
    return $this->application_token;
  }

  function current_applicaiton(){
    return $this->application;
  }

  function current_scope(){
    $this->scope;
  }
}
