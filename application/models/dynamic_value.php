<?php
// reduce n+1 loop + join query
class DynamicValue {
  var $patients = array();
  function __construct($field_codes=array()) {
    $this->field_codes = $field_codes; //filter access scope not used yet
    $this->field_mappers = Field::mapper();
  }

  function result($records, $type='patient') {

    if(count($records) == 0)
      return $records;

    $this->records = $records;
    $this->type = $type;

    $mapper = $this->map_field_values_by_record();
    $result = array();

    foreach($this->records as $record) {
      $id = $this->id_value($record);
      $dynamic_values = isset($mapper[$id]) ? $mapper[$id] : array();
      $result[] = $this->merge_values($record, $dynamic_values);
    }
    return $result;
  }

  private function id_value($record) {
    $id = ($this->type == "patient" ? "id" : "visit_id");
    return $record->$id;
  }

  private function merge_values($record, $dynamic_values) {
    $merged_result = array();

    foreach($record as $key => $value) {
      $merged_result[$key] = $value;
    }

    foreach($dynamic_values as $dynamic_value) {
      $field_code = $this->field_mappers[$dynamic_value->field_id];
      $merged_result[$field_code] = $dynamic_value->value;
    }
    return $merged_result;
  }

  private function map_field_values_by_record() {
    $ids = array();

    foreach($this->records as $record){
      $ids[] = $this->id_value($record);
    }

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



}
