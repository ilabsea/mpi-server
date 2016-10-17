<?php
class Fields extends MpiController {
  function __construct($load_fingerprint=false, $init_session=true){
    parent::__construct($load_fingerprint=false, $init_session=true);
    $this->before_action();
  }

  function before_action() {
    $this->require_admin_access();
    $this->load->model("field");
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $fields = Field::all(array(), $page, "dynamic_field DESC, name ASC");

    $this->load->template("templates/general", "fields/index", Iconstant::MPI_APP_NAME,
                          array("fields" => $fields,
                                "page" => $page));
  }

  function add() {
    $field = new Field();
    $this->load->template("templates/general", "fields/add", Iconstant::MPI_APP_NAME, array("field" => $field));
  }

  function create(){
    $field = new Field();
    $field->set_attributes($this->field_params());

    if($field->save()) {
      Isession::setFlash("success", "Field has been successfully created");
      redirect(site_url("fields/index"));
      return;
    }
    else{
      $this->load->template("templates/general", "fields/add", Iconstant::MPI_APP_NAME, array("field" => $field));
    }
  }

  function edit($id) {
    $field = $this->load_field($id);
    $this->load->template("templates/general", "fields/edit", Iconstant::MPI_APP_NAME, array("field" => $field));
  }

  function update($id){
    $field = $this->load_field($id);

    if($field->update_attributes($this->field_params())){
      Isession::setFlash("success", "Field has been successfully updated");
      redirect(site_url("fields/index"));
    }
    else{
      $this->load->template("templates/general", "fields/edit", Iconstant::MPI_APP_NAME, array("field" => $field));
    }
  }

  function seed_static_fields(){
    // if(!$this->input->is_cli_request())
    //   return;

    $options = array(
      array("name" => "pat_id", "code" => "pat_id", "type" => "String"),
      array("name" => "pat_gender", "code" => "pat_gender", "type" => "Integer"),
      array("name" => "pat_dob", "code" => "pat_dob", "type" => "Date"),
      array("name" => "pat_age", "code" => "pat_age", "type" => "Integer" ),
      array("name" => "date_create", "code" => "date_create", "type" => "DateTime" ),
      array("name" => "pat_version", "code" => "pat_version", "type" => "String" ),
      array("name" => "pat_register_site", "code" => "pat_register_site", "type" => "String" ),
      array("name" => "new_pat_id", "code" => "new_pat_id", "type" => "String" ),


      array("name" => "fingerprint_l1", "code" => "fingerprint_l1", "type" => "String" ),
      array("name" => "fingerprint_l2", "code" => "fingerprint_l2", "type" => "String" ),
      array("name" => "fingerprint_l3", "code" => "fingerprint_l3", "type" => "String" ),
      array("name" => "fingerprint_l4", "code" => "fingerprint_l4", "type" => "String" ),
      array("name" => "fingerprint_l5", "code" => "fingerprint_l5", "type" => "String" ),
      array("name" => "fingerprint_r1", "code" => "fingerprint_r1", "type" => "String" ),
      array("name" => "fingerprint_r2", "code" => "fingerprint_r2", "type" => "String" ),
      array("name" => "fingerprint_r3", "code" => "fingerprint_r3", "type" => "String" ),
      array("name" => "fingerprint_r4", "code" => "fingerprint_r4", "type" => "String" ),
      array("name" => "fingerprint_r5", "code" => "fingerprint_r5", "type" => "String" )
    );

    foreach($options as $field_attrs) {
      $field_attrs['dynamic_field'] = 0;
      $field_attrs['is_encrypted'] = 0;

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

  function delete($id) {
    $field = Field::find($id);

    if($field && $field->delete())
      Isession::setFlash("success", "Field has been removed");
    else
      Isession::setFlash("error", "Failed to remove field");
    redirect(site_url("fields/index"));
  }

  function field_params(){
    return $this->filter_params(array("name", "code", "type", "is_encrypted"));
  }

  function load_field($id) {
    $field = Field::find($id);
    if($field->dynamic_field == 0)
      throw new Exception("Invalid field", $id);
    return $field;
  }
}
