<?php
class Report extends Imodel {
	function get_oi_art_report_data($criteria) {
		$sites = $this->get_site_by_province(2, $criteria["cri_pro_code"]);
		
		if (count($sites) <= 0) :
			return null;
		endif;
		
		$site_code_sql = implode("','", array_keys($sites));
		
		
		$query = $this->get_fp_patient_by_site($site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_register"] = $row["nb_register"];
			endif;
		endforeach;
		
		
		
		// Number of Patients with other person come and take drug on their behalf
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.info LIKE '%On behave of patient%'";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT COUNT(*) AS nb_on_behave, pat_register_site 
				FROM mpi_patient p
				WHERE pat_register_site IN ('".$site_code_sql."') AND
					  EXISTS (".$sub_sql.")";
		$sql .= " GROUP BY pat_register_site";
		
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_on_behave"] = $row["nb_on_behave"];
			endif;
		endforeach;
		
		// Number of Patients refer to STD
		$query = $this->get_patient_reach_service(3, $site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_reach_std"] = $row["nb_reach"];
			endif;
		endforeach;
		
		return $sites;
	}
	
	function get_vcct_report_data($criteria) {
		$sites = $this->get_site_by_province(1, $criteria["cri_pro_code"]);
		if (count($sites) <= 0) :
			return null;
		endif;
		
		$site_code_sql = implode("','", array_keys($sites));
		
		$query = $this->get_fp_patient_by_site($site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_register"] = $row["nb_register"];
			endif;
		endforeach;
		
		
		// Number of Patients positive
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					LCASE(v.info) = 'positive'";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT COUNT(*) AS nb_positive, pat_register_site 
				FROM mpi_patient p
				WHERE pat_register_site IN ('".$site_code_sql."') AND
					  EXISTS (".$sub_sql.")";
		$sql .= " GROUP BY pat_register_site";
		
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_positive"] = $row["nb_positive"];
			endif;
		endforeach;
		
		
		// Number of Patients refer to OI/ART
		$query = $this->get_patient_reach_service(2, $site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_reach_oiart"] = $row["nb_reach"];
			endif;
		endforeach;
		
		// Number of Patients refer to STD
		$query = $this->get_patient_reach_service(3, $site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_reach_std"] = $row["nb_reach"];
			endif;
		endforeach;
		
		return $sites;
	}
	
	/**
	 * Get site list with specific province
	 * @param string $pro_code
	 */
	private function get_site_by_province($serv_id, $pro_code) {
		$sites = array();
		$sql = "SELECT 	site_code, 
						site_name, 
						od_name 
				FROM mpi_site
				WHERE pro_code = '".mysql_real_escape_string($pro_code)."' AND
				      serv_id = ".$serv_id."
				      ORDER BY site_code"; 
		$query = $this->db->query($sql);
		$sites = array();
		
		foreach ($query->result_array() as $row) :
			$sites[$row["site_code"]] = $row;
		endforeach;
		return $sites;
	}
	
	private function get_fp_patient_by_site($site_code_sql, $criteria) {
		$sql = "SELECT 	COUNT(*) AS nb_register, 
						pat_register_site 
				FROM mpi_patient
				WHERE pat_register_site IN ('".$site_code_sql."') ";
		
		if ($criteria["date_from"] != "") :
			$sql .= " AND date_create >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sql .= " AND date_create <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
        $sql .= " GROUP BY pat_register_site";
        
		$query = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $serv_id the id of service
	 * 				1 - VCCT
	 * 				2 - OI/ART
	 * 				3 - STD
	 * @param string $site_code_sql
	 * @param array $criteria
	 */
	private function get_patient_reach_service($serv_id, $site_code_sql, $criteria) {
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.serv_id = ".$serv_id;
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT COUNT(pat_id) AS nb_reach, pat_register_site 
				FROM mpi_patient p
				WHERE pat_register_site IN ('".$site_code_sql."') AND
					  EXISTS (".$sub_sql.")";
		$sql .= " GROUP BY pat_register_site";
		
		$query = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * Get STD report data
	 * @param array $criteria
	 */
	function get_std_report_data ($criteria) {
		$sites = $this->get_site_by_province(3, $criteria["cri_pro_code"]);
		if (count($sites) <= 0) :
			return null;
		endif;
		
		$site_code_sql = implode("','", array_keys($sites));
		
		$query = $this->get_fp_patient_by_site($site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_register"] = $row["nb_register"];
			endif;
		endforeach;
		
		
		// Number of Patients refer to VCCT
		$query = $this->get_patient_reach_service(1, $site_code_sql, $criteria);
		foreach ($query->result_array() as $row) :
			if (key_exists($row["pat_register_site"], $sites)) :
				$sites[$row["pat_register_site"]]["nb_reach_vcct"] = $row["nb_reach"];
			endif;
		endforeach;
		
		return $sites;
	}
	
	/**
	 * Duplicate
	 * @param array $criteria
	 */
	function duplicate_oiart_register($criteria) {
		$sql = "SELECT COUNT(v.pat_id) AS nb_register,
						v.pat_id,
						p.pat_gender,
						p.pat_age,
						p.pat_register_site
							FROM mpi_visit v
							LEFT JOIN mpi_patient p ON (p.pat_id = v.pat_id)
							WHERE v.serv_id = 2 AND v.info = 'Initial Visit'";
		
		if ($criteria["date_from"] != "") :
			$sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql .= "
							GROUP BY v.pat_id, p.pat_gender, p.pat_age
							HAVING nb_register > 1";
		
		$query = $this->db->query($sql);
		$result = array();
		foreach ($query->result_array() as $row) :
			array_push($result, $row);
		endforeach;
		return $result;
	}
	
	/**
	 * duplicate test
	 * @param array $criteria
	 * @return Ambigous <multitype:, multitype:number , unknown>
	 */
	function duplicate_vcct_test($criteria) {
		$sql = "SELECT 	COUNT(visit_id) as nb_duplicate,
						pat_id
						FROM mpi_visit v
						WHERE serv_id = 1";
		
		if ($criteria["date_from"] != "") :
			$sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql .= "
							GROUP BY pat_id
							HAVING nb_duplicate > 1";
		
		$query = $this->db->query($sql);
		$return = array();
		foreach ($query->result_array() as $row) :
			$sql1 = "SELECT COUNT(v.visit_id) AS nb, v.info
						FROM mpi_visit v WHERE pat_id = '".$row["pat_id"]."'";
			if ($criteria["date_from"] != "") :
				$sql1 .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
	        endif;
	        	
	        if ($criteria["date_to"] != "") :
				$sql1 .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
	        endif;
	        
	        $sql1 .= " GROUP BY info";
	        $query1 = $this->db->query($sql1);
	        $return[$row["pat_id"]] = array("positive" => 0, "negative" =>0);
	        foreach($query1->result_array() as $row1) :
	        	$return[$row["pat_id"]][strtolower($row1["info"])] = $row1["nb"];
	        endforeach;
	        unset($query1);
		endforeach;
		
		return $return;
	}
	
	function get_routine_report_data($criteria) {
		$sites = array();
		$sql = "SELECT 	site_code, 
						site_name, 
						od_name 
				FROM mpi_site
				WHERE pro_code = '".mysql_real_escape_string($criteria["cri_pro_code"])."'
				      ORDER BY site_code"; 
		$query = $this->db->query($sql);
		$sites = array();
		if ($criteria["date_from"] != "") :
			$criteria["date_from"] = date_html_to_mysql($criteria["date_from"]);
		endif;
		
		if ($criteria["date_to"] != "") :
			$criteria["date_to"] = date_html_to_mysql($criteria["date_to"]);
		endif;
		$to_sort = array();
		foreach ($query->result_array() as $row) :
			$sites[$row["site_code"]] = $row;
			$dir_path = FCPATH.APPPATH."logs/".$row["site_code"];
			if (file_exists($dir_path)) :
				$handler = opendir($dir_path);
				$files = array();
				while ($file = readdir($handler)) {
				   if ($file != "." && $file != "..") {
				      array_push($files, $file);
				   }
				}
				
				rsort($files, SORT_STRING);
				$files_total = count($files);
			
				
				foreach ($files as $file) :
					$file_date = str_replace("MPI_log-", "", $file);
					$file_date = str_replace(".php", "", $file_date);
					$file_date = str_replace("_", "-", $file_date);
					
					if ($criteria["date_from"] != "" && strcmp($criteria["date_from"], $file_date) > 0) :
						continue;
			        endif;

			        if ($criteria["date_to"] != "" && strcmp($criteria["date_to"], $file_date) < 0) :
						continue;
			        endif;
					
					$content = file_get_contents($dir_path."/".$file);
					if (strpos($content, " --> Synchronization") !== FALSE) :
						$arr_found = explode(" --> Synchronization", $content);
						$last_sync_date = substr($arr_found[count($arr_found) - 2], -19);
						$sites[$row["site_code"]]["last_sync_date"] = $last_sync_date;
						$now = new DateTime();
						$sync_date = mysql_datetime_to_date($last_sync_date);
						$interval = $now->diff($sync_date);
						$sites[$row["site_code"]]["period_from_now"] = $interval->format("%a");
						break;
					endif;
					
				endforeach;
				closedir($handler);
			endif;
			$val = isset($sites[$row["site_code"]]["period_from_now"]) ? $sites[$row["site_code"]]["period_from_now"] : null;
			array_push($to_sort, $val);
			array_multisort($to_sort, SORT_DESC, SORT_NUMERIC, $sites);
		endforeach;
		return $sites;
	}
	
	function get_fingerprint_report_data($criteria) {
		$sql = "SELECT p.pat_id,
    	               p.pat_gender,
    	               p.pat_dob,
    	               p.pat_age,
    	               p.pat_register_site,
    	               p.date_create,
    	               p.fingerprint_r1,
    	               p.fingerprint_r2,
    	               p.fingerprint_r3,
    	               p.fingerprint_r4,
    	               p.fingerprint_r5,
    	               p.fingerprint_l1,
    	               p.fingerprint_l2,
    	               p.fingerprint_l3,
    	               p.fingerprint_l4,
    	               p.fingerprint_l5
    	        FROM mpi_patient p 
    	        WHERE (p.fingerprint_r1 IS NOT NULL OR 
    	        	   p.fingerprint_r2 IS NOT NULL OR
    	        	   p.fingerprint_r3 IS NOT NULL OR
    	        	   p.fingerprint_r4 IS NOT NULL OR
    	        	   p.fingerprint_r5 IS NOT NULL OR
    	        	   p.fingerprint_l1 IS NOT NULL OR
    	        	   p.fingerprint_l2 IS NOT NULL OR
    	        	   p.fingerprint_l3 IS NOT NULL OR
    	        	   p.fingerprint_l4 IS NOT NULL OR
    	        	   p.fingerprint_l5 IS NOT NULL) ";
		if ($criteria["date_from"] != "") :
			$sql .= " AND p.date_create >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sql .= " AND p.date_create <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		$query = $this->db->query($sql);
		
		$result = array();
		foreach ($query->result_array() as $row) :
			$result[$row["pat_id"]] = $row;
		endforeach;
		return $result;
	}
	
	function vcct_show_at_oi ($site, $criteria) {
		
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.serv_id = 2";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT pat_id, pat_age, pat_gender, pat_register_site, 
						(SELECT COUNT(visit_id) 
						 FROM mpi_visit v
						 WHERE v.pat_id = p.pat_id
						 ) as nb_visit 
				FROM mpi_patient p
				WHERE pat_register_site = '".$site."' AND 
					  EXISTS (".$sub_sql.")";
		
		$query = $this->db->query($sql);
		
		$result = array();
		foreach($query->result_array() as $row) :
			array_push($result, $row);
		endforeach;
		return $result;
	}
	
	function vcct_show_at_std($site, $criteria) {
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.serv_id = 3";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT pat_id, pat_age, pat_gender, pat_register_site, 
						(SELECT COUNT(visit_id) 
						 FROM mpi_visit v
						 WHERE v.pat_id = p.pat_id
						 ) as nb_visit 
				FROM mpi_patient p
				WHERE pat_register_site = '".$site."' AND 
					  EXISTS (".$sub_sql.")";
		
		$query = $this->db->query($sql);
		
		$result = array();
		foreach($query->result_array() as $row) :
			array_push($result, $row);
		endforeach;
		return $result;
	}
	
	function oiart_show_at_std($site, $criteria) {
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.serv_id = 3";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT pat_id, pat_age, pat_gender, pat_register_site, 
						(SELECT COUNT(visit_id) 
						 FROM mpi_visit v
						 WHERE v.pat_id = p.pat_id
						 ) as nb_visit 
				FROM mpi_patient p
				WHERE pat_register_site = '".$site."' AND 
					  EXISTS (".$sub_sql.")";
		
		$query = $this->db->query($sql);
		
		$result = array();
		foreach($query->result_array() as $row) :
			array_push($result, $row);
		endforeach;
		return $result;
	}
	
	function std_show_at_vcct($site, $criteria) {
		$sub_sql = "SELECT pat_id FROM mpi_visit v 
					  			  WHERE v.pat_id = p.pat_id AND
					  					v.serv_id = 1";
		if ($criteria["date_from"] != "") :
			$sub_sql .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        endif;
        	
        if ($criteria["date_to"] != "") :
			$sub_sql .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        endif;
		
		$sql = "SELECT pat_id, pat_age, pat_gender, pat_register_site, 
						(SELECT COUNT(visit_id) 
						 FROM mpi_visit v
						 WHERE v.pat_id = p.pat_id
						 ) as nb_visit 
				FROM mpi_patient p
				WHERE pat_register_site = '".$site."' AND 
					  EXISTS (".$sub_sql.")";
		
		$query = $this->db->query($sql);
		
		$result = array();
		foreach($query->result_array() as $row) :
			array_push($result, $row);
		endforeach;
		return $result;
	}
	
	function number_visit($list_pat_id) {
		$sql = "SELECT pat_id, (SELECT COUNT(visit_id)  
		                         FROM mpi_visit v WHERE v.pat_id = p.pat_id) as nb_visit 
		        FROM mpi_patient p 
		        WHERE p.pat_id IN ('".$list_pat_id."')";
		$query = $this->db->query($sql);
		$result = array();
		foreach($query->result_array() as $row) :
			$result[$row["pat_id"]] = $row["nb_visit"];
		endforeach;
		return $result;
	}
}