<?php
class Field_logs extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
  }

  function index() {
    $params = $this->filter_params(array('application_name', 'from', 'to'));
    $view_params = array("params" => $params);

    $paginate_logs = FieldLog::search_paginate($params);
    $view_params["paginate_logs"] = $paginate_logs;

    $this->set_view_variables($view_params);
    return $this->render_view();
  }

  function show($id) {
    $log = FieldLog::find($id);
    $this->set_view_variables(array("log" => $log));
    $this->render_view();
  }

  function page(){
    echo AppHelper::paginate(10);
  }
}
