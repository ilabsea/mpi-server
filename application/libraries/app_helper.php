<?php
class AppHelper {
  static function url($params=array()){
    $form = array();
    parse_str($_SERVER['QUERY_STRING'], $form);
    $form = AppHelper::merge_array($form, $params);
    $url = $_SERVER['PHP_SELF']. "?".http_build_query($form);
    return $url;
  }

  static function present($params, $key){
    return isset($params[$key]) && $params[$key];
  }

  static function merge_array($array1, $array2) {
    $result = $array1;
    foreach($array2 as $key =>$value)
      $result[$key] = $value;
    return $result;
  }

  static function is_post_request(){
    $request_type = AppHelper::request_type();
    return $request_type == 'POST';

  }

  static function is_get_request(){
    $request_type = AppHelper::request_type();
    return $request_type == 'GET';
  }

  static function request_type(){
    return $_SERVER['REQUEST_METHOD'];
  }

  static function h($value){
    if(isset($value))
      return $value;
    return "";
  }

  static function h_c($record, $key){
    if(is_object($record) && property_exists($record, $key))
      return $record->$key;
    else if(is_array($record) && isset($record[$key]))
      return $record[$key];
    return "";
  }
}
