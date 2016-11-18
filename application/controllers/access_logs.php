<?php
class Access_logs extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
  }

  function index() {
    $params = $this->filter_params(array('application_id', 'from', 'to'));
    $paginate_logs = ApiAccessLog::search_paginate($params,"created_at ASC");

    $this->set_view_variables(array(
      "paginate_logs" => $paginate_logs,
      "params" => $params
    ));
    $this->render_view();
  }

  function show($id) {
    $log = ApiAccessLog::find($id);
    $this->set_view_variables(array("log" => $log));
    $this->render_view();
  }

  function page(){
    echo AppHelper::paginate(10);
  }
}
