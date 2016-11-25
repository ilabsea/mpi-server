<?php
class Visit extends Imodel {
  var $visit_id = null;
  var $pat_id = '';
  var $serv_id = '';
  var $site_code = '';
  var $ext_code = '';
  var $ext_code_2 = '';

  var $info = ''; //positive
  var $visit_date = null;
  var $date_create = null;
  var $pat_age = '';
  var $refer_to_vcct = null;
  var $refer_to_oiart = null;
  var $refer_to_std = null;
  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "visit_id";
  }

  static function table_name() {
    return "mpi_visit";
  }

  static function class_name(){
    return 'Visit';
  }

  static function update_count_cache($patient){
    $patient_id = $patient->id();
    $visits_count = Visit::count(array("pat_id" => $patient_id));
    $visit_positives_count = Visit::count(array("pat_id" => $patient_id, "info"=> "positive"));


    $patient->update_attributes(array("visits_count" => $visits_count,
                                      "visit_positives_count" => $visit_positives_count ));

  }

  function after_create(){
    $patient = Patient::find($this->pat_id);
    Visit::update_count_cache($patient);

  }

  function after_update() {
    $patient = Patient::find($this->pat_id);
    $visit_positives_count = Visit::count(array("pat_id" => $patient->id(), "info"=> "positive"));

    $patient->update_attributes(array("visit_positives_count" => $visit_positives_count ));
  }
}
