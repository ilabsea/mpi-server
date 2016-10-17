<?php
class Application extends Imodel {
  var $id = null;
  var $name = '';
  var $api_key = '';
  var $api_secret = '';
  var $whitelist = '';
  var $status = '1';

  var $scope_id = null;
  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "application";
  }

  static function class_name(){
    return 'Application';
  }

  static function statuses() {
    return array("0" => "Disable", "1" => "Enable");
  }

  function before_create() {
    $api_key_gen = str_replace(".", "", uniqid(null, true)) ;
    $api_secret_gen = base64_encode(uniqid(null, true));

    $this->api_key = "nk{$api_key_gen}";
    $this->api_secret = "ns{$api_secret_gen}";
  }

  function validation_rules(){
    $name_uniqueness = $this->uniqueness_field('name');
    $api_key_uniqueness = $this->uniqueness_field('api_key');
    $api_secret_uniqueness = $this->uniqueness_field('api_secret');

    $this->form_validation->set_rules('name', 'Name', "trim|required|{$name_uniqueness}");
    $this->form_validation->set_rules('scope_id', 'Name', "trim|required");
    $this->form_validation->set_rules('api_key', 'Name', "trim|required|{$api_key_uniqueness}");
    $this->form_validation->set_rules('api_secret', 'Name', "trim|required|{$api_secret_uniqueness}");
  }
}
