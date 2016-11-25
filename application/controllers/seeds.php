<?php
class Seeds extends MpiController {
  function before_actions(){
    parent::before_action();
    $this->required_admin_user();
  }

  function init(){
    parent::init();
    $this->load->model("field");
    $this->load->model("patient");

  }

  functon console() {
    exit;
  }

  function patient_visits_counter(){
    Patient::migrate_counter_cache();
  }

  function visit_fields() {
    $visits = array(
      array("code" => "visit_id", "type" => "Integer"),
      array("code" => "pat_id", "type" => "String"),
      array("code" => "serv_id", "type" => "Integer"),
      array("code" => "site_code", "type" => "String"),
      array("code" => "ext_code", "type" => "String"),
      array("code" => "ext_code_2", "type" => "String"),
      array("code" => "info", "type" => "String"),
      array("code" => "visit_date", "type" => "Date"),
      array("code" => "date_create", "type" => "DateTime"),
      array("code" => "pat_age", "type" => "Integer"),
      array("code" => "refer_to_vcct", "type" => "Integer"),
      array("code" => "refer_to_oiart", "type" => "Integer"),
      array("code" => "refer_to_std", "type" => "Integer")
    );
    return $visits;
  }

  function patient_fields(){
    $patients = array(
      array("code" => "pat_id", "type" => "String"),
      array("code" => "pat_gender", "type" => "Integer"),
      array("code" => "pat_dob", "type" => "Date"),
      array("code" => "pat_age", "type" => "Integer" ),
      array("code" => "date_create", "type" => "DateTime" ),
      array("code" => "pat_version", "type" => "String" ),
      array("code" => "pat_register_site", "type" => "String" ),
      array("code" => "new_pat_id", "type" => "String" ),


      array("code" => "fingerprint_l1", "type" => "String" ),
      array("code" => "fingerprint_l2", "type" => "String" ),
      array("code" => "fingerprint_l3", "type" => "String" ),
      array("code" => "fingerprint_l4", "type" => "String" ),
      array("code" => "fingerprint_l5", "type" => "String" ),
      array("code" => "fingerprint_r1", "type" => "String" ),
      array("code" => "fingerprint_r2", "type" => "String" ),
      array("code" => "fingerprint_r3", "type" => "String" ),
      array("code" => "fingerprint_r4", "type" => "String" ),
      array("code" => "fingerprint_r5", "type" => "String" )
    );
    return $patients;
  }

  function fields(){
    $table_fields = array_merge($this->visit_fields(), $this->patient_fields());
    ILog::debug_message("fields merged: ", $table_fields);

    foreach($table_fields as $field_attrs) {
      $field_attrs['dynamic_field'] = 0;
      $field_attrs['is_encrypted'] = 0;
      $field_attrs['name'] = $field_attrs["code"];
      ILog::debug_message("fields: ", $field_attrs);
      $field = Field::find_by(array("code" => $field_attrs["code"] ));

      if($field)
        $field->update_attributes($field_attrs);
      else
        $field = new Field();
        $field->set_attributes($field_attrs);
        if(!$field->save()){
          ILog::debug_message("errors", $field->get_errors());
      }
    }

  }
}
