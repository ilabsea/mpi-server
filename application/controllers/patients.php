<?php
/**
 * Patient Controller
 * @author Sokha RUM
 */
class Patients extends MpiController {
	/**
	 * List of patient
	 */
    function patientlist() {
    	$data = array();
		$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("service");
    	$data["services"] = $this->service->getServices();
    	
    	$criteria = array(
    						"cri_serv_id" => "",
    						"cri_master_id" => "",
    						"cri_pat_gender" => "",
    						"cri_site_code" => "",
    						"cri_external_code" => "",
    						"cri_external_code2" => "",
    						"cur_page" => 1,
    						"orderby" => "pat_id",
    						"orderdirection" => "ASC",
    						"date_from" => "",
    						"date_to" => "",
    					);
    	
    	$this->load->model("patient");
    	
    	
    	$start = 0;
    	
    	$session_data = Isession::getCriteria("patient_list");
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if (isset($_REQUEST["orderby"])) :
    		$criteria["orderby"] = $_REQUEST["orderby"];
    	endif;
    	
    	if (isset($_REQUEST["orderdirection"])) :
    		$criteria["orderdirection"] = $_REQUEST["orderdirection"];
    	endif;
    	
    	if (isset($_REQUEST["cur_page"])) :
    		$criteria["cur_page"] = $_REQUEST["cur_page"];
    	endif;
    	
    	//var_dump($data);
    	if ($first_access || ($data["error"] != null && $data["error"] != "")) :
    	    $data = array_merge($data, $criteria);
    	    $data["patient_list"] = null;
    	    $data["total_record"] = 0;
    	    $data["nb_of_page"] = 1;
    	    $this->load->template("templates/general", "patients/patient_list", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	$total_patients = $this->patient->count_patient_list($criteria);
    	$total_pages = (int)($total_patients / Iconstant::PAGINATION_ROW_PER_PAGE);
    	if ($total_pages == 0 || $total_pages * Iconstant::PAGINATION_ROW_PER_PAGE < $total_patients) :
    	    $total_pages++;
    	endif;
    	
    	if ($criteria["cur_page"] > $total_pages) :
    		$criteria["cur_page"] = $total_pages;
    	endif; 
    	
    	Isession::setCriteria("patient_list", $criteria);
    	
    	
    	
    	$start = ($criteria["cur_page"] - 1) * Iconstant::PAGINATION_ROW_PER_PAGE;
    	
    	//$criteria["orderby"] = $orderby;
    	//$criteria["orderdirection"] = $orderdirection;

    	$data["show_partial_page"] = 0;
    	if ($total_pages >11) :
    		$start_plus = 0;
    		$end_plus = 0;
   			if ($criteria["cur_page"] <= 5) :
   				$start_page = 1;
   				$start_plus = 1 + 5 - $criteria["cur_page"];
   			endif;
   			
   			if ($criteria["cur_page"] >= $total_pages - 5) :
    			$end_page = $total_pages;
    			$end_plus =  $criteria["cur_page"] + 5 - $total_pages ;
    		endif;
   			
    		if ($criteria["cur_page"] > 5) :
    			$start_page = $criteria["cur_page"] - 5 - $end_plus;
    		endif;
    		
    		if ($criteria["cur_page"] < $total_pages - 5) :
    			$end_page = $criteria["cur_page"] + 5 + $start_plus;
    		endif;
    		
   			/* 	
    		if ($criteria["cur_page"] >= 6) :
    			$start_page = $criteria["cur_page"] - 5;
    		else :
    			$start_page = 1;
    		endif;
    		
    		if ($criteria["cur_page"] <= $total_pages - 5) :
    			$end_page = $criteria["cur_page"] + 5;
    		else :
    			$end_page = $total_pages; 
    		endif;
    		*/
    	
    		$data["show_partial_page"] = 1;
    		$data["start_page"] = $start_page; 
    		$data["end_page"] = $end_page;
    	endif;
    	$data["patient_list"] = $this->patient->patient_list($criteria, $start, Iconstant::PAGINATION_ROW_PER_PAGE);
    	//$data["cur_page"] = $cur_page;
    	$data["total_record"] = $total_patients;
    	$data["nb_of_page"] = $total_pages;
    	$data = array_merge($data, $criteria);
    	
    	//echo "<pre>"; var_dump($data); echo "</pre>";
    	
    	
        $this->load->template("templates/general", "patients/patient_list", Iconstant::MPI_APP_NAME, $data);
    }

    /**
     * Patient Detail
     * @param int $pat_id
     */
    function patientdetail($pat_id) {
        $this->load->model("patient");
        $orderby = "visit_date";
        $orderdirection = "DESC";
        if (isset($_REQUEST["orderby"])) :
    		$orderby = $_REQUEST["orderby"];
    	endif;
    	
    	if (isset($_REQUEST["orderdirection"])) :
    		$orderdirection = $_REQUEST["orderdirection"];
    	endif;
        
        
        $patient = $this->patient->getPatientById($pat_id);
        $var = array();
        array_push($var, $pat_id);
        $visit = $this->patient->getVisits($var, $orderby, $orderdirection);
        $data = array();
        $data["patient"] = $patient;
        $data["visit_list"] = $visit;
        $data["orderby"] = $orderby;
        $data["orderdirection"] = $orderdirection;
        $this->load->template("templates/general", "patients/patient_detail", Iconstant::MPI_APP_NAME, $data);
    }

    /**
     * Search patient
     */
    function search() {
    	$criteria = $_POST;
    	$criteria["cri_site_code"] = trim($criteria["cri_site_code"]);
    	$criteria["cri_external_code"] = trim($criteria["cri_external_code"]);
    	$criteria["cri_external_code2"] = trim($criteria["cri_external_code2"]);
    	$criteria["date_from"] = trim($criteria["date_from"]);
    	$criteria["date_to"] = trim($criteria["date_to"]);
    	
    	$error = "";
    	
    	if ($error=="" && $criteria["date_from"] != null && date_html_to_php($criteria["date_from"]) == null) :
    		$error = "Visit date (from) format is not correct"; 
    	endif;
    	
    	if ($error=="" && $criteria["date_to"] != null && date_html_to_php($criteria["date_to"]) == null) :
    		$error = "Visit date (to) format is not correct"; 
    	endif;
    	
    	Isession::setFlash("error", $error);
    	

    	$session_data = Isession::getCriteria("patient_list");
    	if ($session_data != null) :
    		$criteria = array_merge($session_data, $criteria);
    	endif;
    	
    	Isession::setCriteria("patient_list", $criteria);
    	redirect(site_url("patients/patientlist"));
    }
}