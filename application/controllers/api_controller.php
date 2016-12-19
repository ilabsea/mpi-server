<?php
class ApiController extends BaseController {
  var $oauth = null;
  function init(){
    parent::init();
    $this->oauth = new ApiOauthHelper();
  }
}
