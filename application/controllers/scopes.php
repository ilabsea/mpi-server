<?php
/**
 * Users controller
 * @author Sokha RUM
 */

class Scopes extends MpiController {

  function before_action() {
    parent::before_action();
    $this->load->model("scope");
    $this->load->model("field");
  }

  function build_params($payload) {
    $fields = Field::mapper();
    $payload["fields"] = $fields;
    return $payload;
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $scopes = Scope::all(array(), $page, "name ASC");
    $this->load->template("templates/general", "scopes/index", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("scopes" => $scopes, "page" => $page)));
  }

  function add() {
    $scope = new Scope();
    $this->load->template("templates/general", "scopes/add", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("scope" => $scope)));
  }

  function create(){
    $scope = new Scope();
    $scope->set_attributes($this->scope_params());

    if($scope->save()) {
      Isession::setFlash("success", "Scope has been successfully created");
      redirect(site_url("scopes/index"));
      return;
    }
    else{
      $this->load->template("templates/general", "scopes/add", Iconstant::MPI_APP_NAME,
                            $this->build_params(array("scope" => $scope)));
    }
  }

  function edit($id) {
    $scope = Scope::find($id);
    $this->load->template("templates/general", "scopes/edit", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("scope" => $scope)));
  }

  function update($id){
    $scope = Scope::find($id);
    if($scope->update_attributes($this->scope_params())){
      Isession::setFlash("success", "Scope has been successfully updated");
      redirect(site_url("scopes/index"));
    }
    else{
      $this->load->template("templates/general", "scopes/edit", Iconstant::MPI_APP_NAME,
                            $this->build_params(array("scope" => $scope)));
    }
  }

  public function scope_params(){
    $params =  $this->filter_params(array("name", "searchable_fields", "updatable_fields"));
    if(!isset($params['updatable_fields']))
      $params['updatable_fields'] = array();
    if(!isset($params['searchable_fields']))
      $params['searchable_fields'] = array();
    return $params;
  }

  function delete($id) {
    $scope = Scope::find($id);

    if($scope && $scope->delete())
      Isession::setFlash("success", "Scope has been removed");
    else
      Isession::setFlash("error", "Failed to remove scope");
    redirect(site_url("scopes/index"));
  }
}
