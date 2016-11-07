<?php
class MpiAuthHelper {
  var $user = null;
  function current_user(){
    if($this->user)
      return $this->user;

    if(isset($_SESSION['current_user_id'])){
      $user_id = $_SESSION['current_user_id'];
      $this->user = User::find($user_id);
      return $this->user;
    }
    return null;
  }

  function sign_in($user) {
    $this->user = $user;
    $_SESSION['current_user_id'] = $user->id();
  }

  function sign_out(){
    unset($_SESSION['current_user_id']);
    $this->user = null;
  }

  function user_signed_in(){
    return $this->current_user();
  }
}
