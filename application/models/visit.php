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

  function after_create(){
    $visits_count = Visit::count(array("pat_id" => $this->pat_id));
    $visit_positives_count = Visit::count(array("pat_id" => $this->pat_id, "info"=> "positive"));
    $patient = Patient::find($this->pat_id);

    $patient->update_attributes(array("visits_count" => $visits_count,
                                      "visit_positives_count" => $visit_positives_count ));

    ILog::debug_message('patient:', $patient,1);

  }

  function after_update() {
    $visit_positives_count = Visit::count(array("pat_id" => $this->pat_id, "info"=> "positive"));
    $patient = Patient::find($this->pat_id);
    $patient->update_attributes(array("visit_positives_count" => $visit_positives_count ));
  }
}
