<?php

class ILog extends CI_Log {
  private static $ILog = null;

  function __construct() {
    parent::__construct();
    $this->_log_path .= "MPI_";
  }

  static function debug_message($message, $var,$exit=false, $html_format = true){
    $html = <<<EOT
     <div style='text-align:left;border-top:1px solid #ccc;background-color:white;color:black;overflow:auto;' >
         <pre>
             <br /> <strong> line : </strong> {line}
             <br /> <strong> file : </strong> {file}
             <br /> {message}
             <br /> {data}
         </pre>
     </div>
EOT;

    $console = <<<EOT
         \n
         -------------------------------debug ---------------------------
         line : {line}, file : {file}
         output: ->
         {data}
         ----------------------------------------------------------------
         \n\r
EOT;


    $debug_traces = debug_backtrace();
    $debug_trace = $debug_traces[0];

    $format = $html_format ? $html: $console ;
    $str = strtr($format,
       array( "{line}"=>"{$debug_trace['line']}",
               "{file}" => "{$debug_trace['file']}",
               "{message}" => $message,
               "{data}"=> print_r($var, true) //debug_trace['args'][1]
            ));
    echo $str ;
    if($exit==true)
      exit;
  }

  static function getInstance () {
    if (ILog::$ILog == null)
      ILog::$ILog = new ILog();
    return ILog::$ILog;
  }

  static function info($msg) {
    ILog::$ILog->write_log('info', $msg);
  }

  static function debug($msg) {
    ILog::$ILog->write_log('debug', $msg);
  }

  static function error($msg) {
    ILog::$ILog->write_log('error', $msg);
  }

  static function setPath($path) {
    if($path == "")
      return;
    $config =& get_config();
    $config_path = $config['log_path'];
    ILog::$ILog->_log_path = $config_path != '' ? "{$config_path}/{$path}/" : APP_PATH."logs/{$path}/" ;

    if (!is_dir(ILog::$ILog->_log_path))
      mkdir(ILog::$ILog->_log_path, 0777, true);

    ILog::$ILog->_log_path .= "MPI_";
  }
}
