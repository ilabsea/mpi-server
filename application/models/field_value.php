<?php
class FieldValue extends Imodel {
  var $id = null;
  var $field_id = null;
  var $field_type = null;

  var $field_owner_id = null;
  var $field_owner_type = null;

  var $value = null;

  var $created_at = null;
  var $updated_at = null;

  const PATIENT = "Patient";
  const VISIT = "Visit";

  static function create_fields($params, $field_owner) {
    $field_owner_id = $field_owner->id();
    $class_name = "Patient";
    $dynamic_fields = Field::dynamic_fields();

    $field_owner_type = is_a( $field_owner, $class_name ) ? FieldValue::PATIENT : FieldValue::VISIT;

    foreach($params as $field_code => $field_value) {
      $dynamic_field = $dynamic_fields[$field_code];
      $attrs = array( "field_owner_id" => $field_owner_id,
                      "field_owner_type" => $field_owner_type,
                      "field_id" => $dynamic_field->id(),
                      "field_type" => $dynamic_field->type,
                      "value" => ($field_value) );

      $field_value = new FieldValue();
      $field_value->set_attributes($attrs);
      $field_value->save();
    }
  }

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field_value";
  }

  static function class_name(){
    return 'FieldValue';
  }
}
