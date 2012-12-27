<?php
/**
 * The ISession class
 * @author Sokha RUM
 */
class Isession {
	const SESSION_KEY = "I_MPI_SERVER"; 
	const SESSION_USER_KEY = "I_USER_KEY";
	const SESSION_DATA_KEY = "I_DATA_KEY";
	const SESSION_FLASH_KEY = "I_FLASH_KEY";
	const SESSION_CRITERIA_KEY = "I_CRITERIA_KEY";
	const TIMEOUT = 3600;

	/**
     * Initialize session
     * return 0 - Normal
     *        1 - Expired
     */

    static function initializeSession($start_session=true) {
    	if ($start_session) :
    		session_start();
    	endif;
    	if (!isset($_SESSION[Isession::SESSION_KEY])) {
            $_SESSION[Isession::SESSION_KEY] = array();
            $_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY] = array();
            $_SESSION[Isession::SESSION_KEY][Isession::SESSION_FLASH_KEY] = array();
            $_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY] = array();
        }
    	$uri = trim(uri_string(), "/");
    	if (!k_start_with($uri, "ajax/")) {
	        if (isset($_SESSION[Isession::SESSION_KEY]["CUR_URL"])) {
		    	$_SESSION[Isession::SESSION_KEY]["PRE_URL"] = $_SESSION[Isession::SESSION_KEY]["CUR_URL"];
		    }
		    $_SESSION[Isession::SESSION_KEY]["CUR_URL"] = cur_page_url();
		    /*if (isset($_SESSION[Isession::SESSION_KEY]["CUR_URI"])) {
		    	$_SESSION[Isession::SESSION_KEY]["PRE_URI"] = $_SESSION[Isession::SESSION_KEY]["CUR_URI"];
		    }
		    $_SESSION[Isession::SESSION_KEY]["CUR_URI"] = $uri;*/
    	}

    	if ($uri == "" || k_start_with($uri, "main") || k_start_with($uri, "main/index")) {
    		$_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY] = array();
    	}

    	if (!isset($_SESSION[Isession::SESSION_KEY]["CREATE"])) {
    		$_SESSION[Isession::SESSION_KEY]["CREATE"] = time();
		} else if (time() - $_SESSION[Isession::SESSION_KEY]["CREATE"] > Isession::TIMEOUT) {
	    	// session started more than 30 minates ago
    		//session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
    		//Isession::destroy();
    		$_SESSION[Isession::SESSION_KEY]["CREATE"];
    		$_SESSION[Isession::SESSION_KEY]["CREATE"] = time();
    		return 1;
		}
		$_SESSION[Isession::SESSION_KEY]["CREATE"] = time();
		return 0;
    }

	/**
	 * Set user to session
	 * @param unknown_type $user
	 */
	static function setUser($user) {
        $_SESSION[Isession::SESSION_KEY][Isession::SESSION_USER_KEY] =  Iencryption::encrypt(serialize($user)); 
    }

    /**
     * getting session from user
     */

	static function getUser() {
    	if (!isset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_USER_KEY])) { return null;} 
        return unserialize(Iencryption::decrypt($_SESSION[Isession::SESSION_KEY][Isession::SESSION_USER_KEY]));
    }

    /**
     * Setting data to session
     * @param unknown_type $key
     * @param unknown_type $data
     */

    static function setData($key, $data) {
        $_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY][$key] = $data;
    }

    /**
     * getting data from session
     * @param unknown_type $key
     */

	static function getData($key) {
        if (!isset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY][$key])) { return null;} 
        return $_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY][$key];
    }

    /**
     * Removing data from session
     * @param unknown_type $key
     */

    static function removeData($key) {
        unset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY][$key]);
    }

    /**
     * setting flash to session
     * @param unknown_type $key
     * @param unknown_type $flash
     */

    static function setFlash($key, $flash) {
    	$_SESSION[Isession::SESSION_KEY][Isession::SESSION_FLASH_KEY][$key] = $flash;
    }

    /**
     * getting flash from session
     * @param unknown_type $key
     */
	static function getFlash($key) {
    	if (!isset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_FLASH_KEY][$key])) { return null;} 
        $result = $_SESSION[Isession::SESSION_KEY][Isession::SESSION_FLASH_KEY][$key];
        unset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_FLASH_KEY][$key]);
        return $result;
    }


    /**
     * Destroy a session
     */
    static function destroy() {
    	unset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_DATA_KEY]);
    	unset($_SESSION[Isession::SESSION_KEY]);
    }

    /**
     * Getting the last page url
     */
    static function getLastPageUrl() {
    	return $_SESSION[Isession::SESSION_KEY]["PRE_URL"];
    }

    /**
     * Set the criteria to session
     * @param String $key
     * @param object $criteria
     */
    static function setCriteria($key, $criteria) {
    	 $_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY][$key] = $criteria;
    }
    
   /**
    * Getting object from the session with specific key
    * @param String $key
    */ 
   static function getCriteria($key) {
   	 if (!isset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY][$key])) {
    	 	return null;
   	 }
     return $_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY][$key];
   }
    
   /**
    * remove the criteria from session
    * @param String $key
    */
    static function removeCriteria($key) {
       if (isset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY][$key])) {
       	   unset($_SESSION[Isession::SESSION_KEY][Isession::SESSION_CRITERIA_KEY][$key]);
       }
    }
}