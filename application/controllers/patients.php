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
    	$criteria = array();
    	$this->load->model("patient");
    	$data["patient_list"] = $this->patient->patient_list($criteria);
        $this->load->template("templates/general", "patients/patient_list", Iconstant::MPI_APP_NAME, $data);
    }
    
    function patientdetail($pat_id) {
        $this->load->model("patient");
        $patient = $this->patient->getPatientById($pat_id);
        $var = array();
        array_push($var, $pat_id);
        $visit = $this->patient->getVisits($var);
        $data = array();
        $data["patient"] = $patient;
        $data["visit_list"] = $visit;
        $this->load->template("templates/general", "patients/patient_detail", Iconstant::MPI_APP_NAME, $data);
    }

}