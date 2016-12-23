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

  static $cache_fields = null;

  const PATIENT = 'Patient';
  const VISIT = 'Visit';

  static function table_types(){
    return array(
      self::PATIENT => self::PATIENT,
      self::VISIT => self::VISIT
    );
  }

  function internal_code(){
    if($this->dynamic_field)
      return $this->code;
    if($this->is_patient_field())
      return substr($this->code, strlen(Patient::PREFIX_DYNAMIC));
    else
      return substr($this->code, strlen(Visit::PREFIX_DYNAMIC));
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

  static function cache_all(){
    if(Field::$cache_fields)
      return Field::$cache_fields;

    Field::$cache_fields = Field::all();
    return Field::$cache_fields;
  }

  static function map_field_codes($field_ids) {
    $fields = Field::cache_all();
    $result = [];

    foreach($fields as $field)
      if(in_array($field->id(), $field_ids))
        $result[$field->id] = $field->code;

    return $result;

  }

  static function map_by_code(){
    $fields = Field::cache_all();
    $result = [];
    foreach($fields as $field)
      $result[$field->code] = $field;

    return $result;
  }

  static function mapper() {
    $fields = Field::cache_all();
    $result = [];
    foreach($fields as $field)
      $result[$field->id] = $field->code;

    $result[Visit::ALL_FIELD] = Visit::ALL_FIELD;
    $result[Patient::ALL_FIELD] = Patient::ALL_FIELD;
    return $result;
  }

  static function static_mapper() {
    $fields = Field::cache_all();
    $result = array();

    foreach($fields as $field)
      if($field->dynamic_field == 0)
        $result[$field->id] = $field->code;

    $result[Visit::ALL_FIELD] = Visit::ALL_FIELD;
    $result[Patient::ALL_FIELD] = Patient::ALL_FIELD;
    return $result;
  }

  static function dynamic_fields() {
    $fields = Field::cache_all(); //dynamic_field
    $result = array();
    foreach($fields as $field)
      if($field->dynamic_field == 1)
        $result[$field->code] = $field;
    return $result;
  }

  function before_save(){
    parent::before_save();

    $code = str_replace(' ', '', $this->code);
    $code = preg_replace('/[^A-Za-z0-9]/', '_', $code);
    $this->set_attribute("code", $code);
  }

  function after_save(){
    $conditions = array("field_id" => $this->id());
    $update_attrs = array("field_code" => $this->code,
                          "field_type" => $this->type,
                          "is_encrypted" => $this->is_encrypted);
    FieldValue::update_all($conditions, $update_attrs);
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
