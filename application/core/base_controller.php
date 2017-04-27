<?php
class BaseController extends CI_Controller {
  var $response_content = "";
  var $skip_before_action = array();

  function __construct() {
    parent::__construct();
    $CI =& get_instance();

    define("CHAR_SET", $CI->config->item("charset"));

    require_once APPPATH.'libraries/http_status_code.php';
    require_once APPPATH.'libraries/ILog.php';
    session_start();

    $this->init();
    $this->callback();

    set_exception_handler(array($this, 'catch_exception'));
  }

  public function catch_exception($exception) {
    ILog::d("Application raise ".get_class($exception). " with: ", $exception->getMessage());
  }

  function action_name(){
    return $this->router->fetch_method();
  }

  function log_request(){
    if(!$this->allow_log_request())
      return;

    $params = $_REQUEST;
    $message = json_encode($params);
    $request_type = $_SERVER['REQUEST_METHOD'];
    $url =  $this->router->fetch_class(). "/".$this->router->fetch_method();

    log_message('info', "REQUEST $request_type $url \n Params: $message");
  }

  function log_response(){
    if(!$this->allow_log_response())
      return false;
    log_message("info", "RESPONSE: " . $this->response_content);
  }

  function allow_log_request(){
    return true;
  }

  function allow_log_response(){
    return true;
  }

  //to init you class, lib
  function init() {
    $this->load->helper("html");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->library('form_validation');

    require_once BASEPATH.'core/Model.php';

    $this->load_dir(APPPATH."core");
    $this->load_dir(APPPATH."helpers");
    $this->load_dir(APPPATH."libraries");
    $this->load_dir(APPPATH."models");
    $this->load_dir(APPPATH."exceptions");
  }

  function load_dir($path, $except='') {
    $files = scandir($path);
    foreach($files as $file){
      if(substr($file, -4, 4) == '.php' && $file != $except){
        require_once "{$path}/{$file}";
      }
    }
  }

  //override this in your controller
  function before_action(){
    $this->log_request();
  }
  function after_action(){
    $this->log_response();
  }

  function callback() {

    $action = $this->router->fetch_method();

    if(is_string($this->skip_before_action) && $this->skip_before_action != "*"){
      $this->before_action();
    }


    else if(is_array($this->skip_before_action) && !in_array($action, $this->skip_before_action)){
      $this->before_action();
    }
  }

  function filter_params($keys, $type=''){
    $result = array();
    $params = $_REQUEST;

    if(is_array($type))
      $params = $type;

    else if(strtolower($type) == 'get')
      $params = $_GET;
    else if (strtolower($type) == 'post')
      $params = $_POST;

    foreach($keys as $key){
      if(isset($params[$key]))
        $result[$key] = $params[$key];
      else
        $result[$key] = null;
    }
    return $result;
  }

  function ensure_field_exist($params, $field_name){
    if(!isset($params[$field_name]) || !$params[$field_name]){
      $errors = array(
        "error" => "Bad request",
        "error_description" => "You must provide {$field_name} in your request"
      );
      return $this->render_bad_request($errors);
    }
  }

  function render_json($object, $status=200) {
    header('Content-Type: application/json;  charset=UTF-8');
    // Content-type: application/json; charset=utf-8
    $this->http_response_code($status);
    $this->response_content = json_encode($object, JSON_UNESCAPED_UNICODE);
    $this->after_action($status);
    echo $this->response_content;
    exit;
  }

  function render_record_errors($active_record) {
    $errors = array("error" => "Validation errors",
                    "error_description" => $active_record->get_errors());
    $this->render_bad_request($errors);
  }

  function render_error($errors, $status) {
    $this->render_json($errors, $status);
  }

  function render_bad_request($errors) {
    $this->render_json($errors, 400);
  }

  function render_unauthorized($errors) {
    $this->render_error($errors, 401);
  }

  function render_record_not_found($errors) {
    $this->render_error($errors, 404);
  }

  function http_response_code($status){
    $status_code_description = $this->status_code_description($status);
    header("HTTP/1.1 {$status_code_description}", true, $status);
  }

  function status_code_description($status){
    $meanings = array(
      100 => 'Continue',
      101 => 'Switching Protocols',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Time-out',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Large',
      415 => 'Unsupported Media Type',
      416 => 'Requested range not satisfiable',
      417 => 'Expectation Failed',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Time-out'
    );
    return $status. " ".$meanings[$status];
  }
}
