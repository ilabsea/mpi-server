<?php
class ApiAccessLog extends Imodel {
  var $id = null;
  var $application_id = null;
  var $application_name = null;
  var $ip = null;
  var $status = null;
  var $status_description = null;
  var $params = array();
  var $action = null;
  var $http_verb = null;
  var $url = null;
  var $created_at = null;
  var $updated_at = null;

  static function search_paginate($params){
    $conditions = array();
    if($params['application_id'] != '')
      $conditions["application_id"] = $params['application_id'];

    if($params['from'] != '')
      $conditions["created_at >="] = Imodel::beginning_of_day($params['from']);

    if($params['to'] != '')
      $conditions["created_at <="] = Imodel::end_of_day($params['to']);

    return ApiAccessLog::paginate($conditions,"created_at DESC");
  }

  static function search_graph($params) {
    $lastmonth = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
    $active_record = new ApiAccessLog();

    if($params["application_id"] != '')
      $active_record->db->where("application_id", $params['application_id']);

    if($params['from'] != '')
      $active_record->db->where("created_at >=", Imodel::beginning_of_day($params['from']));

    if($params['to'] != '')
      $active_record->db->where("created_at <=", Imodel::end_of_day($params['to']));

    $active_record->db->select("application_name, DATE(created_at) AS access_date, count(*) AS access_counts");
    $active_record->db->group_by("application_name, access_date");
    $active_record->db->order_by("access_date DESC");
    $active_record->db->from(ApiAccessLog::table_name());

    $query = $active_record->db->get();

    $datas = array();
    $unique_apps = array();
    foreach($query->result() as $item) {
      $datas[$item->access_date][$item->application_name] =  $item->access_counts;
      $unique_apps[$item->application_name] = 1;
    }
    return ApiAccessLog::normalize_data($datas, $unique_apps);
  }

  static function normalize_data($datas, $unique_apps){
    $rows = array();
    $head = array("Date");
    foreach($unique_apps as $app_name => $_)
      $head[] = $app_name;
    $rows[] = $head;

    foreach($datas as $date => $app_counts) {
      $row = array();
      $row[] = $date;
      foreach($unique_apps as $app_name => $_){
        $count = isset($app_counts[$app_name]) ? $app_counts[$app_name] : 0;
        $row[] = $count;
      }
      $rows[] = $row;
    }
    return $rows;
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
