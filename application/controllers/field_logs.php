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

  //POST field_logs/revert/id
  function revert($id){
    $field_log = FieldLog::find($id);
    $modified_attrs = $field_log->modified_attrs;
    if(count($modified_attrs) == 0)
      return redirect(site_url("field_logs/index"));

    $keys =  array_keys($modified_attrs);
    $field_code = $keys[0];
    $field_value = $modified_attrs[$field_code]["from"];
    $pat_id = $field_log->pat_id;

    Patient::update_field($pat_id, $field_code, $field_value);
    return redirect(site_url("field_logs/index"));
  }

}
