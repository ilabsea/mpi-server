<?php
class ParamsHelper {
  var $params = array();

  function set($name, $value) {
    $this->params[$name] = $value;
  }

  function get($name) {
    return isset($this->params[$name]) ? $this->params[$name] : null;
  }

  function bind($data) {
    foreach($data as $key => $value) {
      $this->set($key, $value);
    }
  }
}
