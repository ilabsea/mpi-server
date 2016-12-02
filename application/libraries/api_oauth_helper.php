<?php
class ApiOauthHelper {
  var $errors = array();
  var $application = null;
  var $scope = null;
  var $application_token = null;

  function __construct(){
  }

  function readParam($key) {
    return isset($this->params[$key]) ? $this->params[$key] : '';
  }

  function error_message(){
    return $this->errors['error']." (".$this->errors['error_description']. ")";
  }

  function strategy_token($params){
    $this->params = $params;
    $this->errors = array();

    $client_id = $this->readParam("client_id");
    $client_secret = $this->readParam("client_secret");

    $conditions = array("api_key" => $client_id,
                        "api_secret" => $client_secret);

    if($client_id == "" || $client_secret == ""){
      $this->errors = array("error" => "invalid params",
                            "error_description" => "require client_id and client_secret");
      return false;
    }

    if(isset($this->params['refresh_token']) && $this->readParam("grant_type") != 'refresh_token'  ) {
      $this->errors = array("error" => "invalid grant type",
                            "error_description" => "require params grant_type to be set with value of refresh_token");
      return false;
    }

    if(!isset($this->params['refresh_token']) && $this->readParam('grant_type') != "client_credentials") {
      $this->errors = array("error" => "invalid grant type",
                            "error_description" => "require params grant_type to be set with value of client_credentials");
      return false;
    }

    $application = Application::find_by($conditions);


    if(!$application){
      $this->errors = array("error" => "invalid application",
                            "error_description" => "incorrect client_id/client_secret");
      return false;
    }

    $app_name = $application->name;
    $ip = $this->ip_address();

    if(!$application->ok()) {
      $this->errors = array("error" => "invalid application",
                            "error_description" => "application <{$app_name}> has been disabled");
      return false;
    }
    else if(!$application->accessible_by_ip($ip)) {
      $this->errors = array("error" => "invalid application",
                            "error_description" => "application <{$app_name}> is not allowed by <{$ip}>");
      return false;
    }

    $this->application = $application;
    $this->scope = Scope::find($application->scope_id);

    if($this->readParam("grant_type") == "client_credentials")
      $this->application_token = ApplicationToken::generate($application);

    else if($this->readParam("grant_type") == "refresh_token"){
      $application_token = ApplicationToken::find_by(array("refresh_token" => $this->readParam("refresh_token"),
                                                           "application_id" => $this->application->id ));

      if(!$application_token) {
        $this->errors = array("error" => "invalid refresh_token",
                              "error_description" => "application refresh_token is incorrect");
        return false;
      }
      $this->application_token = ApplicationToken::generate($application);
    }
    return true;
  }

  function authenticate_token($access_token){
    $this->errors = array();

    if(!$access_token) {
      $this->errors = array("error" => "invalid token",
                            "error_description" => "access token is required");
      return false;
    }

    $application_token = ApplicationToken::find_by( array("token" => $access_token) );

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

  function ip_address(){
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
    else
      $ipaddress = 'UNKNOWN';
    return $ipaddress;
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
