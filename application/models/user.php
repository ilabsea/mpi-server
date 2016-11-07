<?php
class User extends Imodel {
  var $user_id = null;
  var $user_login = null;
  var $user_lname = null;
  var $user_fname = null;
  var $user_pwd = null;
  var $grp_id = null;
  var $user_email = null;
  var $user_create = null;
  var $date_create = null;
  var $date_update = null;
  var $user_update = null;

  var $created_at = null;
  var $updated_at = null;

  var $user_confirm_pwd = null;

  const ADMIN = 1;
  const NORMAL = 2;

  static function timestampable() {
    return true;
  }

  static function virtual_fields(){
    return array("user_confirm_pwd");
  }

  static function primary_key() {
    return "user_id";
  }

  static function table_name() {
    return "users";
  }

  static function class_name(){
    return 'User';
  }

  static function groups(){
    $groups = array();
    $groups[User::NORMAL] = "Normal";
    $groups[User::ADMIN] = "Admin";
    return $groups;
  }

  function is_admin(){
    return $this->grp_id == User::ADMIN;
  }

  static function hex_digest($text){
    return sha1($text);
  }

  function before_create(){
    $this->user_pwd = User::hex_digest($this->user_pwd);
    $this->date_update = $this->current_time();
  }

  function before_update(){
    $this->date_update = $this->current_time();
  }

  function full_name(){
    return $this->user_fname." ".$this->user_lname;
  }

  function group_name(){
    $groups =  User::groups();
    return $groups[$this->grp_id];
  }

  function validation_rules(){
    $login_uniqueness = $this->uniqueness_field('user_login');
    $this->form_validation->set_rules("user_login", "Login", "trim|required|alpha_numeric|$login_uniqueness");
    $this->form_validation->set_rules("user_lname", "Last name", "trim|required");
    $this->form_validation->set_rules("user_fname", "First name", "trim|required");
    $this->form_validation->set_rules("user_email", "Email", "trim|valid_email");

    if($this->new_record()){
      $this->form_validation->set_rules("user_pwd", "Password", "required|matches[user_confirm_pwd]");
    }
    return true;
  }

  static function authenticate ($user_login, $user_pwd) {
    $user = User::find_by(array("user_login" => $user_login));
    if($user && $user->validate_password($user_pwd) )
      return $user;
    return null;
  }

  function validate_password($user_pwd) {
    return $this->user_pwd == User::hex_digest($user_pwd);
  }
}
