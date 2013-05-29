<?php
/**
 * MPI Log: for storing the log
 * @author Sokha RUM
 */
class ILog extends CI_Log {
	/** the instance of ILog */
	private static $ILog = null;
	
	/**
	 * Contructor of this class
	 */
	function __construct() {
	    parent::__construct();
	    $this->_log_path .= "MPI_";
	}
	
	/** get the instance of ILog */
	static function getInstance () {
	    if (ILog::$ILog == null) {
	        ILog::$ILog = new ILog();
	    }
	    return ILog::$ILog;
	}
	
	/**
	 * write the information 
	 * @param string $msg
	 */
	static function info($msg) {
	    ILog::$ILog->write_log('info', $msg);
	}
	
	/**
	 * write the debug message
	 * @param string $msg
	 */
	static function debug($msg) {
		ILog::$ILog->write_log('debug', $msg);
	}
	
	/**
	 * write the error message 
	 * @param string $msg
	 */
	static function error($msg) {
		ILog::$ILog->write_log('error', $msg);
	}
	
	/**
	 * Set the path for the log files
	 * @param string $path
	 */
	static function setPath($path) {
		if ($path == "") :
			return;
		endif;
		$config =& get_config();
		
		ILog::$ILog->_log_path = ($config['log_path'] != '') ? $config['log_path']."/".$path."/" : APPPATH.'logs/'.$path."/";
		if (!is_dir(ILog::$ILog->_log_path)) :
			mkdir(ILog::$ILog->_log_path, 0777, true);
		endif;
		ILog::$ILog->_log_path .= "MPI_";
	} 
}