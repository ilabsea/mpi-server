<?php

class Passwords extends MpiController {

  function before_action(){
    parent::before_action();
  }
  function edit() {
    $this->render_view();
  }

  function update() {
    $current_user = $this->auth->current_user();
    $params = $this->filter_params(array("user_login", "old_password", "new_password", "confirm_password"));

    if (!$current_user->validate_password($params["old_password"]))
      Isession::setFlash("error", "Old Password is not correct");
    else if ($params["new_password"] != $params["confirm_password"])
      Isession::setFlash("error", "Password and Confirm Password did not match");

    else if($params["new_password"] == "")
      Isession::setFlash("error", "New password can not be blank");

    else {
      $attrs = array("user_pwd" => User::hex_digest($params["new_password"]),
                     "user_update" => $current_user->id());

      $current_user->update_attributes($attrs);
      Isession::setFlash("success", "Password changed successfully");
    }

    $this->render_view("edit");
  }
}
