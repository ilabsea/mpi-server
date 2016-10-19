<?php
class ApiAccessController extends ApiController {
  function before_action() {
    parent::before_action();

    if(!$this->oauth->authenticate_token()) {
      return $this->render_unauthorized($this->oauth->errors);
      exit;
    }
  }
}
