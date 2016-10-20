<?php
class MpiAuthHelper {
  function current_user(){
    $user = Isession::getUser();
    return $user;
  }

  function sign_in($user) {
    Isession::setUser($user);
  }

  function sign_out(){
    Isession::destroy();
  }

  function user_signed_in(){
    return $this->current_user();
  }
}
