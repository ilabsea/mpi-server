<?php
class Access_logs extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $logs = ApiAccessLog::all(array(), $page, "created_at ASC");
    $this->set_view_variables(array(
      "logs" => $logs,
      "page" => $page
    ));
    $this->render_view();
  }

  function show($id) {
    $log = ApiAccessLog::find($id);
    $this->set_view_variables(array("log" => $log));
    $this->render_view();
  }
}
