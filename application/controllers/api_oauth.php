<?php
//name should be ApiOauth but can not find a way to map this to CI router
class Api_oauth extends ApiController {
  function before_action() {
    parent::before_action();

    $this->load->model("application");
    $this->load->model("scope");
  }

  function token(){
    if($this->oauth->issue_token())
      return $this->render_json($this->oauth->application_token);
    return $this->render_unauthorized($this->oauth->errors);
  }
}
