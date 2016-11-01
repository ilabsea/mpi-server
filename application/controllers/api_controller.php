<?php
class ApiController extends BaseController {
  var $oauth = null;
  function init(){
    parent::init();

    $this->load->helper("html");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->library('form_validation');

    require_once BASEPATH.'core/model.php';

    require_once APPPATH.'libraries/Imodel.php';
    require_once APPPATH.'libraries/api_oauth_helper.php';

    require_once APPPATH.'models/application.php';
    require_once APPPATH.'models/application_token.php';
    require_once APPPATH.'models/scope.php';

    $this->oauth = new ApiOauthHelper();
  }
}
