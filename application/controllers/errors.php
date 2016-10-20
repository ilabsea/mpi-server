<?php
class Errors extends MpiController {
  var $skip_before_action = "*";

  function index() {
    $this->render_view();
  }
}
