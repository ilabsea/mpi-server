<?php
/**
 * Users controller
 * @author Sokha RUM
 */

class Applications extends MpiController {

  function __construct($load_fingerprint=false, $init_session=true){
    parent::__construct($load_fingerprint=false, $init_session=true);
    $this->before_action();
  }

  function before_action() {
    $this->require_admin_access();
    $this->load->model("application");
    $this->load->model("scope");
  }

  function build_params($view_datas) {
    $applications = Scope::mapper();
    $view_datas["scopes"] = $applications;
    return $view_datas;
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $applications = Application::all(array(), $page, "status DESC, name ASC");
    $this->load->template("templates/general", "applications/index", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("applications" => $applications, "page" => $page)));
  }

  function add() {
    $application = new Application();
    $this->load->template("templates/general", "applications/add", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("application" => $application)));
  }

  function create(){
    $application = new Application();
    $application->set_attributes($this->application_params());

    if($application->save()) {
      Isession::setFlash("success", "Application has been successfully created");
      redirect(site_url("applications/index"));
      return;
    }
    else{
      $this->load->template("templates/general", "applications/add", Iconstant::MPI_APP_NAME,
                            $this->build_params(array("application" => $application)));
    }
  }

  function edit($id) {
    $application = Application::find($id);
    $this->load->template("templates/general", "applications/edit", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("application" => $application)));
  }

  function show($id) {
    $application = Application::find($id);
    $this->load->template("templates/general", "applications/show", Iconstant::MPI_APP_NAME,
                          $this->build_params(array("application" => $application)));
  }

  function update($id){
    $application = Application::find($id);
    if($application->update_attributes($this->application_params())){
      Isession::setFlash("success", "Application has been successfully updated");
      redirect(site_url("applications/index"));
    }
    else{
      $this->load->template("templates/general", "applications/edit", Iconstant::MPI_APP_NAME,
                            $this->build_params(array("application" => $application)));
    }
  }

  public function application_params(){
    return $this->filter_params(array("name","scope_id", "whitelist", "status"));
  }

  function delete($id) {
    $application = Application::find($id);

    if($application && $application->delete())
      Isession::setFlash("success", "Application has been removed");
    else
      Isession::setFlash("error", "Failed to remove scope");
    redirect(site_url("applications/index"));
  }
}
