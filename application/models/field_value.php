<?php
class FieldValue extends Imodel {
  var $id = null;
  var $field_id = null;
  var $field_code = null;
  var $field_type = null;
  var $is_encrypted = false;

  var $field_owner_id = null;
  var $field_owner_type = null;


  var $value = null;

  var $created_at = null;
  var $updated_at = null;

  const PATIENT = "Patient";
  const VISIT = "Visit";

  function before_save(){
    $this->set_attribute("value", $this->cast_value($this->value));
  }

  function get_value(){
    if($this->is_encrypted && $this->value && trim($this->value) !== ""){
      $decryped = FieldEncryption::decrypt($this->value);
      $decryped = str_replace("\0", '', $decryped); //remove \u0000 null charactor Unicode equal to \0
      return $decryped;
    }

    return $this->value;
  }

  function cast_value($value) {
    $cast_value = $value;
    if($this->field_type == "Boolean")
      $cast_value = strtolower($value) == 'true' ? true: false;

    else if($this->field_type == "Integer")
      $cast_value = intval($value);

    else if($this->field_type == "Float")
      $cast_value = floatval($value);

    else if($this->field_type == "Date") {
      $format = '%Y-%m-%d';
      $cast_value = strptime($value, $format) ? $value : '';
    }

    else if($this->field_type == 'DateTime') {
      $format = '%Y-%m-%d %H:%M:%S';
      $cast_value = strptime($value, $format) ? $value : '' ;
    }

    if($this->is_encrypted)
      return FieldEncryption::encrypt($value);
    else
      return $cast_value;
  }

  static function create_or_update_fields($params, $field_owner) {
    $field_owner_id = $field_owner->id();
    $class_name = "Patient";
    $dynamic_fields = Field::dynamic_fields();

    $field_owner_type = is_a( $field_owner, $class_name ) ? FieldValue::PATIENT : FieldValue::VISIT;

    foreach($params as $field_code => $value) {
      $dynamic_field = $dynamic_fields[$field_code];
      $attrs = array( "field_owner_id" => $field_owner_id,
                      "field_owner_type" => $field_owner_type,
                      "field_id" => $dynamic_field->id(),
                      "field_type" => $dynamic_field->type
                    );
      $field_value = FieldValue::find_by_or_new($attrs);

      $field_value->set_attributes(array("field_code" => $field_code ,
                                         "is_encrypted" => $dynamic_field->is_encrypted,
                                         "value" => $value));
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
