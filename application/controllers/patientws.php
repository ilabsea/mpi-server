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
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $result["error"] = "Could not initialize finger print SDK";
	            echo json_encode($result);
	            return;
	        endif;
	        
	        if (isset($_POST["fingerprint"])) :
	            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST["fingerprint"], $grFingerprint->GR_DEFAULT_CONTEXT);
	            if($ret!=$grFingerprint->GR_OK) :
	                 $result["error"] = "Fingerprint template1 is not correct";
	                 echo json_encode($result);
	                 return;
	            endif;
	            
	           /*
	            if (isset($_POST["fingerprint2"])) :
		            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST["fingerprint2"], $grFingerprint->GR_DEFAULT_CONTEXT);
		            if($ret!=$grFingerprint->GR_OK) :
		                 $result["error"] = "Fingerprint template2 is not correct";
		                 echo json_encode($result);
		                 return;
		            endif;
		            //$data["fingerprint2"] = $_POST["fingerprint2"];
		        else:
		            //Do Nothing
		        endif;
		        */
		        
	            $gender = "";
	            if (isset($_POST["gender"]) && ($_POST["gender"] == 1 || $_POST["gender"] == 2)) :
	                $gender = $_POST["gender"];
	            endif;
	            
	            $this->load->model("patient");
	            $patients = $this->patient->search($gender);
	            $arr_patient = array();
	            foreach ($patients->result_array() as $row) :
	                $score = 0;
	                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row["pat_fingerprint"],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
	                if( $ret == $grFingerprint->GR_MATCH) : 
	                    $patient = array();
	                    $patient["patientid"] = $row["pat_id"];
	                    $patient["gender"] = $row["pat_gender"];
	                    $patient["birthdate"] = $row["pat_dob"];
	                    $patient["visits"] = array();
	                    //$arr_patient[$row["pat_id"]] = $patient;
	                    $visits = $this->patient->getVisitsByPID($patient["patientid"]);
	                    foreach($visits->result_array() as $row) :
			                $visit = array();
			                $visit["sitecode"] = $row["site_code"];
			                $visit["externalcode"] = $row["ext_code"];
			                $visit["serviceid"] = $row["serv_id"];
			                $visit["info"] = $row["info"];
			                $visit["visitdate"] = $row["visit_date"];
			                array_push($patient["visits"], $visit);
			            endforeach;
	                    array_push($arr_patient, $patient);
	                endif;
	            endforeach;
	            $result["patients"] = $arr_patient;
	        else:
	            $result["error"] = "Could not find fingerprint";
	        endif;
        	echo json_encode($result);
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
    	//var_dump($_POST);
    	$grFingerprint = new GrFingerService();
        if (!$grFingerprint->initialize()) :
            $result["error"] = "Could not initialize finger print SDK";
            echo json_encode($result);
            return;
        endif;
        
        if (isset($_POST["fingerprint"])) :
            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST["fingerprint"], $grFingerprint->GR_DEFAULT_CONTEXT);
            if($ret!=$grFingerprint->GR_OK) :
                 $result["error"] = "Fingerprint template is not correct";
                 echo json_encode($result);
                 return;
            endif;
        else:
            $result["error"] = "Could not find fingerprint";
            echo json_encode($result);
            return;
        endif;
        $data = array();
        $data["fingerprint"] = $_POST["fingerprint"];
        if (isset($_POST["gender"])) :
            $data["gender"] = $_POST["gender"]; 
        endif;
        
        if (isset($_POST["fingerprint2"])) :
            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($_POST["fingerprint2"], $grFingerprint->GR_DEFAULT_CONTEXT);
            if($ret!=$grFingerprint->GR_OK) :
                 $result["error"] = "Fingerprint template2 is not correct";
                 echo json_encode($result);
                 return;
            endif;
            $data["fingerprint2"] = $_POST["fingerprint2"];
        else:
            //Do Nothing
        endif;
        
        
        $this->load->model("patient");
        $pat_id = $this->patient->newPatient($data);
        $result["patientid"] = $pat_id;
        echo json_encode($result);
        return;
    }
    
    /**
     * Create service
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
    	elseif ($_POST["serviceid"] != 1 && $_POST["serviceid"] != 2 && $_POST["serviceid"] != 3) :
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
        $data["info"] = $_POST["info"];
        
        $visitid = $this->patient->newVisit($data);
        $return["visitid"] = $visitid;
        
        echo json_encode($return);
    	return;
    }
    
    function synchronize() {
    	$patient_array = array("patients" => array(), "error" => "");
    	$hander = fopen("c:/works/dane/log.txt", "a");
	    
	        
    	try {
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	            $patient_array["error"] = "Could not initialize finger print SDK";
	            echo json_encode($patient_array);
	            return;
	        endif;
	    	$patient_str = $_REQUEST["patient"];
	    	fwrite($hander, $_REQUEST["patient"].PHP_EOL);
	    	$patient = json_decode($patient_str, true);
	        
	        $val = print_r($patient, true);
	    	fwrite($hander, $val.PHP_EOL);
	    
	    	$data = array();
	    	
	    	$pat_fingerprint_1 = $patient["fingerprint"];
	    	$pat_fingerprint_2 = $patient["fingerprint2"];
	    	$gender = $patient["gender"];
	    	if ($gender == "0") :
	    		$gender = "";
	    	endif;
	    	$this->load->model("patient");
	    	$patient_list = $this->patient->search($gender);
	    	
	    	if (isset($patient["fingerprint"])) :
	            $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($pat_fingerprint_1, $grFingerprint->GR_DEFAULT_CONTEXT);
	            if($ret!=$grFingerprint->GR_OK) :
	                 $result["error"] = "Fingerprint template1 is not correct";
	                 echo json_encode($result);
	                 return;
	            endif;
	            
	            $array_found = array();
	            foreach ($patient_list->result_array() as $row) :
	                $score = 0;
	                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row["pat_fingerprint"],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
	                if( $ret == $grFingerprint->GR_MATCH) :
	                     array_push($array_found, $row);
		            endif;
	            endforeach;
	            
	            $array_found2 = array();
	            if (count($array_found) > 1) :
	                $ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($pat_fingerprint_2, $grFingerprint->GR_DEFAULT_CONTEXT);
	            	if($ret!=$grFingerprint->GR_OK) :
	                	$result["error"] = "Fingerprint template2 is not correct";
	                	echo json_encode($result);
	                	return;
	            	endif;
	            	foreach ($array_found as $row) :
		                $score = 0;
		                $ret = $grFingerprint->GrFingerX->IdentifyBase64($row["pat_fingerprint2"],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
		                if( $ret == $grFingerprint->GR_MATCH) :
		                     array_push($array_found2, $row);
			            endif;
		            endforeach;
		            $array_found = $array_found2;
		        endif;
		        
		        if (count($array_found) == 0 || count($array_found) == 1): 
			        if (count($array_found) == 0) :
			            $patient_data["pat_gender"] = $patient["gender"];
			            $patient_data["fingerprint"] = $patient["fingerprint"];
			            $patient_data["fingerprint2"] = $patient["fingerprint2"];
			            $patient_data["pat_dob"] = $patient["datebirth"];
			            $patient_data["date_create"] = $patient["createdate"];
			            $pat_id = $this->patient->newPatient($patient_data);
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
			            $data_visit["info"] = $visit["info"];
			            //$data_visit["info"] = $visit["info"];
			            $data_visit["date_create"] = $visit["createdate"];
			            $data_visit["visit_date"] = $visit["visitdate"];
			            $this->patient->newVisit($data_visit);
			        endforeach;
			        
			        $visits = $this->patient->getVisitsByPID($patient["patientid"]);
			        $patient["visits"] = array();
		            foreach($visits->result_array() as $row) :
			            $visit = array();
			            $visit["sitecode"] = $row["site_code"];
			            $visit["externalcode"] = $row["ext_code"];
			            $visit["serviceid"] = $row["serv_id"];
			            $visit["info"] = $row["info"];
			            $visit["visitdate"] = $row["visit_date"];
			            $visit["createdate"] = $row["date_create"];
			            array_push($patient["visits"], $visit);
			        endforeach;
			        unset($patient["fingerprint"]);
			        unset($patient["fingerprint2"]);
			        array_push($patient_array["patients"], $patient);
			        echo json_encode($patient_array);
			        return;
		        endif;
		        
		        if (count($array_found) > 1) :
		            
		        	foreach ($array_found as $elt) :
		        	    $arr_elt = array();
		        	    $arr_elt["patientid"] = $elt["pat_id"];
		                $arr_elt["gender"] = $elt["pat_gender"];
		                $arr_elt["birthdate"] = $elt["pat_dob"];
		                $arr_elt["visits"] = array();
		                $visits = $this->patient->getVisitsByPID($arr_elt["patientid"]);
			            foreach($visits->result_array() as $row) :
				            $visit = array();
				            $visit["sitecode"] = $row["site_code"];
				            $visit["externalcode"] = $row["ext_code"];
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
		    endif;
    	} catch (Exception $e) { 
    		$patient_array["error"] = $e->getMessage() ;
            echo json_encode($patient_array);
            fwrite($hander, $e->getMessage().PHP_EOL);
    	}
    	fclose($hander);
    }
}
