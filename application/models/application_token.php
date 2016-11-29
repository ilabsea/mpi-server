<?php
class ApplicationToken extends Imodel {
  var $id = null;
  var $application_id = null;
  var $scope_id = null;
  var $token = null;
  var $refresh_token = null;
  var $expires_in = null;
  var $created_at = null;
  var $updated_at = null;

  function before_create(){
    $this->token = md5("nk_token" . base64_encode(uniqid()) );
    $this->refresh_token = md5("nk_refresh_token" . base64_encode(uniqid()) );
    $this->expires_in = 30 * 24 * 60 * 60;
  }

  function to_json(){
    return array(
      "token" => $this->token,
      "refresh_token" => $this->refresh_token,
      "expires_in" => $this->expires_in,
      "created_at" => $this->created_at
    );
  }

  function expired() {
    $created_at = strtotime($this->created_at);
    return $created_at + $this->expires_in < time();
  }

  static function generate($application) {
    $application_token = new ApplicationToken();
    $application_token->set_attributes(array(
      "application_id" => $application->id,
      "scope_id" => $application->scope_id
    ));

    if($application_token->save(false))
      return $application_token;
    return null;
  }

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "application_token";
  }

  static function class_name(){
    return 'ApplicationToken';
  }


  static function authenticate($params){
    $conditions = array("api_key" => $params["client_id"],
                        "api_secret" => $params["client_secret"]);

    $app = Application::find_by($conditions);
    return $app;
  }

}
