<?php
class AppHelper {
  static function url($params=array()){
    $form = array();
    parse_str($_SERVER['QUERY_STRING'], $form);
    $form = AppHelper::merge_array($form, $params);
    $url = $_SERVER['PHP_SELF']. "?".http_build_query($form);
    return $url;
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
}
