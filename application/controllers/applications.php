<?php
class Applications extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();

    $this->load->model("application");
    $this->load->model("scope");
  }

  function index() {
    $paginate_applications = Application::paginate(array(), "status DESC, name ASC");

    $this->set_view_variables(array(
      "scopes" => Scope::mapper(),
      "paginate_applications" => $paginate_applications
    ));
    $this->render_view();
  }

  function show($id) {
    $application = Application::find($id);
    $this->set_view_variables(array("scopes" => Scope::mapper(), "application" => $application));
    $this->render_view();
  }

  function add() {
    $application = new Application();

    $this->set_view_variables(array("scopes" => Scope::mapper(), "application" => $application));
    $this->render_view();
  }

  function create(){
    $application = new Application();
    $application->set_attributes($this->application_params());

    if($application->save()) {
      Isession::setFlash("success", "Application has been successfully created");
      redirect(site_url("applications/index"));
    }
    else{
      $this->set_view_variables(array( "scopes" => Scope::mapper(), "application" => $application));
      $this->render_view("add");
    }
  }

  function edit($id) {
    $application = Application::find($id);
    $this->set_view_variables(array("scopes" => Scope::mapper(), "application" => $application));
    $this->render_view();
  }

  function update($id){
    $application = Application::find($id);
    if($application->update_attributes($this->application_params())){
      Isession::setFlash("success", "Application has been successfully updated");
      redirect(site_url("applications/index"));
    }
    else{
      $this->set_view_variables(array("scopes" => Scope::mapper(), "application" => $application));
      $this->render_view("edit");
    }
  }

  public function application_params(){
    return $this->filter_params(array("name","scope_id", "whitelist", "status", "internal_app"));
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
