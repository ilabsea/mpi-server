<?php
class ApiAccessLog extends Imodel {
  var $id = null;
  var $application_id = null;
  var $application_name = null;
  var $ip = null;
  var $status = null;
  var $params = array();
  var $action = null;
  var $http_verb = null;
  var $url = null;
  var $created_at = null;
  var $updated_at = null;

  static function search_paginate($params){
    $conditions = array();
    if($params['application_id'] !='')
      $conditions["application_id"] = $params['application_id'];
    if($params['from'] != '')
      $conditions["created_at >="] = Imodel::beginning_of_day($params['from']);
    if($params['to'] != '')
      $conditions["created_at <="] = Imodel::end_of_day($params['to']);

    return ApiAccessLog::paginate($conditions,"created_at ASC");
  }

  static function timestampable() {
    return true;
  }

  static function serialize_fields() {
    return array('params');
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "api_access_log";
  }

  static function class_name(){
    return 'ApiAccessLog';
  }

}
