<?php

class Passwords extends MpiController {

  function before_action(){
    parent::before_action();
    $this->load->model("usermodel");
  }
  function edit() {
    $this->render_view();
  }

  function update() {
    $current_user = $this->auth->current_user();
    $this->view_params->bind($_POST);

    $user = $this->usermodel->authentication($current_user["user_login"], $this->view_params->get("old_password"));
    if ($user == null)
      Isession::setFlash("error", "Old Password is not correct");

    else if ($this->view_params->get("new_password") != $this->view_params->get("confirm_password"))
      Isession::setFlash("error", "Password and Confirm Password did not match");

    else if(trim($this->view_params->get("new_password")) == "")
      Isession::setFlash("error", "New password can not be blank");

    else {
      $updater = $current_user["user_id"];
      $update_user = $current_user["user_id"];
      $update_password = $this->view_params->get("new_password");
      $this->usermodel->update_pwd($update_user, $update_password, $updater);
      Isession::setFlash("success", "Password changed successfully");
    }

    $this->render_view("edit");
  }
}
