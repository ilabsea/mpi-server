<?php
class Patientws extends  MpiController {
	function __construct() {
	    parent::__construct(true, false);
	}
    function search() {
    	$result = array("patients" => array(),
    	                "error" => "");
    	try{
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
	            
	            $file = fopen(FCPATH."log.txt", "w");
	            $this->load->model("patient");
	            $patients = $this->patient->search();
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
    
    function enroll() {
    	$result = array("patientid" => "",
    	                "error" => "");
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
        
        
        $this->load->model("patient");
        $pat_id = $this->patient->newPatient(array("fingerprint" => $_POST["fingerprint"]));
        $result["patientid"] = $pat_id;
        echo json_encode($result);
        return;
    }
    
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
        $data["gender"] = $_POST["gender"];
        $data["birthdate"] = $_POST["birthdate"];
        $data["serv_id"] = $_POST["serviceid"];
        $data["visit_date"] = $_POST["visitdate"];
        $data["site_code"] = $_POST["sitecode"];
        $data["ext_code"] = $_POST["externalcode"];
        $data["info"] = $_POST["info"];
        
        $this->patient->newVisit($data);
        echo json_encode($return);
    	return;
    }
    
    function synchronize() {
    
    }
    
}