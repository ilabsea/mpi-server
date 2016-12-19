<?php
class Env {
  const NAME = "Development";

  static function is_development(){
    return Env::NAME == "Development";
  }

  static function is_dev() {
    return Env::is_development();
  }

  static function is_production() {
    return Env::NAME == "Production";
  }

  static function is_prod(){
    return Env::is_production();
  }
}
