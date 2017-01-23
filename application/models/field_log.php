<?php
class FieldLog extends Imodel {
  var $id = null;
  var $field_id = null;
  var $field_code = '';
  var $field_name = '';

  var $application_id = null;
  var $application_name = '';
  var $modified_at = '';
  var $modified_attrs = array();

  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function serialize_fields() {
    return array('modified_attrs');
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "field_log";
  }

  static function class_name(){
    return 'FieldLog';
  }

  static function search_paginate($params){
    $conditions = array();
    if($params['application_name'] != '')
      $conditions["application_name"] = $params['application_name'];
    if($params['from'] != '')
      $conditions["created_at >="] = Imodel::beginning_of_day($params['from']);
    if($params['to'] != '')
      $conditions["created_at <="] = Imodel::end_of_day($params['to']);

    return FieldLog::paginate($conditions,"created_at DESC");
  }
}
