<?php
class Sites extends MpiController {
  function index() {

    $services = Service::mapper();
    $provinces = Province::mapper();

    $params = $this->filter_params(array("serv_id", "site_code", "pro_code"  ,"order_by", "order_direction"));

    $paginate_sites = Site::paginate_filter($params);
    $this->set_view_variables(array("params" => $params,
                                    "services" => $services,
                                    "provinces" => $provinces,
                                    "paginate_sites" => $paginate_sites));
    $this->render_view();
  }

  function search() {
    $criteria = $_POST;
    $criteria["cri_site_code"] = trim($criteria["cri_site_code"]);

    $session_data = Isession::getCriteria("site_list");
    if ($session_data != null) :
      $criteria = array_merge($session_data, $criteria);
    endif;

    Isession::setCriteria("site_list", $criteria);
    redirect(site_url("sites/sitelist"));
  }
}
