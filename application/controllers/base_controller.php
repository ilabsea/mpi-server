<?php
class BaseController extends CI_Controller {
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
  }

  //to init you class, lib
  function init() {
  }

  //override this in your controller
  function before_action(){}
  function after_action(){}

  function callback() {

    $action = $this->router->fetch_method();

    if(is_string($this->skip_before_action) && $this->skip_before_action != "*"){
      $this->before_action();
    }


    else if(is_array($this->skip_before_action) && !in_array($action, $this->skip_before_action)){
      $this->before_action();
    }
  }

  function filter_params($keys){
    $result = array();
    foreach($keys as $key){
      if(isset($_REQUEST[$key]))
        $result[$key] = $_REQUEST[$key];
      else
        $result[$key] = null;
    }
    return $result;
  }

  function render_json($object, $status=200) {
    header('Content-Type: application/json');
    $this->http_response_code($status);
    $json_content = json_encode($object);
    $this->after_action($status);
    echo $json_content;
    exit;
  }

  function render_error($errors, $status) {
    $this->render_json($errors, $status);
  }

  function render_unauthorized($errors) {
    $this->render_error($errors, 401);
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
