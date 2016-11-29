<?php
class Access_logs extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
  }

  function index() {
    $params = $this->filter_params(array('application_id', 'from', 'to', 'type'));
    $view_params = array("params" => $params);

    if($params['type'] == 'graph'){
      $rows = ApiAccessLog::search_graph($params);
      $view_params["rows"] = $rows;
    }
    else{
      $paginate_logs = ApiAccessLog::search_paginate($params);
      $view_params["paginate_logs"] = $paginate_logs;
    }
    $this->set_view_variables($view_params);
    return $this->render_view();
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
