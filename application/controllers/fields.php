<?php
/**
 * Users controller
 * @author Sokha RUM
 */

class Fields extends MpiController {
  public $data = array();
  public $model = null;

  function __construct($load_fingerprint=false, $init_session=true){
    parent::__construct($load_fingerprint=false, $init_session=true);
    $this->before_action();
  }

  function before_action() {
    $this->require_admin_access();
    $this->load->model("field");
    $this->model = $this->field;
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $fields = Field::all(array(), $page, "name ASC");

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
    $filter_params = $this->filter_params(array("name", "code", "type", "is_encrypted"));
    $field->build($filter_params);

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
    $field = Field::find($id);
    $this->load->template("templates/general", "fields/edit", Iconstant::MPI_APP_NAME, array("field" => $field));
  }

  function update($id){
    $field = Field::find($id);
    $filter_params = $this->filter_params(array("name", "code", "type", "is_encrypted"));
    ILog::debug_message($filter_params);

    if($field->update_attributes($filter_params)){
      Isession::setFlash("success", "Field has been successfully updated");
      redirect(site_url("fields/index"));
    }
    else{
      $this->load->template("templates/general", "fields/edit", Iconstant::MPI_APP_NAME, array("field" => $field));
    }
  }

  function delete($id) {
    $field = Field::find($id);
    ILog::debug_message($field);

    if($field && $field->delete())
      Isession::setFlash("success", "Field has been removed");
    else
      Isession::setFlash("error", "Failed to remove field");
    redirect(site_url("fields/index"));
  }
}
