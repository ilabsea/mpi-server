<?php

class Patients extends MpiController {
  function init(){
    parent::init();
    $this->load->model("service");
    $this->load->model("patient");
  }

  function render_error($datas){
    $data["patient_list"] = null;
    $data["total_record"] = 0;
    $data["nb_of_page"] = 1;
    $this->set_view_variables($data);
    $this->render_view();
  }

  function patientlist() {

    $data = array();
    $data["services"] = Service::mapper();
    $criteria = $this->filter_params(array("cri_serv_id",
                                          "cri_master_id",
                                          "cri_pat_gender",
                                          "cur_page",
                                          "cri_site_code",
                                          "cri_external_code",
                                          "cri_external_code2",
                                          "date_from",
                                          "date_to",
                                          "orderby",
                                          "orderdirection"));

    $total_patients = $this->patient->count_patient_list($criteria);
    $total_pages = (int)($total_patients / Iconstant::PAGINATION_ROW_PER_PAGE);
    if ($total_pages == 0 || $total_pages * Iconstant::PAGINATION_ROW_PER_PAGE < $total_patients)
      $total_pages++;

    if ($criteria["cur_page"] > $total_pages)
      $criteria["cur_page"] = $total_pages;

    Isession::setCriteria("patient_list", $criteria);

    $page = $criteria["cur_page"] ? $criteria["cur_page"] : 1 ;
    $start = ($page - 1) * Iconstant::PAGINATION_ROW_PER_PAGE;

    $data["show_partial_page"] = 0;
    if ($total_pages >11){
      $start_plus = 0;
      $end_plus = 0;

      if ($criteria["cur_page"] <= 5) {
        $start_page = 1;
        $start_plus = 1 + 5 - $criteria["cur_page"];
      }

      if ($criteria["cur_page"] >= $total_pages - 5){
        $end_page = $total_pages;
        $end_plus =  $criteria["cur_page"] + 5 - $total_pages ;
      }

      if ($criteria["cur_page"] > 5)
        $start_page = $criteria["cur_page"] - 5 - $end_plus;

      if ($criteria["cur_page"] < $total_pages - 5)
        $end_page = $criteria["cur_page"] + 5 + $start_plus;

      $data["show_partial_page"] = 1;
      $data["start_page"] = $start_page;
      $data["end_page"] = $end_page;
    }

    $data["patient_list"] = $this->patient->patient_list($criteria, $start, Iconstant::PAGINATION_ROW_PER_PAGE);
    $data["total_record"] = $total_patients;
    $data["nb_of_page"] = $total_pages;
    $data = array_merge($data, $criteria);
    $this->set_view_variables($data);
    $this->render_view();
  }

  function patientdetail($pat_id) {
    $orderby = "visit_date";
    $orderdirection = "DESC";
    if (isset($_REQUEST["orderby"]))
      $orderby = $_REQUEST["orderby"];

    if (isset($_REQUEST["orderdirection"]))
      $orderdirection = $_REQUEST["orderdirection"];

    $patient = $this->patient->getPatientById($pat_id);
    $var = array();
    array_push($var, $pat_id);
    $visit = $this->patient->getVisits($var, $orderby, $orderdirection);
    $data = array();
    $data["patient"] = $patient;
    $data["visit_list"] = $visit;
    $data["orderby"] = $orderby;
    $data["orderdirection"] = $orderdirection;
    $this->set_view_variables($data);
    $this->render_view();
  }
}
