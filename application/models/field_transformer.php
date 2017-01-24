<?php
class FieldTransformer {
  //$params($_GET, $_POST, $_REQUEST)

  public static function apply_to($params, $whitelists, $allow_empty=true){
    $fields = Field::cache_all();
    $klass = get_class( $whitelists);

    $transforms = array();
    $prefix = $klass == "Patient" ? Patient::PREFIX_DYNAMIC : Visit::PREFIX_DYNAMIC;

    foreach($whitelists as $field_name => $value) {
      $field_in_dynamic_list = $prefix.$field_name;
      if(isset($params[$field_in_dynamic_list]))
        $transforms[$field_name] =  $params[$field_in_dynamic_list];
    }

    foreach($fields as $field) {
      $is_obj_table =  $klass == 'Patient' ? $field->is_patient_field() : $field->is_visit_field();
      if($field->dynamic_field &&  $is_obj_table && isset($params[$field->code]) )
        $transforms[$field->code] = $params[$field->code];
    }

    if($allow_empty)
      return $transforms;

    $result = array();
    foreach($transforms as $key => $value)
      if(trim($value) != "")
        $result[$key] = $value;

    return $result;
  }

  public static function apply_to_patient($params, $allow_empty=true){
    $patient = new Patient();
    return FieldTransformer::apply_to($params, $patient, $allow_empty);
  }

  public static function apply_to_visit($params, $allow_empty=true){
    $visit = new Visit();
    return FieldTransformer::apply_to($params, $visit, $allow_empty);
  }

  public function apply_to_all($params, $allow_empty=true){
    $transform_patients = FieldTransformer::apply_to_patient($params, $allow_empty);
    $transform_visits = FieldTransformer::apply_to_visit($params, $allow_empty);

    return array("patients" => $transform_patients, "visits" => $transform_visits);
  }

}
