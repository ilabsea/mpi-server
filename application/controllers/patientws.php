<?php
/**
 * Patient Web Service
 * @author Sokha RUM
 */
class Patientws extends  MpiController {
	/**
	 * Construction of Patientws
	 */
	function __construct() {
	    parent::__construct(true, false);
	}
	
	/**
	 * Reload the SDK
	 */
	function loadsdk() {
		$grFingerprint = new GrFingerService();
		$grFingerprint->initialize(true);
	}
	
	/**
	 * patient identify
	 */
    function search() {
    	$this->initLogPath();
    	ILog::info("Patient searching");
    	ILog::info(print_r($_POST, true));
    	$result = array("patients" => array(),
    	                "error" => "");
    	
    	try {
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "SDK is busy with other service";
	            ILog::error($result["error"]);
	            echo json_encode($result);
	            return;
	        endif;
	       
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$result["error"] = "SDK is busy with other service";
	        	ILog::error($result["error"]);
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        // get the valid fingerprint from the request
	        $fingerprints = $this->valid_fingerprint($grFingerprint, $result);
	        if($result["error"] != "") :
	        	 ILog::error($result["error"]);
	             echo json_encode($result);
	             return;
	        endif;
	        
	        if (count($fingerprints) <= 0) :
	             $result["error"] = "Could not find fingerprint";
	             ILog::error($result["error"]);
	             echo json_encode($result);
	             return;
	        endif;
	        
	        // Prepare for searching according to the most prioritied
            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST[$fingerprints[0]], $grFingerprint->GR_DEFAULT_CONTEXT);
            if ($ret != $grFingerprint->GR_OK) :
        	    $result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
        	    ILog::error($result["error"]);
        	    echo json_encode($result);
	            return;
        	endif;
	        
            $gender = "";
            if (isset($_POST["gender"]) && ($_POST["gender"] == 1 || $_POST["gender"] == 2)) :
                $gender = $_POST["gender"];
            endif;
            
            $this->load->model("patient");
            
            // get the list of patient with the specific gender
            $patients = $this->patient->search();
            $arr_patient = array();
            
            // start doing the matching
            //$cpt = 0;
            //ILog::info("patients: ".$patients->num_rows());
            foreach ($patients->result_array() as $row) :
                $score = 0;
                //$cpt++;
                //ILog::info("records = ".$cpt );
                if ($row[$fingerprints[0]] == null || $row[$fingerprints[0]] == "") :
                    continue;
                endif;
                                
                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fingerprints[0]],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
                if( $ret == $grFingerprint->GR_MATCH) : 
                    $patient = array();
                    $patient["patientid"] = $row["pat_id"];
                    $patient["gender"] = $row["pat_gender"];
                    $patient["birthdate"] = $row["pat_dob"];
                    $patient["sitecode"] = $row["pat_register_site"];
                    $patient["createdate"] = $row["date_create"];
                    $patient["age"] = $row["pat_age"];
                    $patient["visits"] = array();
                    $visits = $this->patient->getVisitsByPID($patient["patientid"]);
                    $last_test_date = "";
                    $last_test_result = "";
                    foreach($visits->result_array() as $row) :
		                $visit = array();
		                $visit["patientid"] = $patient["patientid"];
		                $visit["visitid"] = $row["visit_id"];
		                $visit["sitecode"] = $row["site_code"];
		                $visit["sitename"] = $row["site_name"];
		                $visit["externalcode"] = $row["ext_code"];
		                $visit["externalcode2"] = $row["ext_code_2"];
		                $visit["serviceid"] = $row["serv_id"];
		                $visit["info"] = $row["info"];
		                $visit["age"] = $row["pat_age"];
		                $visit["visitdate"] = $row["visit_date"];
		                if ($visit["serviceid"] == 1) :
		                    if ($last_test_date == "" || strcmp($last_test_date, $visit["visitdate"]) < 0) :
		                        $last_test_date = $visit["visitdate"];
		                        $last_test_result = $visit["info"];
		                    endif;
		                endif;
		                array_push($patient["visits"], $visit);
		            endforeach;
		            $patient["lastvcctdate"] = $last_test_date;
		            $patient["lastvcctresult"] = $last_test_result;
                    array_push($arr_patient, $patient);
                endif;
            endforeach;
            $result["patients"] = $arr_patient;

	        // Write the JSON object and send back to client
	        ILog::info("Success"); 
        	echo json_encode($result);
        	$grFingerprint->finalize();
		} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		ILog::error("error during patient searching: ".$e->getMessage());
    		echo json_encode($result);
    	}
    }

    /**
     * Enroll patient
     */
    function enroll() {
    	$this->initLogPath();
    	ILog::info("Patient registration");
    	ILog::info(print_r($_POST, true));
    	$result = array("patientid" => "",
    	                "error" => "");
    	try {
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "SDK is busy with other service";
	            echo json_encode($result);
	            return;
	        endif;
	  
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$result["error"] = "The request was rejected. Contact application administrator for more detail";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        // get the valid fingerprint from the request
	        $fingerprints = $this->valid_fingerprint($grFingerprint, $result);
		    if($result["error"] != "") :
	             echo json_encode($result);
	             return;
		    endif;
	        
		    if (count($fingerprints) <= 0) :
	             $result["error"] = "Could not find fingerprint";
	             echo json_encode($result);
	             return;
	        endif;
	        
	        if (count($fingerprints) <= 1) :
	             $result["error"] = "At least 2 fingerprints are required for registration";
	             echo json_encode($result);
	             return;
	        endif;
		        
	        $data = array();
	        foreach ($fingerprints as $fingerprint) :
	        	$data[$fingerprint] = $_POST[$fingerprint];
	        endforeach;
	        
	        if (isset($_POST["gender"])) :
	            $data["gender"] = $_POST["gender"]; 
	        endif;
	        
	        if (isset($_POST["age"]) && is_nint($_POST["age"])) :
	            $data["age"] = $_POST["age"]; 
	        endif;
	        
	        if (isset($_POST["birthdate"])) :
	            $data["birthdate"] = $_POST["birthdate"]; 
	        endif;
	        
	        if (isset($_POST["sitecode"]) && $_POST["sitecode"] != "") :
	            $data["sitecode"] = $_POST["sitecode"];
	            $this->load->model("site");
	            if ($this->site->getSiteByCode($data["sitecode"]) == null) :
	                 $result["error"] = "Side code ".$data["sitecode"]." is not available";
		             echo json_encode($result);
		             return;
	            endif;
	        else : 
	        	$result["error"] = "Side code is required";
		        echo json_encode($result);
		        return;
	        endif;
	        
	        $this->load->model("patient");

	        
	        // creating new patient and return the new patient id
	        $pat_id = $this->patient->newPatientFingerprint($data);
	        ILog::info("Patient registered with id: ".$pat_id);        
	        $result["patientid"] = $pat_id;
	        echo json_encode($result);
	        $grFingerprint->finalize();
    	} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		ILog::error("error during enrollment: ".$e->getMessage());
    		echo json_encode($result);
    	}
    }

	/**
     * Enroll patient
     */
    function enrollWithoutFingerprint() {
    	$this->initLogPath();
    	ILog::info("Patient registration without fingerprint");
    	ILog::info(print_r($_POST, true));
    	$result = array("patientid" => "",
    	                "error" => "");
    	try {
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "SDK is busy with other service";
	            echo json_encode($result);
	            return;
	        endif;
	  
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$result["error"] = "The request was rejected. Contact application administrator for more detail";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        if (isset($_POST["gender"])) :
	            $data["gender"] = $_POST["gender"]; 
	        endif;
	        
	        if (isset($_POST["age"]) && is_nint($_POST["age"])) :
	            $data["age"] = $_POST["age"]; 
	        endif;
	        
	        if (isset($_POST["birthdate"])) :
	            $data["birthdate"] = $_POST["birthdate"]; 
	        endif;
	        
	        if (isset($_POST["sitecode"]) && $_POST["sitecode"] != "") :
	            $data["sitecode"] = $_POST["sitecode"];
	            $this->load->model("site");
	            if ($this->site->getSiteByCode($data["sitecode"]) == null) :
	                 $result["error"] = "Side code ".$data["sitecode"]." is not available";
		             echo json_encode($result);
		             return;
	            endif;
	        else : 
	        	$result["error"] = "Side code is required";
		        echo json_encode($result);
		        return;
	        endif;
	        
	        $this->load->model("patient");

	        
	        // creating new patient and return the new patient id
	        $pat_id = $this->patient->newPatient($data);
	        ILog::info("Patient registered with id: ".$pat_id);        
	        $result["patientid"] = $pat_id;
	        echo json_encode($result);
	        $grFingerprint->finalize();
    	} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		ILog::error("error during enrollment: ".$e->getMessage());
    		echo json_encode($result);
    	}
    }
    
    /**
     * Create service or visit when the patient visit a site
     */
    function createservice() {
    	$this->initLogPath();
    	ILog::info("Create Visit");
    	ILog::info(print_r($_POST, true));
    	$return = array("patientid" => "", "error" => "");
    	try {
    		
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $return["error"] = "SDK is busy with other service";
	            echo json_encode($return);
	            return;
	        endif;
	  
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$return["error"] = "The request was rejected. Contact application administrator for more detail";
	        	echo json_encode($return);
	            return;
	        endif;
	        
	    	if (!isset($_POST["patientid"])) :
	    	     $return["error"] = "patientid is required";
	    	     echo json_encode($return);
	    	     return;
	    	endif;
	    	
	    	$return["patientid"] = $_POST["patientid"];
	    	if (!isset($_POST["serviceid"])) :
	    	     $return["error"] = "serviceid is required";
	    	elseif (!in_array($_POST["serviceid"], Iconstant::$MPI_SERVICE)) :
	    	     $return["error"] = "serviceid is not valid: ".$_POST["serviceid"];
	    	endif;
	    	if ($return["error"] != "") :
	    	     echo json_encode($return);
	    	     return;
	    	endif;
	    	
	    	/*
	    	if (!isset($_POST["sitecode"])) :
	    	     $return["error"] = "sitecode is required";
	    	     echo json_encode($return);
	    	     return;
	    	endif;
	    	*/
	    	
	    	if (isset($_POST["sitecode"]) && $_POST["sitecode"] != "") :
	            $data["sitecode"] = $_POST["sitecode"];
	            $this->load->model("site");
	            if ($this->site->getSiteByCode($data["sitecode"]) == null) :
	                 $result["error"] = "Side code ".$data["sitecode"]." is not available";
		             echo json_encode($result);
		             return;
	            endif;
		    else : 
		         $return["error"] = "sitecode is required";
	    	     echo json_encode($return);
	    	     return;
		    endif;
	    	
	    	
	    	if (!isset($_POST["visitdate"])) :
	    	     $return["error"] = "visitdate is required";
	    	     echo json_encode($return);
	    	     return;
	    	endif;
	    	
	    	$this->load->model("patient");
	    	$patient = $this->patient->getPatientById($_POST["patientid"]);
	    	if ($patient == null) :
	    	    $return["error"] = "Could not find patient with id: ".$_POST["patientid"];
	    	    echo json_encode($return);
	    	    return;
	    	endif;
	        
	        $data = array();
	        $data["pat_id"] = $_POST["patientid"];
	        /*$data["gender"] = $_POST["gender"];
	        $data["birthdate"] = $_POST["birthdate"];*/
	        $data["age"] = isset($_POST["age"]) ? $_POST["age"] : "";
	        $data["serv_id"] = $_POST["serviceid"];
	        $data["visit_date"] = $_POST["visitdate"];
	        $data["site_code"] = $_POST["sitecode"];
	        $data["ext_code"] = $_POST["externalcode"];
	        $data["ext_code_2"] = isset($_POST["externalcode2"]) ? $_POST["externalcode2"] : "";
	        $data["age"] = isset($_POST["age"]) && is_nint($_POST["age"]) ? $_POST["age"] : "";
	        $data["info"] = $_POST["info"];
	        
	        $visitid = $this->patient->newVisit($data);
	        $return["visitid"] = $visitid;
	        
	        echo json_encode($return);
	    	return;
    	} catch (Exception $e) {
    		$return["error"] = $e->getMessage();
    		ILog::error("error during enrollment: ".$e->getMessage());
    		echo json_encode($return);
    	}
    }
    
    /**
     * Synchronization module
     */
    function synchronize() {
    	$this->initLogPath();
    	ILog::info("Synchronization");
    	ILog::info(print_r($_POST, true));
    	$patient_array = array("patients" => array(), "error" => "");
    	try {
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $patient_array["error"] = "SDK is busy with other service";
	            ILog::error($patient_array["error"]);
	            echo json_encode($patient_array);
	            return;
	        endif;
	        
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$patient_array["error"] = "The request was rejected. Contact application administrator for more detail";
	        	ILog::error($patient_array["error"]);
	        	echo json_encode($patient_array);
	            return;
	        endif;
	        
	    	$patient_str = $_POST["patient"];
	    	$patient = json_decode($patient_str, true);
	    
	    	$data = array();
	    	
	    	$fingerprints = $this->valid_fingerprint($grFingerprint, $patient_array, $patient);
		    if($patient_array["error"] != "") :
		    	 ILog::error($patient_array["error"]);
	             echo json_encode($patient_array);
	             return;
		    endif;
	    	$gender = $patient["gender"];
	    	if ($gender == "0") :
	    		$gender = "";
	    	endif;
	    	$this->load->model("patient");
	    	$patient_found = $this->patient->getPatientById($patient["patientid"]);
	    	$array_found = array();
	    	if ($patient_found == null) :
		    	$patient_list = $this->patient->search($gender);
		    	
		    	
		    	foreach ($fingerprints as $fingerprint) :
		    	    $array_found2 = array();
		    		$ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($patient[$fingerprint], $grFingerprint->GR_DEFAULT_CONTEXT);
		            if($ret!=$grFingerprint->GR_OK) :
		                 $patient_array["error"] = "Fingerprint ".$fingerprint." is not correct";
		                 ILog::error($patient_array["error"]);
		                 echo json_encode($patient_array);
		                 return;
		            endif;
		            
		            if (count($array_found) > 1) :
		               foreach ($array_found as $row) :
		               		$score = 0;
			            	if ($row[$fingerprint] == null || $row[$fingerprint] == "") :
			            	    continue;
			            	endif;
			            	
			                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fingerprint],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
			                if( $ret == $grFingerprint->GR_MATCH) :
			                     array_push($array_found2, $row);
				            endif;
		               endforeach;
		            else:
		               foreach ($patient_list->result_array() as $row) :
			               $score = 0;
			            	if ($row[$fingerprint] == null || $row[$fingerprint] == "") :
			            	    continue;
			            	endif;
	
			                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fingerprint],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
			                if( $ret == $grFingerprint->GR_MATCH) :
			                     array_push($array_found2, $row);
				            endif;
		               endforeach;
		            endif;
		            
		            $array_found = $array_found2;
		            if (count($array_found) <= 1) :
		                break;
		            endif;
	    	endforeach;
	    	endif;
		    
	        if ($patient_found != null || count($array_found) == 0 || count($array_found) == 1): 
	            if ($patient_found != null) :
	            	// Do nothing
		        elseif (count($array_found) == 0) :
		            $patient_data["gender"] = $patient["gender"];
		            foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
		                $patient_data[$fingerprint] = isset($patient[$fingerprint]) ? $patient[$fingerprint] : "";
		            endforeach;
		            
		            $patient_data["age"] = isset($patient["age"]) ? $patient["age"] : "";
		            $patient_data["sitecode"] = isset($_POST["sitecode"]) ? $_POST["sitecode"] : null;
		            $patient_data["pat_dob"] = isset($patient["datebirth"]) ? $patient["datebirth"] : null;
		            $patient_data["date_create"] = $patient["createdate"];
		            $pat_id = $this->patient->newPatientFingerprint($patient_data);
		            $patient["patientid"] = $pat_id;
		        elseif (count($array_found) == 1) :
		            $patient["patientid"] = $array_found[0]["pat_id"];
		        endif;
		        
		        
		        foreach($patient["visits"] as $visit) :
		            $data_visit = array();
		            $data_visit["pat_id"] = $patient["patientid"];
		            $data_visit["serv_id"] = $visit["serviceid"];
		            $data_visit["site_code"] = $visit["sitecode"];
		            $data_visit["ext_code"] = $visit["externalcode"];
		            $data_visit["age"] = isset($visit["age"]) ? $visit["age"] : "";
		            $data_visit["ext_code_2"] = isset($visit["externalcode2"]) ? $visit["externalcode2"] : null;
		            $data_visit["info"] = $visit["info"];

		            $data_visit["date_create"] = $visit["createdate"];
		            $data_visit["visit_date"] = $visit["visitdate"];
		            $this->patient->newVisit($data_visit);
		        endforeach;
		        
		        // prepare to send back to client
		        
		        $visits = $this->patient->getVisitsByPID($patient["patientid"]);
		        
		        $patient["visits"] = array();
	            foreach($visits->result_array() as $row) :
		            $visit = array();
		            $visit["patientid"] = $patient["patientid"];
		            $visit["visitid"] = $row["visit_id"];
		            $visit["sitecode"] = $row["site_code"];
		            $visit["sitename"] = $row["site_name"];
		            $visit["externalcode"] = $row["ext_code"];
		            $visit["externalcode2"] = $row["ext_code_2"];
		            $visit["serviceid"] = $row["serv_id"];
		            $visit["info"] = $row["info"];
		            $visit["age"] = $row["pat_age"];
		            $visit["visitdate"] = $row["visit_date"];
		            $visit["createdate"] = $row["date_create"];
		            array_push($patient["visits"], $visit);
		        endforeach;
		        
		        foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
		             if (isset($patient[$fingerprint])) :
		             	unset($patient[$fingerprint]);
		             endif;
		        endforeach;
		        
		        array_push($patient_array["patients"], $patient);
				ILog::info("success");
		        echo json_encode($patient_array);
		        return;
	        endif;
	        
	        if (count($array_found) > 1) :
	        	foreach ($array_found as $elt) :
	        	    $arr_elt = array();
	        	    $arr_elt["patientid"] = $elt["pat_id"];
	                $arr_elt["gender"] = $elt["pat_gender"];
	                $arr_elt["age"] = $elt["pat_age"];
	                $arr_elt["birthdate"] = $elt["pat_dob"];
	                $arr_elt["sitecode"] = $elt["pat_register_site"];
	                $arr_elt["age"] = $elt["pat_age"];
	                $arr_elt["createdate"] = $elt["date_create"];
	                $arr_elt["visits"] = array();
	                $visits = $this->patient->getVisitsByPID($arr_elt["patientid"]);
		            foreach($visits->result_array() as $row) :
			            $visit = array();
			            $visit["patientid"] = $arr_elt["patientid"];
			            $visit["visitid"] = $row["visit_id"];
			            $visit["sitecode"] = $row["site_code"];
			            $visit["sitename"] = $row["site_name"];
			            $visit["externalcode"] = $row["ext_code"];
			            $visit["externalcode2"] = $row["ext_code_2"];
			            $visit["serviceid"] = $row["serv_id"];
			            $visit["info"] = $row["info"];
			            $visit["age"] = $row["pat_age"];
			            $visit["visitdate"] = $row["visit_date"];
			            $visit["createdate"] = $row["date_create"];
			            array_push($arr_elt["visits"], $visit);
			        endforeach;
			        array_push($patient_array["patients"], $arr_elt);
	        	endforeach;
	        	ILog::info("success");
	        	echo json_encode($patient_array);
		        return;
            endif;
            
    	} catch (Exception $e) { 
    		$patient_array["error"] = $e->getMessage() ;
    		ILog::error("error during synchronizing: ".$e->getMessage());
            echo json_encode($patient_array);
    	}
    }
    
    /**
     * 
     * @param unknown_type $grFingerprint
     */
    private function valid_fingerprint($grFingerprint, &$result, $reference=null) {
    	$arr = array();
    	if ($reference == null) :
    	   $reference = $_POST;
    	endif;
    	foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
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
    
    /**
     * synchronize update
     */
    function synchronizeupdate() {
    	$this->initLogPath();
    	ILog::info("Synchronization Update");
    	ILog::info(print_r($_POST, true));
        $patient_array = array("patient" => array(), "error" => "");
        try {
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $patient_array["error"] = "SDK is busy with other service";
	            echo json_encode($patient_array);
	            return;
	        endif;
	        
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$result["error"] = "The request was rejected. Contact application administrator for more detail";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	    	$patient_str = $_POST["patient"];

	    	$patient = json_decode($patient_str, true);
	    	
	    	if (!isset($_POST["patientid"]) || $_POST["patientid"] == "") :
	    	    $patient_array["error"] = "Could not find master id";
            	echo json_encode($patient_array);
            	return;
	    	endif;
	    	
	    	$patient_id = $_POST["patientid"];
	    	$this->load->model("patient");
	    	$patient_found = $this->patient->getPatientById($patient_id);
	    	if ($patient_found == null) :
	    		$patient_array["error"] = "Could not patient with master id ".$patient_id;
            	echo json_encode($patient_array);
            	return;
	    	endif;
	    	
	    	foreach ($patient["visits"] as $visit) :
    			$data_visit = array();
	            $data_visit["pat_id"] = $patient_id;
	            $data_visit["serv_id"] = $visit["serviceid"];
	            $data_visit["site_code"] = $visit["sitecode"];
	            $data_visit["ext_code"] = $visit["externalcode"];
	            $data_visit["ext_code_2"] = isset($visit["externalcode2"]) ? $visit["externalcode2"] : null;
	            $data_visit["info"] = $visit["info"];
				$data_visit["age"] = isset($visit["age"]) ? $visit["age"] : "";
	            $data_visit["date_create"] = $visit["createdate"];
	            $data_visit["visit_date"] = $visit["visitdate"];
	            $this->patient->newVisit($data_visit);
	    	endforeach;
	    	
	    	// prepare to send back to client
	    	$visits = $this->patient->getVisitsByPID($patient_id);
	        $patient["visits"] = array();
	        $patient["patientid"] = $patient_id;
            foreach($visits->result_array() as $row) :
	            $visit = array();
	            $visit["patientid"] = $patient_id;
	            $visit["visitid"] = $row["visit_id"];
	            $visit["sitecode"] = $row["site_code"];
	            $visit["sitename"] = $row["site_name"];
	            $visit["externalcode"] = $row["ext_code"];
	            $visit["serviceid"] = $row["serv_id"];
	            $visit["info"] = $row["info"];
	            $visit["age"] = $row["pat_age"];
	            $visit["visitdate"] = $row["visit_date"];
	            $visit["createdate"] = $row["date_create"];
	            array_push($patient["visits"], $visit);
	        endforeach;
	        
	        foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
	             if (isset($patient[$fingerprint])) :
	             	unset($patient[$fingerprint]);
	             endif;
	        endforeach;
	        $patient_array["patient"] = $patient;
	    	echo json_encode($patient_array);
	    	//log_message($message)
	    	
        } catch (Exception $e) { 
    		$patient_array["error"] = $e->getMessage() ;
    		ILog::error("error during synchronize update: ".$e->getMessage());
            echo json_encode($patient_array);
    	}
    }
    
    /**
     * The test for this controller
     */
    function testAll() {
    	$grFingerprint = new GrFingerService();
		
    	$this->load->library('unit_test');
    	$this->unit->run($grFingerprint->initialize(), true, "loadsdk");
    	//$this->unit->run(1+1, 2, "Test1");
    	
    	//echo "<pre>";
    	//print_r ($this->unit->result());
    	//echo "</pre>";
    	echo $this->unit->report();
    }
    
	/**
     * Detect if the finger is ok with the site
     * @param Object $grFingerprint
     * @param array $reference
     */
    private function accept_webservice($grFingerprint, $reference=null) {
    	if ($reference == null) :
    	   $reference = $_POST;
    	endif;
    	
    	/*if (true) :
    	   return true;
    	endif;*/
    	
    	$site_code = !isset($_POST["sitecode"]) || $_POST["sitecode"] == "" ? null : $_POST["sitecode"];
    	$fp_name = !isset($_POST["member_fp_name"]) || $_POST["member_fp_name"] == "" ? null : $_POST["member_fp_name"];
    	$fp_val = !isset($_POST["member_fp_value"]) || $_POST["member_fp_value"] == "" ? null : $_POST["member_fp_value"];
    	
    	if ($site_code == null || $fp_name == null || $fp_val == null) :
    	    ILog::info("Detect webservice: missing parameters");
    	    return false;
    	endif;
    	$this->load->model("member");
    	$members = $this->member->getMemberBySiteCode($site_code);
    	$ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($fp_val, $grFingerprint->GR_DEFAULT_CONTEXT);
    	if ($ret != $grFingerprint->GR_OK) :
    	    ILog::info("Detect webservice: Cannot prepare base 64");
	        return FALSE;
	    endif;
    	foreach($members->result_array() as $row) :
    		$score = 0;
            if ($row[$fp_name] == null || $row[$fp_name] == "") :
                continue;
            endif;
    	    $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fp_name],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
    	    if( $ret == $grFingerprint->GR_MATCH) :
    	        return true;
    	    endif;
    	endforeach;
    	ILog::info("Detect webservice: Not match fingerprint");
    	return false;
    }
    
    /**
     * Init log path
     */
    private function initLogPath() {
        if (isset($_POST["sitecode"]) && $_POST["sitecode"] != "") :
            ILog::setPath($_POST["sitecode"]);
        endif; 
    }
    
	/**
     * Enroll patient
     */
    function replaceUpdateMasterId() {
    	$this->initLogPath();
    	ILog::info("Update replace patient id");
    	ILog::info(print_r($_POST, true));
    	$result = array("patient" => "",
    	                "error" => "");
    	try {
    		// detect the fingerprint SDK
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "SDK is busy with other service";
	            echo json_encode($result);
	            return;
	        endif;
	  
	        if (!$this->accept_webservice($grFingerprint)) :
	        	$result["error"] = "The request was rejected. Contact application administrator for more detail";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        if (!isset($_POST["master_id"]) || $_POST["master_id"] == "") :
	        	$result["error"] = "Parameter master_id is required";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        if (!isset($_POST["new_master_id"]) || $_POST["new_master_id"] == "") :
	        	$result["error"] = "Parameter master_id is required";
	        	echo json_encode($result);
	            return;
	        endif;
	        
	        $this->load->model("patient");
	        $old_patient = $this->patient->getPatientById($_POST["master_id"]);
	        if ($old_patient == null) :
	        	$result["error"] = "Could not find patient with master id ".$_POST["master_id"];
	        	echo json_encode($result);
	        	return;
	        endif;
	        
	        $new_patient = $this->patient->getPatientById($_POST["new_master_id"]);
	        if ($new_patient == null) :
	        	$result["error"] = "Could not find patient with master id ".$_POST["new_master_id"];
	        	echo json_encode($result);
	        	return;
	        endif;

	        $this->patient->updateReplacePatientId($_POST["master_id"], $_POST["new_master_id"]);
	        
	        ILog::info("Update successfully");
	        echo json_encode($result);
	        $grFingerprint->finalize();
    	} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		ILog::error("error during enrollment: ".$e->getMessage());
    		echo json_encode($result);
    	}
    }
}
