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
	        
	        // Prepare for searching according to the most prioritied
            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST[$fingerprints[0]], $grFingerprint->GR_DEFAULT_CONTEXT);
            if ($ret != $grFingerprint->GR_OK) :
        	    $result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
        	    echo json_encode($result);
	            return;
        	endif;
	        
            $gender = "";
            if (isset($_POST["gender"]) && ($_POST["gender"] == 1 || $_POST["gender"] == 2)) :
                $gender = $_POST["gender"];
            endif;
            
            $this->load->model("patient");
            
            // get the list of patient with the specific gender
            $patients = $this->patient->search($gender);
            $arr_patient = array();
            
            // start doing the matching
            foreach ($patients->result_array() as $row) :
                $score = 0;
                if ($row[$fingerprints[0]] == null || $row[$fingerprints[0]] == "") :
                    continue;
                endif;
                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fingerprints[0]],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
                if( $ret == $grFingerprint->GR_MATCH) : 
                    $patient = array();
                    $patient["patientid"] = $row["pat_id"];
                    $patient["gender"] = $row["pat_gender"];
                    $patient["birthdate"] = $row["pat_dob"];
                    $patient["visits"] = array();
                    $visits = $this->patient->getVisitsByPID($patient["patientid"]);
                    foreach($visits->result_array() as $row) :
		                $visit = array();
		                $visit["sitecode"] = $row["site_code"];
		                $visit["sitename"] = $row["site_name"];
		                $visit["externalcode"] = $row["ext_code"];
		                $visit["externalcode2"] = $row["ext_code_2"];
		                $visit["serviceid"] = $row["serv_id"];
		                $visit["info"] = $row["info"];
		                $visit["visitdate"] = $row["visit_date"];
		                array_push($patient["visits"], $visit);
		            endforeach;
                    array_push($arr_patient, $patient);
                endif;
            endforeach;
            $result["patients"] = $arr_patient;
            
	        // Write the JSON object and send back to client 
        	echo json_encode($result);
        	$grFingerprint->finalize();
		} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		echo json_encode($result);
    	}
    }
    
    /**
     * Enroll patient
     */
    function enroll() {
    	$result = array("patientid" => "",
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
	        
	        if (isset($_POST["age"])) :
	            $data["age"] = $_POST["age"]; 
	        endif;
	        
	        if (isset($_POST["birthdate"])) :
	            $data["birthdate"] = $_POST["birthdate"]; 
	        endif;
	        
	        
	        
	        $this->load->model("patient");
	        
	        // creating new patient and return the new patient id
	        $pat_id = $this->patient->newPatientFingerprint($data);
	        $result["patientid"] = $pat_id;
	        echo json_encode($result);
	        $grFingerprint->finalize();
    	} catch (Exception $e) {
    		$result["error"] = $e->getMessage();
    		echo json_encode($result);
    	}
    }
    
    /**
     * Create service or visit when the patient visit a site
     */
    function createservice() {
    	$return = array("patientid" => "", "error" => "");
    	if (!isset($_POST["patientid"])) :
    	     $return["error"] = "patientid is required";
    	     echo json_encode($return);
    	     return;
    	endif;
    	
    	$return["patientid"] = $_POST["patientid"];
    	if (!isset($_POST["serviceid"])) :
    	     $return["error"] = "serviceid is required";
    	elseif (in_array(Iconstant::$MPI_SERVICE, $_POST["serviceid"])) :
    	     $return["error"] = "serviceid is not valid: ".$_POST["serviceid"];
    	endif;
    	if ($return["error"] != "") :
    	     echo json_encode($return);
    	     return;
    	endif;
    	
    	if (!isset($_POST["sitecode"])) :
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
        $data["serv_id"] = $_POST["serviceid"];
        $data["visit_date"] = $_POST["visitdate"];
        $data["site_code"] = $_POST["sitecode"];
        $data["ext_code"] = $_POST["externalcode"];
        $data["ext_code_2"] = isset($_POST["externalcode2"]) ? $_POST["externalcode2"] : "";
        $data["info"] = $_POST["info"];
        
        $visitid = $this->patient->newVisit($data);
        $return["visitid"] = $visitid;
        
        echo json_encode($return);
    	return;
    }
    
    function synchronize() {
    	$patient_array = array("patients" => array(), "error" => "");
//    	$hander = fopen("c:/works/dane/log.txt", "a");
	    
	        
    	try {
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $patient_array["error"] = "Could not initialize finger print SDK";
	            echo json_encode($patient_array);
	            return;
	        endif;
	    	$patient_str = $_POST["patient"];
//fwrite($hander, $patient_str.PHP_EOL);
	    	$patient = json_decode($patient_str, true);
	    
	    	$data = array();
	    	
	    	$fingerprints = $this->valid_fingerprint($grFingerprint, $patient_array, $patient);
		    if($patient_array["error"] != "") :
	             echo json_encode($patient_array);
	             return;
		    endif;
//fwrite($hander, "Available fingerprints".count($fingerprints).PHP_EOL);
	    	$gender = $patient["gender"];
	    	if ($gender == "0") :
	    		$gender = "";
	    	endif;
	    	$this->load->model("patient");
	    	$patient_list = $this->patient->search($gender);
	    	
	    	$array_found = array();
	    	foreach ($fingerprints as $fingerprint) :
	    	    $array_found2 = array();
	    		$ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($patient[$fingerprint], $grFingerprint->GR_DEFAULT_CONTEXT);
	            if($ret!=$grFingerprint->GR_OK) :
	                 $patient_array["error"] = "Fingerprint ".$fingerprint." is not correct";
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
//fwrite($hander, "Enter loop patients".PHP_EOL);
	               foreach ($patient_list->result_array() as $row) :
		               $score = 0;
		            	if ($row[$fingerprint] == null || $row[$fingerprint] == "") :
		            	    continue;
		            	endif;

		                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fingerprint],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
//fwrite($hander, print_r($row, true).PHP_EOL);
		                if( $ret == $grFingerprint->GR_MATCH) :
//fwrite($hander, "Exspected pass".PHP_EOL);
		                     array_push($array_found2, $row);
			            endif;
	               endforeach;
	            endif;
	            
	            $array_found = $array_found2;
//fwrite($hander, "found ".count($array_found).PHP_EOL);
	            if (count($array_found) <= 1) :
	                break;
	            endif;
	    	endforeach;
	    	
	    	
//fwrite($hander, "Second found ".count($array_found).PHP_EOL);
		        
	        if (count($array_found) == 0 || count($array_found) == 1): 
		        if (count($array_found) == 0) :
		            $patient_data["gender"] = $patient["gender"];
		            foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
		                $patient_data[$fingerprint] = isset($patient[$fingerprint]) ? $patient[$fingerprint] : "";
		            endforeach;
		            
		            $patient_data["age"] = isset($patient["pat_age"]) ? $patient["pat_age"] : null;
		            $patient_data["pat_dob"] = isset($patient["datebirth"]) ? $patient["datebirth"] : null;
		            $patient_data["date_create"] = $patient["createdate"];
//fwrite($hander, "Create new".PHP_EOL);
//$tmp = print_r($expression)
		            $pat_id = $this->patient->newPatientFingerprint($patient_data);
		            $patient["patientid"] = $pat_id;
		        elseif (count($array_found) == 1) :
		            $patient["patientid"] = $array_found[0]["pat_id"];
		        endif;
		        
//fwrite($hander, "Third found ".count($array_found).PHP_EOL);
		        
		        foreach($patient["visits"] as $visit) :
		            $data_visit = array();
		            $data_visit["pat_id"] = $patient["patientid"];
		            $data_visit["serv_id"] = $visit["serviceid"];
		            $data_visit["site_code"] = $visit["sitecode"];
		            $data_visit["ext_code"] = $visit["externalcode"];
		            $data_visit["ext_code_2"] = isset($visit["externalcode2"]) ? $visit["externalcode2"] : null;
		            $data_visit["info"] = $visit["info"];

		            $data_visit["date_create"] = $visit["createdate"];
		            $data_visit["visit_date"] = $visit["visitdate"];
		            $this->patient->newVisit($data_visit);
		        endforeach;
		        
		        $visits = $this->patient->getVisitsByPID($patient["patientid"]);
		        $patient["visits"] = array();
	            foreach($visits->result_array() as $row) :
		            $visit = array();
		            $visit["sitecode"] = $row["site_code"];
		            $visit["sitename"] = $row["site_name"];
		            $visit["externalcode"] = $row["ext_code"];
		            $visit["serviceid"] = $row["serv_id"];
		            $visit["info"] = $row["info"];
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
	                $arr_elt["visits"] = array();
	                $visits = $this->patient->getVisitsByPID($arr_elt["patientid"]);
		            foreach($visits->result_array() as $row) :
			            $visit = array();
			            $visit["sitecode"] = $row["site_code"];
			            $visit["sitename"] = $row["site_name"];
			            $visit["externalcode"] = $row["ext_code"];
			            $visit["externalcode2"] = $row["ext_code_2"];
			            $visit["serviceid"] = $row["serv_id"];
			            $visit["info"] = $row["info"];
			            $visit["visitdate"] = $row["visit_date"];
			            $visit["createdate"] = $row["date_create"];
			            array_push($arr_elt["visits"], $visit);
			        endforeach;
			        array_push($patient_array["patients"], $arr_elt);
	        	endforeach;
	        	echo json_encode($patient_array);
		        return;
            endif;

    	} catch (Exception $e) { 
    		$patient_array["error"] = $e->getMessage() ;
            echo json_encode($patient_array);
            //fwrite($hander, $e->getMessage().PHP_EOL);
    	}
    	//fclose($hander);
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
}
