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
}
