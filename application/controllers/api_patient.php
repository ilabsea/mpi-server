<?php
//name should be ApiPatient but can not find a way to map this to CI router
class Api_patient extends ApiAccessController{
  // var $skip_before_action = array('index');

  function index() {
    echo "worked";
  }
}
