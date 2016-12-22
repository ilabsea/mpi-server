<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_visits extends ApiAccessController{

  //GET api/visits/index?pat_id=xxx
  function index() {
    $params = $_GET;
    $this->ensure_field_exist($params, "pat_id");

    $paginator = Visit::paginate(array("pat_id" => $params["pat_id"]), "visit_id DESC");
    $dynamic_values = new DynamicValue();
    $paginator->records = $dynamic_values->result($paginator->records, "visit");
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
    $this->render_json($visit->dynamic_value());
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
    $this->render_json($visit->dynamic_value());

  }
}
