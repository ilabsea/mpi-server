<?php
class Users extends MpiController {
  function before_action(){
    $this->required_admin_user();
  }

  function index() {
    $users = User::all();
    $this->set_view_variables(array("users" => $users));
    $this->render_view();
  }

  function add() {
    $user = new User();
    $this->set_view_variables(array("user" => $user));
    $this->render_view();
  }

  function create() {
    $current_user = $this->current_user();
    $attrs = $this->filter_params(array("user_login", "user_lname", "user_fname",
                                         "user_email", "grp_id", "user_pwd"));
    $attrs["user_create"] = $current_user->id();

    $user = new User($attrs);
    if($user->save()){
      Isession::setFlash("success", "User was successfully created");
      redirect(site_url("users/index"));
    }
    else{
      $this->set_view_variables(array("user" => $user));
      $this->render_view("add");
    }
  }

  function edit($id) {
    $user = User::find($id);
    $this->set_view_variables(array("user" => $user));
    $this->render_view();
  }

  function update($id) {
    $current_user = $this->current_user();

    $attrs = $this->filter_params(array("user_login", "user_lname", "user_fname",
                                         "user_email", "grp_id"));
    $attrs["user_update"] = $current_user->id();
    $user = User::find($id);

    if($user->save()){
      Isession::setFlash("success", "User was successfully updated");
      redirect(site_url("users/index"));
    }
    else {
      Isession::setFlash("error", "Failed to update user");
      $this->set_view_variables(array("user"=> $user));
      $this->render_view("edit");
    }
  }

  function delete($id) {
    $user = User::find($id);
    if($user->delete())
      Isession::setFlash("success", "User have been deleted");
    else
      Isession::setFlash("error", "Failed to delete user");
    redirect(site_url("users/index"));
  }

  function regenerate_pwd($id) {
    $user = User::find($id);
    $new_pwd = uniqid();

    $user->set_attribute("user_pwd",$new_pwd);
    if($user->save()){
      $this->set_view_variables(array("new_pwd" => $new_pwd, "user" => $user));
      $this->render_view("show");
    }
    else {
      Isession::setFlash("error", "Failed to generate user password");
      redirect(site_url("users/edit/{$id}"));
    }
  }
}
