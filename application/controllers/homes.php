<?php

class Homes extends MpiController {
  function index() {
    $this->render_view();
  }

  function changepwdsave() {
    $cur_user = Isession::getUser();
    $this->load->model("usermodel");

    $this->load->helper(array('form'));
    $this->load->library('form_validation');
    $this->form_validation->set_rules("user_pwd", "Password", "required");
    $this->form_validation->set_rules("user_new_pwd", "New Password", "required");

    $error = "";
    if ($this->form_validation->run() == FALSE) {
      $this->form_validation->set_error_delimiters("<li>", "</li>");
      $error = validation_errors();
    }

    if ($error != null){
      Isession::setFlash("error_list", "<ul>".$error."<ul>");
      redirect("main/changepwd");
      return;
    }

    $user = $this->usermodel->authentication($cur_user["user_login"], $_POST["user_pwd"]);
    if ($user == null){
      Isession::setFlash("error_list", "<ul><li>Password is not correct</li></ul>");
      redirect("main/changepwd");
      return;
    }

    if ($_POST["user_new_pwd"] != $_POST["user_confirm_pwd"]){
      Isession::setFlash("error_list", "<ul><li>Password confirmation is not correct</li></ul>");
      redirect("main/changepwd");
      return;
    }

    $this->usermodel->update_pwd($cur_user["user_id"], $_POST["user_new_pwd"], $cur_user["user_id"]);
    Isession::setFlash("success", "Password changed successfully");
    redirect("main/changepwd");
  }

  function errorpage() {
    $data = array();
    $data["error"] = "You do not have right to access this page";
    $this->load->template("templates/general", "main/errorpage", Iconstant::MPI_APP_NAME, $data);
  }
}
