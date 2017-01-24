<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_fields extends ApiAccessController{

  function skip_authenticate(){
    return true;
  }

  //api/fields/search
  //site_code, ex_code, ex_code2


  //POST /api/fields/update/:pat_id
  // name, value
  function update($pat_id){
    $params = $_GET;

    $name  = $params['name'];
    $value = $params['value'];

    $params = array("$name" => $value);
    $field_params = FieldTransformer::apply_to_patient($params);
  }

}
