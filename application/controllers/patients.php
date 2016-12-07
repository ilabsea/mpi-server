<?php

class Patients extends MpiController {
  function init(){
    parent::init();
    $this->load->model("service");
    $this->load->model("patient");
  }

  function index() {
    $services = Service::mapper();
    $params = $this->filter_params(array("serv_id", "master_id", "pat_gender",
      "site_code", "external_code", "external_code2", "date_from",
      "date_to", "order_by", "order_direction"));

    $paginate_patients = Patient::paginate_filter($params);
    $this->set_view_variables(array("services" => $services,
                                    "params" => $params,
                                    "paginate_patients" => $paginate_patients));
    $this->render_view();
  }

  function show($pat_id) {
    $patient = Patient::find_by(array("pat_id" => $pat_id));
    $params = $this->filter_params(array("order_by", "order_direction"));
    $visits = Patient::visits(["'{$pat_id}'"], $params["order_by"], $params["order_direction"]);

    $this->set_view_variables(array("patient" => $patient,
                                    "visits" => $visits,
                                    "params" => $params));
    $this->render_view();
  }
}
