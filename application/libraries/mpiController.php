<?php
/**
 * 
 * The MPI main controller
 * @author Sokha RUM
 *
 */
class MpiController extends CI_Controller {
	
	/**
	 * The construction of this controller
	 * @param boolean $load_fingerprint: if we need to reload the fingerprint SDK
	 * @param boolean $init_session: if we need to create session
	 */
    function __construct($load_fingerprint=false, $init_session=true) {
        parent::__construct();
        if ($load_fingerprint) :
        	require FCPATH.'application/libraries/GrFingerService.php';
        endif;
        $this->initController($init_session);
    }  

    /**
     * Initialize the session
     * @param boolean $init_session: if we need to create session
     */
    private function initController($init_session) {
        $CI =& get_instance();
		define("CHAR_SET", $CI->config->item("charset"));
		$this->load->helper("html");
        $this->load->helper('url');
        
        /**  load the library */
        require_once BASEPATH.'core/model.php';
        require_once APPPATH.'libraries/Imodel.php';
        require_once APPPATH.'libraries/Isession.php';
        require_once APPPATH.'libraries/Iencryption.php';
        require_once APPPATH.'libraries/Iconstant.php';
        require_once APPPATH.'libraries/ILog.php';
        require_once APPPATH.'helpers/mpi_helper.php';
        ILog::getInstance();
        
        if ($init_session) :
	        $session_status = Isession::initializeSession();
	        $this->controlUser($session_status);
        endif;
    }
    
    /**
     * @param integer $session_status : the status of the session
     */
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
	    		return;
    	    } 
            if ($uri == "") {
            	redirect("main");
            	return;
            }
        } else if ($uri == "" || $uri == "main/login" || $uri == "main/authentication") {
        	//Nothing
        } else {
           redirect(site_url());
           return;
        }
    }
}
