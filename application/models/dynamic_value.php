<?php
// reduce n+1 loop + join query
class DynamicValue {
  var $patients = array();
  function __construct($records, $field_codes=array()) {
    $this->field_codes = $field_codes;
    $this->field_mappers = Field::mapper();
    $this->records = $records;
  }

  function map_field_values_by_record() {
    $ids = array();
    foreach($this->records as $record)
      $ids[] = $record->id();

    $field_values = FieldValue::all(array("field_owner_id" => $ids));

    $result = array();
    foreach($field_values as $field_value) {
      $record_id = $field_value->field_owner_id;

      if(isset($result[$record_id]))
        $result[$record_id][] = $field_value;
      else
        $result[$record_id] = array($field_value);
    }

    return $result;
  }

  function merge_values($record, $dynamic_values) {
    $merged_result = $record->to_array();

    foreach($dynamic_values as $dynamic_value) {
      $field_code = $this->field_mappers[$dynamic_value->field_id];
      $merged_result[$field_code] = $dynamic_value->value;
    }
    return $merged_result;
  }

  function result() {
    $mapper = $this->map_field_values_by_record();
    $result = array();

    foreach($this->records as $record) {
      $dynamic_values = isset($mapper[$record->id()]) ? $mapper[$record->id()] : array();
      $result[] = $this->merge_values($record, $dynamic_values);
    }
    return $result;
  }

}
