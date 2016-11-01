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

  function access_token(){
    return isset($_SERVER["HTTP_TOKEN"]) ? $_SERVER["HTTP_TOKEN"] : null;
  }
}
