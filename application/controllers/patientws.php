<?php
/**
 * Patient Web Service
 * @author Sokha RUM
 */
class Patientws extends  MpiController {

	function skip_authentication() {
		return true;
	}

	function allow_log_ui_response(){
		return false;
	}
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
    // $grFingerprint = new GrFingerService();
    // $grFingerprint->initialize(true);
  }

	function before_action(){
		log_message("info", "Hello");
		parent::before_action();

		$this->load->model("site");
		$this->load->model("member");
		$this->load->model("patient");
		$this->require_member_fingerprint();
	}

	private function require_member_fingerprint(){
		if (!$this->accept_webservice()){
			$result["error"] = "The request was rejected. Contact application administrator for more detail";
			return $this->render_json($result);
		}
	}

  /**
   * patient identify
	 *	$params = array("fingerprint_r1"=>"","fingerprint_r2"=>"p/8BHrMAjAABNgGKALYAAXcAmACVAAGCAGcApQABZgByAKEAAXEApgDAAAF3AJIAzAABKwHMAHwAAYIAuADKAAErAcIAngABggCBAMUAASsBWgCkAAFgAMUAtgABfADLAEAAAkEB0QC3AAEwAWQA2gABdwCDAPAAAisBjwA4AAGpAIQAXgABrwBaAEoAARcAuAAKAQFBAX4ALAABAABtAAABASsBXABpAAEtANMACAECQQHkAPsAATsBIwA9AQEGAMIAPQEBXQGTABUAAakAHwA2AQHWALEAGh0ODAMEAwsKAQYKERUYGQgFCQAGBQYBCAwMCQsEFhAUGAIADgkABwUBFRwXEwgOAQQFDBEcCQcPCgECCgUGCBAPFg8QBgEDCgQSEQQCFxIKAxAKCQIFDgUJBQIICRQZDAASExMVDwYIARIVAQsKCwMCDwEbFA4ADwMKAgUABgQGAg8LCAoMAhsYBgwTEQYDDAcCBxABDgcCEhAFAQAPBAsXFBAHDQQXBQQBCQMXDREWCggACAIWBgsCFAgQCAYOABIOAhkIBAAYCAYLBBIXFQ0cFxEZDhQGGxkCFxIcFAUZDBQWEg0FBwcSExwWAQ0VAA0QAxgOCAcLEhgMGBAYBRYFGQUBEgkSBBMLEwARFgMHEQMTFggJDR0WGQYaFhQPGxAZCQINFxwbFgIVABUPFxsIHQ8aDwccDg0dEBoQABwbBhsFGQcdChoKHQYaFB0LGgYdFBobHQMdBBkNHRcdEh0T",
	 *	 "fingerprint_r3"=>"","fingerprint_r4"=>"","fingerprint_r5"=>"","fingerprint_l1"=>"",
	 *	 "fingerprint_l2"=>"p/8BHmAAvgABTwBmAM0AAU8ATwCxAAH4AFkAtQABSgCEANYAAVUASAC/AAFEAJoAqwABYAB1ALUAAVUAcACOAAF3AHcAWQABngCfAMYAAmAAnwD1AAFaALMArwABawBYAGwAAbQAiwBVAAGTAKkAUQABjQCDAIsAAXcAbgAGAQFKAG0AagABUgFgAAkBAf4AVwDlAAH+ADUABAEB8gBIAPsAAUQAYgAlAQH4AFQAEgEBRABLAKMAAT4AVgCgAAFEAGYAewABmAA0ADYBAecAiwAnAQFKAKAAAwIAAxkaExECGRgTAQAFAgIaGxIIEAkOBQMSCQgbAxobDQACFRYNEgAHAxkXGAUADAYYFgEDCgYWFBQBBwMFGQEHFxMTFhgRDg8KDAQBBAoAGhoIFxEQGwEFBRoAGRgVBAcBAg0JCBIHGhMUEg4bCQcGBwIGEAcIFAARFBASERYaGx0XFAULBAgNGQgEABMVBxAdEQMICgcYFAcZBQcVFBQECwoCCAQGGRsBGhwYHBcXFhwVGhAJDxAJBggaDR0TEQsbDhANEQQICR0LFgEQDhcVDQ4UBxEVAQoZDREBHRgaEgcbDBAWBRMBBAwABhwWAQYHDBMEHBMIDgoABAUSDwoQFxQTCxAPCwEVBQsMCwYLFBwRDAgLBxMFGgkbDx0WEQodBAEMCA8dFBgFDQ8GDhUCHBQWGRwdBg8MDwwOFRk=",
	 *	 "fingerprint_l3"=>"","fingerprint_l4"=>"","fingerprint_l5"=>"","gender"=>"1",
	 *	 "sitecode"=>"","member_fp_name"=>"","member_fp_value"=>"");
   */
  function search() {
    $result = array("patients" => array(), "error" => "");
		$params = $_POST;
    $gender = "";
    if (isset($params["gender"]) && ($params["gender"] == 1 || $params["gender"] == 2))
        $gender = $params["gender"];
    $this->load->model("patient");
    // get the list of patient with the specific gender
    $patients = $this->patient->search();
    $arr_patient = array();
		$sdk = GrFingerService::get_instance();
		$fingerprints = Patient::fingerprint_params($params);
		$match = false;
		foreach ($fingerprints as $key => $value){
			if(!$value)
				continue;
			$ok = $sdk->prepare($value);
			if (!$ok){
				$result["error"] = "Fingerprint (".$key.") template is not correct";
				ILog::error($result["error"]);
				// echo json_encode($result);
				return $this->render_json($result);
			}
			foreach ($patients->result_array() as $row) {
				if (!$row[$key])
						continue;
				$patient_tmp = $this->search_patient_fingerprint($row, $key);
				if($patient_tmp)
					$arr_patient[$row["pat_id"]] = $patient_tmp;
			}
		}
		foreach($arr_patient as $value){
			$result["patients"][] = $value;
		}
    ILog::info("Success");
		return $this->render_json($result);
  }

	//  $_POST = array(
	// 		 "fingerprint_r1"=>"",
	// 		 "fingerprint_r2"=>"p/8BHWIAxgABYABJALcAAVUA3ACOAAF8AKkAsgABcQBuAOMAAR8BUQDfAAFgAHwALAEBRwFyAOsAAXEAcwAIAQE7AY0AdAABtABxAHwAATMAqAAxAAFMAaEAJgEBNgFfAIMAAUQAZAB7AAHyAKMAXQABTAF1AHMAAeEAbgBkAAEtAMsAIwEBggCqADMBAYIAxgAoAQE2AVkANgEBggC4AAwBATYBgwCWAAFPAF8AGgEBhwCSAJkAAXcAkQA4AQE7AdoAMgABhwCBACUAAQAAowAUEg0OBwQKEAoOGRcTDBARDhANCgkQChEaDBoGDhEaEw0QGAgIBwQFCgkAARUYExQFABIWBAAUFhcKCQ8GGA0RAxkMFgkRFwkHBRUGExIGDAYIGQkIBBQMFxAHABMWFw0LHAUBFw4OCQwSDwsZChMGAxcZEA0JGwsYBxAPFQgRDwgFFxEaFAwIGQ4ZDQENGhUYBBoIBAEAFxoYCg8aFhgFAwIZDxoSGREXDwYHBwEAGQEOERwPHAANARcMGAMJBhYIABYIDxsTCAEKCQsAAwYUBgQVDBkCDAcCDxELBAMWBw0PAwoQHBUHARkTGAcDBhIJHBUTBBkQCwIJAw8VBBUFChwGBRQIFwIbHBYDAREOHBIIAhsKCwUDDRwOCwkbGAEIAxQHEgcCEAILFQAWBRccEgMRGxAbFhkZGxUBFgIHAhICARwUAg==",
	// 		 "fingerprint_r3"=>"",
	// 		 "fingerprint_r4"=>"",
	// 		 "fingerprint_r5"=>"",
	// 		 "fingerprint_l1"=>"",
	// 		 "fingerprint_l2"=>"p/8BHswAAAEBSgDTANkAAVUAuQAFAQFEAOUA1gACWgC5APsAAfgAqgDjAAHyAMMA7AABSgDgAN4AAlUA1QArAQJEANcAFAEBAwHZAOgAAgMBsQDOAAJKALoA1wACSgC7ALwAAWAAnAC+AAE5AJEAtwAB7QCAAPkAAe0AbgAXAQHnAHgALAEB5wBgABABATkAagABAQHtAIQACAEBOQDPADwBAfgAjQATAQE5APQAhgABggC0ALMAATkAGgA3AQF3AEoAPwEBpACXAIcAAdYAsQB7AAG6ALQABwMCBA0ZCgcODwwLFxUHARETFRAKARMUAQMWCAQGAAQFDAIACw0GCgoDAAYRFAULEhEICQYMCQAUEAYBAQwLDg4ZCxkACgYFDA0RFQIGFRQEBRwdFxARFw4NBgcSFwkCBgsPGREQCgwEDAELDBkSEwIFExUEChIVAQ0TEAkEFxQHDAwOCw8ABwUOAAEGAxYJDQ8FAQQBAgoFDQwDCAAJCgAFEhQXEwkGFwICDAgCEAUPHBsaARkEBwMNBg0FGQADFwQbEgcNBwsFDxsTDA8SEBUCFQUZHAMLGxEJBxUEDhwZHQgEFwUEEBYCCQEDGRYABg4JAw0cAgMQCxAODR0XAAgGCAoYHRAPDx0VBhYEGxQOHRQFCxwSAggXEQIVDhkYFhcNGBEEGxUaExsXAxgWBhUPCx0UDhQPFgoIAwwcEBkWEhsQARgaEQcYDB0cGBoSGhQLGBYRFBkaFRoQGhcUHBsWExwaDxoc",
	// 		 "fingerprint_l3"=>"",
	// 		 "fingerprint_l4"=>"",
	// 		 "fingerprint_l5"=>"",
	// 		 "gender"=>"1",
	// 		 "sitecode"=>"0206",
	// 		 "member_fp_name"=>"member_fp_l1",
	// 		 "member_fp_value"=>"p/8BHooABQEBMwDJACkBAU8AdAAaAQI5AHgACAEBMwB/AP4AAecAvgAgAQFPAFkAEAEB7QBjAPsAAecAnAAJAQI5AG4ACgEB5wBnABEBATkAcwDuAAItAOgAFQEBFAHpACABAVoA7gAPAQFmAFYAHQEB7QBNAMQAATMAkwDrAAEtAFsAsgAB4QA4AB8BAfgAzgCzAAFjAe0A8AABdwDrAMEAAY0AwQDbAAGeAJcAiAABywBVADMBAfIAswAKAQFEANQA+AABawA1ABgBAUQAywCtAAGvAKgAFB0MDhMcCQMKCQ0MAwQABA8GAQUKBgIKAgkNDggAAgMDAAoDCQcECwkEDwoHCxkPBgkKBxoIEBIGBwUaAwcDCxsVCQsAEQQHCQAEEQIGDwICBAIAEw8OFQoEDwkIEQgECxEWFAYDAQ0ACw8cDBsbFw4bAgcZBhkTCAMPBxMGAQwcBgwVCgAKCxobGQoWHQEaAxEZAhoABQgZHBcUBgsFDAUNAggaEQEODRsJCAUbFx0VFg0VCREZCREXFRcXFhMKGhcBGxwKBQ4aBAwaHAcZAwEIAhETBwsQEwkNGhwJCBsFAA4aCBcHEBMCGxYCHBoVHRgCGgsSGxEBFQUVFRQFERsUBRcOFxQYDBcSGAcSBQIVHRsdCRAEEAYQDhYNFxESERADEBEUBBIRHQwWAQIcEAkSGhQDEhoWFxgQGBodERYRGBYYGQULGBIdAgwBDxkB"
	//  );
  function enroll() {
    $result = array("patientid" => "", "error" => "");
		$params = $_POST;
		log_message("info", "Params".print_r($params, true));
		$filter_params = AppHelper::slice_array($params, array("gender","age", "sitecode", "birthdate"));
		$fingerprints = $this->patient_fingerprint_names($params);
    if (count($fingerprints) <= 0){
      $result["error"] = "Could not find fingerprint";
      return $this->render_json($result);
    }

    if (count($fingerprints) <= 1){
      $result["error"] = "At least 2 fingerprints are required for registration";
      return $this->render_json($result);
    }

    $data = array();
    foreach ($fingerprints as $fingerprint)
      $filter_params[$fingerprint] = $params[$fingerprint];

    if ($filter_params["sitecode"] && $this->site->getSiteByCode($filter_params["sitecode"]) == null){
      $result["error"] = "Side code ".$data["sitecode"]." is not available";
      return $this->render_json($result);
		}
    else if($filter_params["sitecode"] == ""){
			$result["error"] = "Side code is required";
      return $this->render_json($result);
		}

    $this->load->model("patient");
    // creating new patient and return the new patient id
    $pat_id = $this->patient->newPatientFingerprint($filter_params);
    $result["patientid"] = $pat_id;
    return $this->render_json($result);
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
          $data["refer_to_vcct"] = isset($_POST["refer_to_vcct"]) && is_nint($_POST["refer_to_vcct"]) ? $_POST["refer_to_vcct"] : 0;
          $data["refer_to_oiart"] = isset($_POST["refer_to_oiart"]) && is_nint($_POST["refer_to_oiart"]) ? $_POST["refer_to_oiart"] : 0;
          $data["refer_to_std"] = isset($_POST["refer_to_std"]) && is_nint($_POST["refer_to_std"]) ? $_POST["refer_to_std"] : 0;
          $data["info"] = $_POST["info"];

          $visitid = $this->patient->newVisit($data);
          $return["visitid"] = $visitid;


          if ($data["serv_id"] == 2) : // OI/ART Service
            if (isset($_POST["vcctsite"]) && $_POST["vcctsite"] != "" && isset($_POST["vcctnumber"]) && $_POST["vcctnumber"] != "") :
              $vcct_info = array();
              $vcct_info["ext_code"] = $_POST["vcctnumber"];
              $vcct_info["site_code"] = $_POST["vcctsite"];
              $vcct_info["pat_id"] = $data["pat_id"];
              $this->patient->manageVcctNoFpFromOiart($vcct_info);
            endif;
          endif;

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
  function match_patients_with_fingerprint($fingerprint_name, $patient_list){
		$sdk = GrFingerService::get_instance();
		$patient_result = array();
		foreach ($patient_list as $patient) :
			if ($patient[$fingerprint_name] == null || $patient[$fingerprint_name] == "")
				 continue;
			$found = $sdk->identify($patient[$fingerprint_name]);
			if( $found)
				$patient_result[] = $patient;
		endforeach;
		return $patient_result;
	}

  function synchronize() {
		$params = $_POST;
    //  $params = array(
		// 	 "patient"=>"{\"patientid\":\"08f0fa26-1223-472b-8de5-d40d80fead49\",\"fingerprint_r1\":\"\",\"fingerprint_r2\":\"\",\"fingerprint_r3\":\"\",\"fingerprint_r4\":\"p/8BHpMADQEBJQHIAK8AATYBygDtAAErAdUAvwABggCDAMAAAWsA4wCeAAGHAKwAWAABngB4AMIAAWYA1wDWAAF8AGsAwAABWgCbAEsAAV0BwwAvAQI2AbQA/QACKwGZANQAAXcAfAAfAQElAaIA7QABKwGnALIAAXwArgDrAAF3AJsAfQABpACQAOQAASUBtgDhAAF3ALMANAABpADlANkAATABpwAYAAFYAcMAOAECRwHfAC4BATYBmQDpAAJxAOgAXgABRwGSAKkAAYIAbgD6AAFxAK0AGAsPGhoTBwQPEREUBwkWCBMNDBEPExoNBgoDAREaCAMPFBAcAhQECQwPAggMAg8NBBwLGQwUAhEOAA0EGBkVFxoUFgMRExENAQUUDRABChUMGgIWFAgADwAaAwUHHAAMDRAGFQ0HEwQTFAQQAg8dEw4dEgYAEwgBEwcAHQARDBMNHAkcHBIRCB0aFAMCAxoEFBYDEAwNFBACGhIKBxANCRoHFgEMCBMJChcLDBQBEBIBHB0PAg0TEA8EABQREAgFHQ0ADR0HERYdCREDCwAWBRMcDQEbBg4aFAQYDAwWGgkJEA4THQQQBQ4PGAAGFwUbGQwLAg4MARIZAhsVCxEEEg4RCw4LDxgCGA4SFQcSGwoFEhgRAAcUBQkSGA8ZABIbGREcBhkWGRQBGxkIAxIFBh0cEAYcChsXAxsZDhIXFBIQCgEKBRUWEhwVFhsBFQkKHBcFFxAXCRUJFw==\",\"fingerprint_r5\":\"\",\"fingerprint_l1\":\"\",\"fingerprint_l2\":\"\",\"fingerprint_l3\":\"\",\"fingerprint_l4\":\"p/8BI1EAXAABMwDXAGkAARQBXAB9AAE5AHMAPAABHQCOANwAATkAsKAFgAlQAC7QBPAJcAAe0ArwAfAAGeAMIAlQABTwC6AFwAAXEApgDDAALyAHoAnQACOQCEAKoAATkAtACgAAFKAHgAiwACOQBmAKcAAeEAnQAvAAFjAZMAOAABtACbAKIAAj4A5ACmAAJaAKEAngAB+ACpAIoAAU8AYQDoAAEtAI0ADAEBOQDqAHwAAWAAigBIAAK6AD0AnAABPgBUANAAAS0AfQADMAsgAB8gBDAMwAAeEANQDBAAHtAC0A3wAB4QCeAFAAAkwBxAATFQcGEhEgHgsFDQwcHxoSHyAOCQwPDhUbByIaFRYQDB0YEAYRCA0THhsGAg4WEw4iEhoDFxwJFhkBBQ4QBxsGExYHAiEfDRAKIg0VGhEPAiEgHx4BCgMSDQ8QDxMMIhEcIAUVFQkGDwQLCxMCAAwGBRMLFQsOFAkSCCAbFQwQkXHxMPFBkQGwsNIRwMBwcPEAIbAgwCAxEhHhweFQ8cEAkZFwQeBgADIgMfGxQOGAQNBiAHDRYJAQ8WFgoNDgQFBQ0FFgwWHxAEDQoaIggeEBATBRQXIQoSCwkfBwoRGgggECAGHBsGABYBGBccBwsWGQoJChcgBBwWIgcABBMdFw0CABoPABwNFAEKCAEiDhkEFRQWFxAOARYZBBAeAhQVGwAdQPGiEbAgMPIgIaFw0WGh8NABIVARgLFwsKAyEHAiIhEA8KFRkBERMiHAsYHAEIARIFGQ8SHRwAERgFHgAWEQYaIQQYDRgfHQsdHx0hGCEWCBgTGQgdBRsaCQgYFA==\",\"fingerprint_l5\":\"\",\"gender\":1,\"age\":\"\",\"sitecode\":\"\",\"datebirth\":\"\",\"cate\":\"2017-01-18 14:50:14\",\"updatedate\":\"2017-01-18 14:50:14\",\"visits\":[{\"visitid\":\"ba336822-026e-4d5d-a380-7d0bda89cda7\",\"patientid\":\"KH002100003441\",\"age\":80,\"serviceid\":2,\"sitecode\":\"0202\",\"visitdate\":\"2017-01-25 00:00:00\",\"externalcode\":\"009999\",\"externalcode2\":\"\",\"info\":\"Followup Visit\",\"syn\":false,\"servicename\":\"OI_ART\",\"createdate\":\"2017-01-25 15:38:12\",\"updatedate\":\"2017-01-25 15:38:12\",\"vcctsite\":\"\",\"vcctnumber\":\"\",\"refer_to_vcct\":0,\"refer_to_oiart\":0,\"refer_to_std\":0}]}",
		// 	 "sitecode"=>"V02-04",
		// 	 "member_fp_name"=>"",
		// 	 "member_fp_value"=>""
		//  );

    $patient_array = array("patients" => array(), "error" => "");
    $sdk = GrFingerService::get_instance();
    $patient_str = $params["patient"];
    $patient = json_decode($patient_str, true);

		$fingerprints = $this->patient_fingerprint_names($patient);
		$data = array();
    $gender = $patient["gender"];
    if ($gender == "0")
      $gender = "";

    $this->load->model("patient");
    $patient_found = $this->patient->getPatientById($patient["patientid"]);
		$patient_found = $patient_found ? array($patient_found):array();
    if (count($patient_found) == 0) {
			$patient_found = $this->sync_search_patient_by_fingerprint($fingerprints, $patient);
    }
		$patient_data = array();
    if (count($patient_found)==0){
      $patient_data["gender"] = $patient["gender"];
      foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint)
          $patient_data[$fingerprint] = isset($patient[$fingerprint]) ? $patient[$fingerprint] : "";

      $patient_data["age"] = isset($patient["age"]) ? $patient["age"] : "";
      $patient_data["sitecode"] = isset($params["sitecode"]) ? $params["sitecode"] : null;
      $patient_data["pat_dob"] = isset($patient["datebirth"]) ? $patient["datebirth"] : null;
      $patient_data["date_create"] = isset($patient["createdate"])? $patient["createdate"]:"";
      $pat_id = $this->patient->newPatientFingerprint($patient_data);
      $patient["patientid"] = $pat_id;
		}
		else if(count($patient_found) == 1){
			$patient["patientid"] = $patient_found[0]["pat_id"];

			$this->sync_insert_visits($patient["visits"], $patient["patientid"]);
			$patient["visits"] = $this->sync_return_visits($patient["patientid"]);


	  	foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint)
	    	if (isset($patient[$fingerprint]))
	      	unset($patient[$fingerprint]);

	  	$patient_array["patients"][] = $patient;
	  	return $this->render_json($patient_array);
		}
    else{
    	foreach ($patient_found as $elt) {
        $patient_json = array();
        $patient_json["patientid"] = $elt["pat_id"];
        $patient_json["gender"] = $elt["pat_gender"];
        $patient_json["age"] = $elt["pat_age"];
        $patient_json["birthdate"] = $elt["pat_dob"];
        $patient_json["sitecode"] = $elt["pat_register_site"];
        $patient_json["age"] = $elt["pat_age"];
        $patient_json["createdate"] = $elt["date_create"];
        $patient_json["visits"] = $this->sync_return_visits($elt["pat_id"]);
				$patient_array["patients"][] = $patient_json;
			}
			return $this->render_json($patient_array);
    }
  }



  private function patient_fingerprint_names($params) {
    $result = array();
    foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint)
      if (isset($params[$fingerprint]) && $params[$fingerprint] != "")
        $result[] = $fingerprint;

    return $result;
  }
  private function valid_fingerprint($grFingerprint=null, &$result, $reference=null) {
      $arr = array();
      if ($reference == null) :
         $reference = $_POST;
      endif;
			$sdk = GrFingerService::get_instance();
      foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
          if (isset($reference[$fingerprint]) && $reference[$fingerprint] != "") :
            $ok = $sdk->identify($reference[$fingerprint]);
            if ($ok)
							array_push($arr, $fingerprint);
            else{
							$result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
              return FALSE;
						}

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
          $patient_array["error"] = "Could not find patient with master id ".$patient_id;
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
        $data_visit["refer_to_vcct"] = isset($visit["refer_to_vcct"]) ? $visit["refer_to_vcct"] : 0;
        $data_visit["refer_to_oiart"] = isset($visit["refer_to_oiart"]) ? $visit["refer_to_oiart"] : 0;
        $data_visit["refer_to_std"] = isset($visit["refer_to_std"]) ? $visit["refer_to_std"] : 0;
              $data_visit["date_create"] = $visit["createdate"];
              $data_visit["visit_date"] = $visit["visitdate"];
              $this->patient->newVisit($data_visit);
               if ($data_visit["serv_id"] == 2) : // OI/ART Service
              if (isset($visit["vcctsite"]) && $visit["vcctsite"] != "" && isset($visit["vcctnumber"]) && $visit["vcctnumber"] != "") :
                $vcct_info = array();
                $vcct_info["ext_code"] = $visit["vcctnumber"];
                $vcct_info["site_code"] = $visit["vcctsite"];
                $vcct_info["pat_id"] = $data_visit["pat_id"];
                $this->patient->manageVcctNoFpFromOiart($vcct_info);
              endif;
            endif;
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
              $visit["refer_to_vcct"] = $row["refer_to_vcct"];
            $visit["refer_to_oiart"] = $row["refer_to_oiart"];
            $visit["refer_to_std"] = $row["refer_to_std"];
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

	// $_POST = array(
	// 		"fingerprint_r1"=>"",
	// 		"fingerprint_r2"=>"p/8BHWIAxgABYABJALcAAVUA3ACOAAF8AKkAsgABcQBuAOMAAR8BUQDfAAFgAHwALAEBRwFyAOsAAXEAcwAIAQE7AY0AdAABtABxAHwAATMAqAAxAAFMAaEAJgEBNgFfAIMAAUQAZAB7AAHyAKMAXQABTAF1AHMAAeEAbgBkAAEtAMsAIwEBggCqADMBAYIAxgAoAQE2AVkANgEBggC4AAwBATYBgwCWAAFPAF8AGgEBhwCSAJkAAXcAkQA4AQE7AdoAMgABhwCBACUAAQAAowAUEg0OBwQKEAoOGRcTDBARDhANCgkQChEaDBoGDhEaEw0QGAgIBwQFCgkAARUYExQFABIWBAAUFhcKCQ8GGA0RAxkMFgkRFwkHBRUGExIGDAYIGQkIBBQMFxAHABMWFw0LHAUBFw4OCQwSDwsZChMGAxcZEA0JGwsYBxAPFQgRDwgFFxEaFAwIGQ4ZDQENGhUYBBoIBAEAFxoYCg8aFhgFAwIZDxoSGREXDwYHBwEAGQEOERwPHAANARcMGAMJBhYIABYIDxsTCAEKCQsAAwYUBgQVDBkCDAcCDxELBAMWBw0PAwoQHBUHARkTGAcDBhIJHBUTBBkQCwIJAw8VBBUFChwGBRQIFwIbHBYDAREOHBIIAhsKCwUDDRwOCwkbGAEIAxQHEgcCEAILFQAWBRccEgMRGxAbFhkZGxUBFgIHAhICARwUAg==",
	// 		"fingerprint_r3"=>"",
	// 		"fingerprint_r4"=>"",
	// 		"fingerprint_r5"=>"",
	// 		"fingerprint_l1"=>"",
	// 		"fingerprint_l2"=>"p/8BHswAAAEBSgDTANkAAVUAuQAFAQFEAOUA1gACWgC5APsAAfgAqgDjAAHyAMMA7AABSgDgAN4AAlUA1QArAQJEANcAFAEBAwHZAOgAAgMBsQDOAAJKALoA1wACSgC7ALwAAWAAnAC+AAE5AJEAtwAB7QCAAPkAAe0AbgAXAQHnAHgALAEB5wBgABABATkAagABAQHtAIQACAEBOQDPADwBAfgAjQATAQE5APQAhgABggC0ALMAATkAGgA3AQF3AEoAPwEBpACXAIcAAdYAsQB7AAG6ALQABwMCBA0ZCgcODwwLFxUHARETFRAKARMUAQMWCAQGAAQFDAIACw0GCgoDAAYRFAULEhEICQYMCQAUEAYBAQwLDg4ZCxkACgYFDA0RFQIGFRQEBRwdFxARFw4NBgcSFwkCBgsPGREQCgwEDAELDBkSEwIFExUEChIVAQ0TEAkEFxQHDAwOCw8ABwUOAAEGAxYJDQ8FAQQBAgoFDQwDCAAJCgAFEhQXEwkGFwICDAgCEAUPHBsaARkEBwMNBg0FGQADFwQbEgcNBwsFDxsTDA8SEBUCFQUZHAMLGxEJBxUEDhwZHQgEFwUEEBYCCQEDGRYABg4JAw0cAgMQCxAODR0XAAgGCAoYHRAPDx0VBhYEGxQOHRQFCxwSAggXEQIVDhkYFhcNGBEEGxUaExsXAxgWBhUPCx0UDhQPFgoIAwwcEBkWEhsQARgaEQcYDB0cGBoSGhQLGBYRFBkaFRoQGhcUHBsWExwaDxoc",
	// 		"fingerprint_l3"=>"",
	// 		"fingerprint_l4"=>"",
	// 		"fingerprint_l5"=>"",
	// 		"gender"=>"1",
	// 		"sitecode"=>"0206",
	// 		"member_fp_name"=>"member_fp_r2",
	// 		"member_fp_value"=>"p/8BG48AkAABcQCvAI8AASsBXgCjAAFaAMkAfQABdwC4AKIAAWsAewC2AAFmAGIAOwABEQCEAFYAAaQAUACeAAFVAJMAwAABZgBfAF4AASIApADVAAEaAYoAzwABZgDUAEAAAjYBfwDUAAEaAY0A4wABGgG1AL8AAWsAwQDDAAEfAZYAHwEBMAFpAPYAARoBagCeAAFgAHwAiAABdwCYADAAAZ4AcACGAAJmAHAAfQABtACIACIAAV0BwgAVAQErAZkAFxgVFw4MERACFAIIFRgMCQ8MFhkEAQ8OABUUFwkFCBQLDAsJDwsOCQsQFBUFFBAEDAUOBQEDAAEAFwsREQQCFwkQFBgYCgUCCgYAGA8JCw4KBxQAAhUIFxMOEw8EAwIYBQAHBhcKBxYYBxIaBhkEAAgYEQkMEAUVCQQPBRABCQAIFQUXBQgBFRUHFQoTDAsFBxkXBwIACRQPEBEBBhYLBAwRCQEOEA4UDgIABxAAEg8PEQADEhMDDQwCDRYMAAEXBQQUCggAEwUIChgGEwsOERMJAgoQAxEDGgsBBw4IChYKGRoPEgsXBhIOFQMPAgMHEgwVBg0ZGhEHDRMCGBYBDRoQCAcaDBMIAxYVFgQHGg4YGRoTEgkLAwAGEhAIBgQNFRkXGQANFQ0aBBENGgMaBxoN"
	// );

  private function accept_webservice($grFingerprint=null, $reference=null) {
		$params = AppHelper::slice_array($_POST, array("sitecode","member_fp_name","member_fp_value"));
    if (!$params["sitecode"] || !$params["member_fp_name"] || !$params["member_fp_value"]){
        log_message("info","Detect webservice: missing parameters");
        return false;
    }
		$sdk = GrFingerService::get_instance();
    $members = $this->member->getMemberBySiteCode($params["sitecode"]);
    $ok = $sdk->prepare($params["member_fp_value"]);
    if (!$ok)
			return FALSE;
    foreach($members->result_array() as $row){
			log_message("info", "Row ".print_r($row, true));
			$member_fingerprint_value = $row[$params["member_fp_name"]];
			if (!$member_fingerprint_value)
				continue;
			$matched = $sdk->identify($member_fingerprint_value);
			if( $matched)
				return true;
		}
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

		private function search_patient_fingerprint($patient, $key){
			$sdk = GrFingerService::get_instance();
			$ok = $sdk->identify($patient[$key]);
			$patient_result = array();
			if(!$ok)
				return null;
			$patient_result["patientid"] = $patient["pat_id"];
			$patient_result["gender"] = $patient["pat_gender"];
			$patient_result["birthdate"] = $patient["pat_dob"];
			$patient_result["sitecode"] = $patient["pat_register_site"];
			$patient_result["createdate"] = $patient["date_create"];
			$patient_result["age"] = $patient["pat_age"];
			$patient_result["visits"] = array();
			$visits = $this->patient->getVisitsByPID($patient_result["patientid"]);
			$last_test_date = "";
			$last_test_result = "";
			foreach($visits->result_array() as $patient){
				$visit = array();
				$visit["patientid"] = $patient_result["patientid"];
				$visit["visitid"] = $patient["visit_id"];
				$visit["sitecode"] = $patient["site_code"];
				$visit["sitename"] = $patient["site_name"];
				$visit["externalcode"] = $patient["ext_code"];
				$visit["externalcode2"] = $patient["ext_code_2"];
				$visit["serviceid"] = $patient["serv_id"];
				$visit["info"] = $patient["info"];
				$visit["age"] = $patient["pat_age"];
				$visit["visitdate"] = $patient["visit_date"];
				$visit["refer_to_vcct"] = $patient["refer_to_vcct"];
				$visit["refer_to_oiart"] = $patient["refer_to_oiart"];
				$visit["refer_to_std"] = $patient["refer_to_std"];
				if ($visit["serviceid"] == 1 && ($last_test_date == "" || strcmp($last_test_date, $visit["visitdate"]) < 0)){
					$last_test_date = $visit["visitdate"];
					$last_test_result = $visit["info"];
				}
				$patient_result["visits"][] = $visit;
			}
			$patient_result["lastvcctdate"] = $last_test_date;
			$patient_result["lastvcctresult"] = $last_test_result;
			return $patient_result;
		}

		private function sync_insert_visits($visits, $patient_id){
			foreach($visits as $visit){
				$data_visit = array();
				$data_visit["pat_id"] =$patient_id;
				$data_visit["serv_id"] = $visit["serviceid"];
				$data_visit["site_code"] = $visit["sitecode"];
				$data_visit["ext_code"] = $visit["externalcode"];
				$data_visit["age"] = isset($visit["age"]) ? $visit["age"] : "";
				$data_visit["ext_code_2"] = isset($visit["externalcode2"]) ? $visit["externalcode2"] : null;
				$data_visit["info"] = $visit["info"];
				$data_visit["refer_to_vcct"] = isset($visit["refer_to_vcct"]) ? $visit["refer_to_vcct"] : 0;
				$data_visit["refer_to_oiart"] = isset($visit["refer_to_oiart"]) ? $visit["refer_to_oiart"] : 0;
				$data_visit["refer_to_std"] = isset($visit["refer_to_std"]) ? $visit["refer_to_std"] : 0;

				$data_visit["date_create"] = $visit["createdate"];
				$data_visit["visit_date"] = $visit["visitdate"];
				$this->patient->newVisit($data_visit);


				if ($data_visit["serv_id"] == 2 && isset($visit["vcctsite"]) && $visit["vcctsite"] != "" && isset($visit["vcctnumber"]) && $visit["vcctnumber"] != ""){
					$vcct_info = array();
					$vcct_info["ext_code"] = $visit["vcctnumber"];
					$vcct_info["site_code"] = $visit["vcctsite"];
					$vcct_info["pat_id"] = $data_visit["pat_id"];
					$this->patient->manageVcctNoFpFromOiart($vcct_info);
				}
			}
		}

		private function sync_return_visits($patient_id){
			$visits = $this->patient->getVisitsByPID($patient_id);
			$result = array();
			foreach($visits->result_array() as $row){
				$visit = array();
				$visit["patientid"] = $patient_id;
				$visit["visitid"] = $row["visit_id"];
				$visit["sitecode"] = $row["site_code"];
				$visit["sitename"] = $row["site_name"];
				$visit["externalcode"] = $row["ext_code"];
				$visit["externalcode2"] = $row["ext_code_2"];
				$visit["serviceid"] = $row["serv_id"];
				$visit["info"] = $row["info"];
				$visit["age"] = $row["pat_age"];
				$visit["visitdate"] = $row["visit_date"];
				$visit["refer_to_vcct"] = $row["refer_to_vcct"];
				$visit["refer_to_oiart"] = $row["refer_to_oiart"];
				$visit["refer_to_std"] = $row["refer_to_std"];
				$visit["createdate"] = $row["date_create"];
				$result[] = $visit;
			}
			return $result;
		}

		private function sync_search_patient_by_fingerprint($fingerprints, $patient_params){
			$sdk = GrFingerService::get_instance();
			$patient_list_query = $this->patient->search($patient_params["gender"]);
			$patient_list = $patient_list_query->result_array();

			foreach ($fingerprints as $fingerprint) {
				$ok = $sdk->prepare($patient_params[$fingerprint]);
				if(!$ok){
					 $patient_array["error"] = "Fingerprint ".$fingerprint." is not correct";
					 return $this->render_json($patient_array);
				}

				$patient_list = $this->match_patients_with_fingerprint($fingerprint, $patient_list);
				if(count($patient_list) <= 1)
						break;
			}
			return $patient_list;
		}
}
