<?php
class FieldTransformer {
  //$params($_GET, $_POST, $_REQUEST)

  public static function apply_to($params, $whitelists){
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
      if($field->dynamic_field &&  $is_obj_table)
        $transforms[$field->code] = isset($params[$field->code]) ? $params[$field->code] : null;
    }
    return $transforms;
  }

  public static function apply_to_patient($params){
    $patient = new Patient();
    return FieldTransformer::apply_to($params, $patient);
  }

  public static function apply_to_visit($params){
    $visit = new Visit();
    return FieldTransformer::apply_to($params, $visit);
  }

  public function apply_to_all($params, $merged=false){
    $transform_patients = FieldTransformer::apply_to_patient($params);
    $transform_visits = FieldTransformer::apply_to_visit($params);

    return $merged ? AppHelper::merge_array($transform_visits, $transform_patients) : array("patients" => $transform_patients, "visits" => $transform_visits);
  }

}
