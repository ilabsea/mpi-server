<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_visits extends ApiAccessController{
  function before_action(){
    parent::before_action();
    $this->display_value = new DisplayValue($this->oauth->scope);

    if($this->action_name() == "update_field")
      $this->require_internal_app();
  }

  //GET /api/visits/search
  //{v_site_code, v_ext_code, v_ext_code_2}
  function search(){

    $params = $_GET;
    $filter_visits = FieldTransformer::apply_to_visit($params);
    $no_site_code = !isset($filter_visits['site_code']) || trim($filter_visits['site_code']) == '' ;
    $no_ext_code  = !isset($filter_visits['ext_code']) || trim($filter_visits['ext_code']) == '' ;

    if($no_site_code || $no_ext_code){
      $errors = array("error" => "Invalid input",
                      "error_description" => "api require site code and ext_code");
      $this->render_bad_request($errors);
    }

    $conditions = array();
    $param_keys = array("site_code", "ext_code", "ext_code_2");

    foreach ($param_keys as $value) {
      if(isset($filter_visits[$value]) && $filter_visits[$value])
        $conditions[$value] = $filter_visits[$value];
    }



    $visit = Visit::find_by($conditions);
    $patient = Patient::find_by(array("pat_id" => $visit->pat_id));

    $scope = Scope::find(2);
    $display_value = new DisplayValue($scope);

    $patient_json = $display_value->patient($patient->dynamic_value());

    $visit_json = $display_value->visit($visit->dynamic_value());

    $result = array("patient" => $patient_json, "visit" => $visit_json);

    return $this->render_json($result);
  }

  //GET api/visits/index?pat_id=xxx
  function index() {
    $params = $_GET;
    $this->ensure_field_exist($params, "p_pat_id");

    $paginator = Visit::paginate(array("pat_id" => $params["p_pat_id"]), "visit_id DESC");
    $dynamic_values = new DynamicValue();
    $paginator->records = $dynamic_values->result($paginator->records, "visit");
    $paginator->records = $this->display_value->visits($paginator->records);
    $this->render_json($paginator);
  }

  //POST api/visits/create
  function create(){
    $params = $_POST;

    // mock request params
    // $params = array(
    //   "v_pat_id"=> "KH002100000002",
    //
    //   "v_pat_age"=> "80",
    //   "v_serv_id"=> "2",
    //   "v_visit_date"=> "2016-12-06",
    //   "v_site_code"=> "0202",
    //   "v_ext_code"=> "ex1",
    //   "v_ext_code_2"=> "ex2",
    //   "v_refer_to_vcct"=> "2",
    //   "v_refer_to_oiart"=> "1",
    //   "v_refer_to_std"=> "",
    //   "v_info"=> "extra-test",
    //   "v_vcctnumber"=> "1000",
    //   "v_vcctsite" => "V01-02",
    //
    //   "v_dynamic_field1" => "False",
    //   "v_dynamic_field2" => "hello",
    //   "v_dynamic_field3" => "12dfdsa",
    //   "v_dynamic_field4" => "12.34",
    //   "v_dynamic_field5" => "2016-09-10",
    //   "v_dynamic_field6" => "2016-10-10 12:10:10",
    //   "v_dynamic_field7" => "zzzzz",
    // );

    $filter_visits = FieldTransformer::apply_to_visit($params);
    $this->ensure_field_exist($filter_visits, "pat_id");

    $visit = new Visit($filter_visits);
    if($visit->save()){
      return $this->render_json($visit->dynamic_value());
    }
    else{
      return $this->render_record_errors($visit);
    }
  }

  //GET api/visits/show/visit_id_xxx
  function show($visit_id) {
    $visit = Visit::ensure_find_by(array("visit_id" => $visit_id));
    $visit_json = $visit->dynamic_value();
    $visit_json = $this->display_value->visit($visit_json);
    $this->render_json($visit_json);
  }

  //PUT api/visits/show/visit_id
  function update($visit_id) {
    $params = $_POST;

    // mock
    // $params = array(
    //   "v_pat_age"=> "30",
    //   "v_serv_id"=> "2",
    //   "v_visit_date"=> "2016-12-06");

    $filter_visits = FieldTransformer::apply_to_visit($params);
    $visit = Visit::ensure_find_by(array("visit_id" => $visit_id));

    // Dont allow to update pat_id
    // if(isset($filter_visits["pat_id"]))
    //   unset($filter_visits["pat_id"])
    $visit->update_attributes($filter_visits);

    $visit_json = $visit->dynamic_value();
    $visit_json = $this->display_value->visit($visit_json);
    $this->render_json($visit_json);

  }
}
