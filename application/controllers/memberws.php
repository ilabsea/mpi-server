<?php
/**
 * The member webservice class
 * @author Sokha RUM
 */
class Memberws extends MpiController {
	/**
	 * Construction of Memer web service
	 */
	function __construct() {
	    parent::__construct(true, false);
	}
	
	/**
	 * Register New Member
	 */
	function register() {
	    ILog::info("Register new member");
    	ILog::info(print_r($_POST, true));
    	$result = array("patients" => array(),
    	                "error" => "");
    	try {
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "Could not initialize finger print SDK";
	            echo json_encode($result);
	            return;
	        endif;
	        
	        // get the valid fingerprint from the request
	        $fingerprints = $this->valid_user_fp($grFingerprint, $result);
	        if($result["error"] != "") :
	             echo json_encode($result);
	             return;
	        endif;
	        
	        if (count($fingerprints) <= 0) :
	             $result["error"] = "Could not find fingerprint";
	             echo json_encode($result);
	             return;
	        endif;
	        
            if (!isset($_POST["sitecode"]) || $_POST["sitecode"] == "") :
                 $result["error"] = "Could not find site code";
	             echo json_encode($result);
	             return;
            endif;
            
            $this->load->model("site");
            $site = $this->site->getSiteByCode($_POST["sitecode"]);
            if ($site == null) :
                 $result["error"] = "Site code is not correct";
	             echo json_encode($result);
	             return;
            endif;
            
            $data = array();
	        foreach ($fingerprints as $fingerprint) :
	        	$data[$fingerprint] = $_POST[$fingerprint];
	        endforeach;
	        
	        $data["member_login"] = $_POST["member_login"];
	        $data["member_pwd"] = $_POST["member_pwd"];
	        $data["site_code"] = $_POST["sitecode"];
	        foreach ($fingerprints as $fingerprint) :
	        	$data[$fingerprint] = $_POST[$fingerprint];
	        endforeach;
	        
	        
	        $this->load->model("member");
	        $this->member->createNew($data);
	        
    		
    	} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		ILog::error("error during patient searching: ".$e->getMessage());
    		echo json_encode($result);
    	}
	}
	
	/**
	 * Getting the available fingerprint
	 * @param Object $grFingerprint
	 * @param Array $result
	 * @param Array $reference
	 */
    private function valid_user_fp($grFingerprint, &$result, $reference=null) {
    	$arr = array();
    	if ($reference == null) :
    	   $reference = $_POST;
    	endif;
    	foreach (Iconstant::$MPI_USER_FP as $fingerprint) :
    	    if (isset($reference[$fingerprint]) && $reference[$fingerprint] != "") :
        		$ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($reference[$fingerprint], $grFingerprint->GR_DEFAULT_CONTEXT);
        		if ($ret != $grFingerprint->GR_OK) :
        		    $result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
	            	return FALSE;
	            else :
	                array_push($arr, $fingerprint);
        		endif;
        	endif;
    	endforeach;
        return $arr;
    }

}