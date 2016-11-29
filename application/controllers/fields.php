<?php
class Fields extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
    $this->load->model("field");
  }

  function index() {
    $paginate_fields = Field::paginate(array(), "dynamic_field DESC, name ASC");
    $this->set_view_variables(array("paginate_fields" => $paginate_fields));
    $this->render_view();
  }

  function add() {
    $field = new Field();
    $this->set_view_variables(array("field" => $field));
    $this->render_view();
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
      $this->set_view_variables(array("field" => $field));
      $this->render_view("add");
    }
  }

  function edit($id) {
    $field = $this->load_field($id);
    $this->set_view_variables(array("field" => $field));
    $this->render_view();
  }

  function update($id){
    $field = $this->load_field($id);

    if($field->update_attributes($this->field_params())){
      Isession::setFlash("success", "Field has been successfully updated");
      redirect(site_url("fields/index"));
    }
    else{
      $this->set_view_variables(array("field" => $field));
      $this->render_view("edit");
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
    return $this->filter_params(array("name", "code", "type", "is_encrypted", "table_type"));
  }

  function load_field($id) {
    $field = Field::find($id);
    if($field->dynamic_field == 0)
      throw new Exception("Invalid field", $id);
    return $field;
  }

}
