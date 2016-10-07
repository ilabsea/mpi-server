<?php
/**
 * Users controller
 * @author Sokha RUM
 */
ini_set("display_errors", 2);
class Fields extends MpiController {
  public $data = array();
  public $model = null;

  function __construct($load_fingerprint=false, $init_session=true){
    parent::__construct($load_fingerprint=false, $init_session=true);
    $this->before_action();
  }

  function before_action() {
    $this->require_admin_access();
    $this->load->model("field");
    $this->model = $this->field;

    $this->data["error"] = Isession::getFlash("error");
    $this->data["error_list"] = Isession::getFlash("error_list");
    $this->data["success"] = Isession::getFlash("success");
  }

  function record_validation_rules(){
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('code', 'Code', 'trim|required');
    $this->form_validation->set_rules('type', 'Type', 'required');
  }

  function index() {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $fields = array();
    $this->load->template("templates/general", "fields/index", Iconstant::MPI_APP_NAME, array("fields" => $fields));
  }

  function add() {
    $field = new Field();
    $this->load->template("templates/general", "fields/add", Iconstant::MPI_APP_NAME, array("field" => $field));
  }

  function create(){
    $field = new Field();
    $filter_params = $this->filter_params(array("name", "code", "type", "is_encrypted"));
    $field->build($filter_params);

    if($field->save()) {
      // Isession::setFlash("success", "User was successfully created");
      redirect(site_url("fields/index"));
    }
    else{
      $this->load->template("templates/general", "fields/add", Iconstant::MPI_APP_NAME, array("field" => $field));
    }
  }

  /**
  * Create a new user
  */
  function save() {
    $this->data = array();
    $this->data["user_login"] = trim($_POST["user_login"]);
    $this->data["user_lname"] = trim($_POST["user_lname"]);
    $this->data["user_fname"] = trim($_POST["user_fname"]);
    $this->data["user_email"] = trim($_POST["user_email"]);
    $this->data["grp_id"] = $_POST["grp_id"];

    if ($this->form_validation->run() == FALSE){
      $this->form_validation->set_error_delimiters("<li>", "</li>");
    }

    $this->load->model("usermodel");
    $user_found = $this->usermodel->user_by_login($this->data["user_login"]);
    if ($user_found != null) :
    	Isession::setFlash("user_data", $this->data);
    	Isession::setFlash("error_list", "<ul><li>User with login ".$this->data["user_login"]." already exist</li><ul>");
          redirect("users/usernew");
          return;
    endif;

    if ($_POST["user_pwd"] != $_POST["user_confirm_pwd"]){
      Isession::setFlash("user_data", $this->data);
      Isession::setFlash("error_list", "<ul><li>Confirm Password is not correct</li><ul>");
      redirect("users/usernew");
      return;
    }
    $this->data["user_pwd"] = $_POST["user_pwd"];
    $this->data["user_create"] = $cur_user["user_id"];
    $this->usermodel->user_new($this->data);

    redirect(site_url("users/userlist"));
  }


}
