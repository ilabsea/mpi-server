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
  var $refer_to_vcct = 0;
  var $refer_to_oiart = 0;
  var $refer_to_std = 0;
  var $created_at = null;
  var $updated_at = null;
  var $vcctsite = null;
  var $vcctnumber = null;

  static function virtual_fields() {
    return array("vcctsite", "vcctnumber");
  }

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
    $patient_id = $patient->pat_id;
    $visits_count = Visit::count(array("pat_id" => $patient_id));
    $visit_positives_count = Visit::count(array("pat_id" => $patient_id, "info"=> "positive"));


    $patient->update_attributes(array("visits_count" => $visits_count,
                                      "visit_positives_count" => $visit_positives_count ));
  }

  function before_create() {
    if(!$this->date_create)
      $this->set_attribute("date_create", Imodel::current_time());
  }

  function after_create(){
    $patient = Patient::find_by(array("pat_id" => $this->pat_id));
    Visit::update_count_cache($patient);

    if($this->pat_age) {
      $patient = Patient::find_by(array("pat_id" => $this->pat_id));
      $patient->update_attributes(array("pat_age" => $this->pat_age));
    }

    if($this->serv_id == 2 && $this->vcctsite && $this->vcctnumber) {
      $vcct_oiart_attrs = array( "vcct_external_code" => $this->vcctnumber,
                                 "vcct_site" => $this->vcctsite);

      $vcct = VcctFromOIART::find_by_or_new($vcct_oiart_attrs);
      if($vcct->new_record()){
        $vcct->set_attribute("oiart_pat_id", $this->pat_id);
        $vcct->save();
      }
    }
  }

  function after_update() {
    $patient = Patient::find_by(array("pat_id" =>$this->pat_id));
    $visit_positives_count = Visit::count(array("pat_id" => $patient->pat_id, "info"=> "positive"));

    $patient->update_attributes(array("visit_positives_count" => $visit_positives_count ));
  }

  function validation_rules(){
    $this->form_validation->set_rules('serv_id', 'Service ID', 'trim|required');
    $this->form_validation->set_rules('pat_id', 'Patient ID', "trim|required");
    $this->form_validation->set_rules('site_code', 'Site Code', 'required');
    $this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
    return true;
  }
}
