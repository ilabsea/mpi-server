<?php
class Field extends Imodel {
  var $id = null;
  var $name = '';
  var $code = '';
  var $is_encrypted = 0;
  var $type = "";
  var $table_type = "";
  var $soft_delete = false;
  var $dynamic_field = 1;
  var $created_at = null;
  var $updated_at = null;

  const PATIENT = 'Patient';
  const VISIT = 'Visit';

  static function table_types(){
    return array(
      self::PATIENT => self::PATIENT,
      self::VISIT => self::VISIT
    );
  }

  function cast_value($value) {
    if($this->type == "Boolean")
      return strtolower($value) == 'true' ? true: false;

    if($this->type == "Integer")
      return intval($value);

    if($this->type == "Float")
      return floatval($value);

    return $value;
  }

  static function types() {
    return array(
      "Boolean" => "Boolean",
      "String" => "String",
      "Integer" => "Integer",
      "Float" => "Float",
      "Date" => "Date",
      "DateTime" => "DateTime"
    );
  }

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field";
  }

  static function class_name(){
    return 'Field';
  }

  static function mapper() {
    $fields = Field::all();
    $result = [];
    foreach($fields as $field)
      $result[$field->id] = $field->code;

    $result["visit.*"] = "visit.*";
    $result["patient.*"] = "patient.*";
    return $result;
  }

  static function dynamic_fields() {
    $fields = Field::all(array("dynamic_field" => 1));
    $result = array();
    foreach($fields as $field)
      $result[$field->code] = $field;
    return $result;
  }

  function before_save(){
    parent::before_save();

    $code = str_replace(' ', '', $this->code);
    $code = preg_replace('/[^A-Za-z0-9]/', '_', $code);
    $this->set_attribute("code", $code);
  }

  function validation_rules(){
    $code_uniqueness = $this->uniqueness_field('code');
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('code', 'Code', "trim|required|{$code_uniqueness}");
    $this->form_validation->set_rules('type', 'Type', 'required');
    return true;
  }

  function is_patient_field(){
    return $this->table_type == Field::PATIENT;
  }

  function is_visit_field(){
    return $this->table_type == Field::VISIT;
  }
}
