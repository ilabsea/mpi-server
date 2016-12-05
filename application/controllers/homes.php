<?php

class Homes extends MpiController {
  var $skip_before_action = "*";

  function console(){
    $patient_ids = array("KH002100000001" , "KH002100000002" );
    $visits = PatientModule::patient_visits($patient_ids);
    ILog::debug_message("visits", $visits,1 ,1);
  }

  function index() {
    $params = array(
      "pat_register_site" => "0202",
      "pat_age" => 30,
      "fingerprint_r1" => "fpr1",
      "fingerprint_l1" => "fpl1",
      "pat_gender" => 1,
      "is_referred" => false,
      "p_is_referral" => true,
      "v_tb" => "positve"
    );

    $patient_params = Patient::field_params($params);
    $patient_dynamic_params = Patient::dynamic_field_params($params);
    ILog::debug_message("params", $patient_params);
    ILog::debug_message("dynamic params", $patient_params);

    // $params = array(
    //   "pat_register_site" => "0202",
    //   "pat_age" => 30,
    //   "fingerprint_r1" => "fpr1",
    //   "fingerprint_l1" => "fpl1",
    //   "pat_gender" => 1,
    // );
    // $exclude_patient_ids = array("'KH002100003193'", "'KH002100000002'", "'KH002100000051'");
    // $patients = PatientModule::patients($params);
    // ILog::debug_message("patients", $patients, 1, 1);
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
