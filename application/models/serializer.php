<?php
class Serializer {
  var $fields = array();
  var $object = null;

  function __construct($object, $fields = array()) {
    $this->object = $object;
    if(count($fields) > 0)
      $this->fields = $fields;
  }

  function set_fields($fields) {
    $this->fields = $fields;
  }

  function attributes() {
    $result = array();
    foreach($this->fields as $field_name) {
      if(property_exists($this->object, $field_name))
        $result[$field_name] = $this->object->$field_name;
    }
    return $result;
  }

}
