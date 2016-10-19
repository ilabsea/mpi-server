<?php
class MpiController extends BaseController {

	/**
	 * The construction of this controller
	 * @param boolean $load_fingerprint: if we need to reload the fingerprint SDK
	 * @param boolean $init_session: if we need to create session
	 */
    function __construct($load_fingerprint=false, $init_session=true) {
      parent::__construct();
      if ($load_fingerprint)
        require FCPATH.'application/libraries/GrFingerService.php';
    }

    function before_action() {
      $this->load->helper("html");
      $this->load->helper('url');
      $this->load->helper('form');
      $this->load->library('form_validation');

      require_once BASEPATH.'core/model.php';
      require_once APPPATH.'libraries/Imodel.php';
      require_once APPPATH.'libraries/Isession.php';
      require_once APPPATH.'libraries/Iencryption.php';
      require_once APPPATH.'libraries/Iconstant.php';
      require_once APPPATH.'libraries/ILog.php';
      require_once APPPATH.'helpers/mpi_helper.php';

      ILog::getInstance();
      $session_status = Isession::initializeSession();
      $this->controlUser($session_status);

      $this->require_admin_access();
    }

    function require_admin_access() {
      $user = Isession::getUser();
      if ($user["grp_id"] != Iconstant::USER_ADMIN)
        redirect(site_url("main/errorpage"));
    }

    private function controlUser($session_status) {
      $uri = trim(uri_string(), "/");
      $user = Isession::getUser();
      //$this->load_language("common");
      if ($user != null) {
        if ($session_status == 1) {
          Isession::destroy();
          Isession::initializeSession(false);
          Isession::setFlash("message", "Session Timeout");
          redirect(site_url("main/login"));
        }
        else if ($uri == "")
          redirect("main");
      }
      else if ($uri == "" || $uri == "main/login" || $uri == "main/authentication")
        ; //nothing
      else
        redirect(site_url());
    }
}
