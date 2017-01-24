<?php
/**
 * The member webservice class
 * @author Sokha RUM
 */
class Memberws extends MpiController {
	/**
	 * Construction of Memer web service
	 */
	function skip_authentication(){
		return true;
	}

	/**
	 * Register New Member
	 * $params = array("sitecode"=>"0202","member_login"=>"sokmesa","member_pwd"=>"sokmesa",
	 *						 "member_fp_r2"=>"p/8BHoQAwQABawCVANIAAWsAnQDJAAFxALYAnwABNgGLAN0AAR8BlQCgAAF8AJsA6QABJQF4AP8AAR8BwwDJAAFxAMIAsAABdwCzAN4AASUBzwDNAAErAdAAjwABggCdAEgAAZ4AWACwAAFPANgAVgACOwGNAGwAAaQAXQAUAQElAcYAIQEBOwFjAHIAASIAqgAjAQE7AWYAUQABEQCNADoAAV0BogAlAAGeAHEArQABZgBkAK4AAVoAfgCYAAJ3ANcAGQEBNgFVADUBASsB8gA9AQGHAK4ADhkBAgsIGRgEARIbBgQNFgkDAQAFGgYBDhgICRgaAgAGCgoIBAIUEgAYBAAKAhYXAwwGAgoBCwkFAxMVCgscEREHGRoNFwkMABkABQIIGAUHBBANCgQAGgcGAgUTEAgDFRYOGgIJHRsUGwYAGhAaEwEIAA4QFQkFCgkCAwsCAQUQFhkFBggBGB0SCwMCGAUQBwEEGAoAFQ0BCQMaDA8BCwYLCAwECBQGGRMAAwQZDw0FDAEZGBMLDBQHBwAOEw4FHAcHAgMQBRMEDhsKEgoBDhQKExYSBhgQEQQTDQ8XFRcQFxoVEQYMEBsLGwYCDB0UFAQQDxQRGRAPFgMPGwgUARILEgcHDhoNDhAJEAwNEQEcFAYOEggRAAUVCQ8ZFRgVAhAaFg4VGwQTFxECEQ4FDwUWHAYcBB0KHBIMFx0LHAELDxoXHQYcAB0IHA4dBxsMFA4dER0cHQwdEB0P",
	 *						 "member_fp_l2"=>"p/8BHo8A+wAC1gCKAEkAAVUApgC+AAEDAXcA9QAB4QC9AGcAAVoAlQCrAAFPAKAAoAABCQGgAP0AAdwAkwBVAAFVAEEAigACSgB5AFYAAk8AnABiAAFVALEAAQEB1gCFAJQAAU8AWgDSAAHnAKcARwABYACOAIAAAgMBqwCcAAJVAJsAIQABdwDPAL4AAVUAYQDBAAI+AHAAyAAB8gBsALMAAfgA2wDrAAH4AHgAOgABTwB3ADAAATkAXQDjAAEzAJAAJgEBxQCxABIBAcsASwC6AAHyAJ8AGBkGEQgBCwgFBgcAHAwVFAwHGg4UFg4UCgEVFg0QFB0BGAgPDhUAAwIFCggFERwHDh0KGAUNCw8BDwYNARkLAQIGAxoQCxoVBAsaFAIRHRYMAA4WCwoGEAgYChkZEhENGxwEDxYNERAPEhUdAhMcAAcDFgUbAAESGBIQCBsHBRADDhodBAgDFQgZFQUMFxAKFxMKDw8YHQkbDBMRGhYCDQ8ZCBIQBAsYFgYbAw0LEAEAGhUCEwYDFBUNFAURBBwXFA0WCQwDAhYEAQAVEQsTBQcXEA8LGQYLFQYHAgoSDQoGBBQJAAILEhwDAhAADgwCDQkdDQQKFwIOBQMCBxoHFRAYDBMDHQkKDgkJEAAXBBIVCQcTAAUQGRsaHAITDRMEHBMTEAUJAgQXERcFGxcJGAkBBgkJCxsVCRkbAhcE");
	 */
	function register() {
  	$result = array("patients" => array(),
  	                "error" => "");
    // get the valid fingerprint from the request
		$params = $_POST;
    $fingerprints = $this->valid_user_fp($params);
    if($result["error"] != "") :
         echo $result["error"];
         return;
    endif;

    if (count($fingerprints) <= 0) :
         echo "Could not find fingerprint";
         return;
    endif;

    if (!isset($params["sitecode"]) || $params["sitecode"] == "") :
         //$result["error"] = "Could not find site code";
			$params["sitecode"] = "0202";
      //  echo "Site code is required";
      //  return;
    endif;

    $this->load->model("site");
    $site = $this->site->getSiteByCode($params["sitecode"]);
    if ($site == null) :
         //$result["error"] = "Site code is not correct";
       echo "Site code ".$params["sitecode"]." is not found";
       return;
    endif;

    $data = array();
    foreach ($fingerprints as $fingerprint) :
    	$data[$fingerprint] = $params[$fingerprint];
    endforeach;

    $data["member_login"] = $params["member_login"];
    $data["member_pwd"] = $params["member_pwd"];
    $data["site_code"] = $params["sitecode"];
		if(isset($params["member_code"]) && $params["member_code"] != "")
			$data["member_code"] = $params["member_code"];
    foreach ($fingerprints as $fingerprint) :
    	$data[$fingerprint] = $params[$fingerprint];
    endforeach;

    $this->load->model("member");
    $memberFound = $this->member->getMemberBySiteCodeAndLogin($data["site_code"], $data["member_login"]);
    if ($memberFound != null) :
    	 echo "Login ".$data["member_login"]." is found on server";
         return;
    endif;
    // $this->member->create($data);
		$member = new Member();
    $member->set_attributes($data);
    $member->save();
    echo "success";
	}

	/**
	 * Getting the available fingerprint
	 * @param Object $grFingerprint
	 * @param Array $result
	 * @param Array $reference
	 */
    private function valid_user_fp($params) {
			$sdk = GrFingerService::get_instance();
			$arr = array();
    	foreach (Iconstant::$MPI_USER_FP as $fingerprint){
    	  if (isset($params[$fingerprint]) && $params[$fingerprint] != ""){
        	$ok = $sdk->prepare($params[$fingerprint]);
      		if (!$ok){
						$result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
						return FALSE;
					}
      		else
            $arr[] = $fingerprint;
	      }
    	}
      return $arr;
    }
}
