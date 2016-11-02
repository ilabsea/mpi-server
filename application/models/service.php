<?php

class Service extends Imodel {
  var $serv_id = null;
  var $serv_code = null;
  var $serv_desc = null;


  static function primary_key() {
    return "serv_id";
  }

  static function table_name() {
    return "mpi_service";
  }

  static function class_name(){
    return 'Service';
  }

  static function mapper(){
    $services = Service::all();
    $result = [];
    foreach($services as $service)
      $result[$service->serv_id] = $service->serv_code;
    return $result;
  }

  function getServices() {
      $sql = "SELECT serv_id, serv_code, serv_desc FROM mpi_service";
      return $this->db->query($sql);
  }
}
