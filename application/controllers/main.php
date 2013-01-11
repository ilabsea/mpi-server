<?php
/**
 * Enter description here ...
 * @author Sokha RUM
 */
class Main extends MpiController {
	/**
	 * Login sceen
	 * @author Sokha RUM
	 */
    function login() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	$this->load->template("templates/login", "main/login", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Authentication action
     * @author Sokha RUM
     */
    function authentication() {
        $this->load->model("usermodel");
        //TODO 
        $user = $this->usermodel->authentication($_POST["user_login"], $_POST["user_pwd"]);
        if ($user == null) :
            Isession::setFlash("error", "Login or Password is not correct");
            redirect(site_url("main/login"));
            return;
        else:
            Isession::setUser($user);
            redirect("main");
        endif;
    }
    
    /**
     * Default method of this controller
     */
    function index() {
    	$data = array();
    	Isession::removeAllCriteria();
        $this->load->template("templates/general", "main/homepage", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Logout function
     */
    function logout() {
        Isession::destroy();
        redirect(site_url());
    }
    
    /**
     * Change Password function 
     */
    function changepwd() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
        $this->load->template("templates/general", "main/changepwd", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Change Password
     */
    function changepwdsave() {
        $cur_user = Isession::getUser();
        $this->load->model("usermodel");
        
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules("user_pwd", "Password", "required");
        $this->form_validation->set_rules("user_new_pwd", "New Password", "required");
        
        $error = "";
        if ($this->form_validation->run() == FALSE) {
   	        $this->form_validation->set_error_delimiters("<li>", "</li>");
		    $error = validation_errors();
	    }
	    
	    if ($error != null) :
	        Isession::setFlash("error_list", "<ul>".$error."<ul>");
            redirect("main/changepwd");
            return;
	    endif;
        
        $user = $this->usermodel->authentication($cur_user["user_login"], $_POST["user_pwd"]);
        if ($user == null) :
            Isession::setFlash("error_list", "<ul><li>Password is not correct</li></ul>");
            redirect("main/changepwd");
            return;
        endif;
        
        if ($_POST["user_new_pwd"] != $_POST["user_confirm_pwd"]) :
            Isession::setFlash("error_list", "<ul><li>Password confirmation is not correct</li></ul>");
            redirect("main/changepwd");
            return;
        endif;
        
        $this->usermodel->update_pwd($cur_user["user_id"], $_POST["user_new_pwd"]);
        Isession::setFlash("success", "Password changed successfully");
        redirect("main/changepwd");
    }
}