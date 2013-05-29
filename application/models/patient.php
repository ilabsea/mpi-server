<?php
/**
 * The patient model class
 * @author Sokha RUM
 */
class Patient extends Imodel {
	/**
	 * Searching patient filtering $gender
	 * @param int $gender
	 * @param string $dob
	 */
    function search ($gender="", $dob="") {
    	ILog::debug("search patient");
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
    	        FROM mpi_patient p";
    	        //LEFT JOIN mpi_fingerprint f ON (f.pat_id = p.pat_id)";
    	$where = "";
    	/*if ($gender != "") :
    	    $where = "(p.pat_gender=".$gender." OR p.pat_gender IS NULL)";
    	endif;*/
    	
    	if ($where == "") :
    	    if ($gender != "") :
    	    $sql.= " WHERE (p.pat_gender = ".$gender." OR p.pat_gender IS NULL)";
    	    endif;
    	endif;
    	//$sql.= " LIMIT 0, 1000";
    	//ILog::info($sql);
    	$query = $this->db->query($sql);
    	if (!$query) :
    		ILog::error(mysql_error());
    	endif;
    	return $query;
    }

    /**
     * getting the list of patients list
     * @param array $criteria
     */
    function patient_list($criteria, $start, $rows) {
    	
        $sql = "SELECT p.pat_id, 
                       p.pat_gender,
                       p.pat_age,
                       p.pat_dob,
                       p.date_create,
                       p.pat_register_site,
                       (SELECT COUNT(ps.visit_id) FROM mpi_visit ps WHERE ps.pat_id = p.pat_id) AS nb_visit,
                       (SELECT COUNT(ps.visit_id) FROM mpi_visit ps WHERE ps.pat_id = p.pat_id AND LOWER(ps.info) = 'positive') AS nb_visit_positive,
                       p.new_pat_id
                  FROM mpi_patient p";
        
                  
        $where = $this->generate_where($criteria);
        if ($where != "") : 
        	$sql .= " WHERE ".$where;
        endif;
        
        $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"]."
                  LIMIT ".$start.", ".$rows;
        return $this->db->query($sql);
    }

    /**
     * Generate where clause
     * @param unknown_type $criteria
     */
    private function generate_where($criteria) {
    	$where = "";
        if ($criteria["cri_pat_gender"] != "") :
        	$where .= " AND p.pat_gender =".$criteria["cri_pat_gender"];
        endif;
        
        $sub_where = "";
        
        if ($criteria["date_from"] != "" || $criteria["date_to"]) :
        	if ($criteria["date_from"] != "") :
				$sub_where .= " AND p.date_create >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";
        	endif;
        	
        	if ($criteria["date_to"] != "") :
				$sub_where .= " AND p.date_create <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";
        	endif;
        endif;
        
        $sub_where = trim($sub_where, " AND");
        $sub_where = trim($sub_where);
        
        $sub_sql = "";
        $sub_sql2 = "";
        $sub_sql0 = "";
        
        if ($criteria["date_from"] != "" || $criteria["date_to"]) :
        	if ($criteria["date_from"] != "") :
            	$sub_sql2 .= " AND v.visit_date >= '".date_html_to_mysql($criteria["date_from"])."'";
            endif;
            
            if ($criteria["date_to"] != "") :
            	$sub_sql2 .= " AND v.visit_date <= '".date_html_to_mysql($criteria["date_to"])."'";
            endif;
        endif;
        
        if ($sub_sql2 != "") : 
	        $sub_sql2 = trim($sub_sql2, " AND");
	        $sub_sql2 = trim($sub_sql2);
        endif;
        
        if ($criteria["cri_serv_id"] != "" || $criteria["cri_site_code"] != "" || 
            $criteria["cri_external_code"] != "" || $criteria["cri_external_code2"] != "" ) :
            
            $sub_sql = " EXISTS (SELECT v.visit_id FROM mpi_visit v WHERE v.pat_id = p.pat_id";

            if ($criteria["cri_serv_id"] != "") :
            	$sub_sql .= " AND v.serv_id = ".$criteria["cri_serv_id"];
            endif;
            
            if ($criteria["cri_site_code"] != "") :
            	$sub_sql .= " AND v.site_code = '".mysql_real_escape_string($criteria["cri_site_code"])."'";
            endif;
            
            if ($criteria["cri_external_code"] != "") :
            	$sub_sql .= " AND v.ext_code = '".mysql_real_escape_string($criteria["cri_external_code"])."'";
            endif;
            
            if ($criteria["cri_external_code2"] != "") :
            	$sub_sql .= " AND v.ext_code_2 = '".mysql_real_escape_string($criteria["cri_external_code2"])."'";
            endif;
            
            
        endif;
        
        if ($sub_sql2 != "") :
            if ($sub_sql != "") : 
            	$sub_sql0 = $sub_sql." AND ".$sub_sql2;
            else:
            	$sub_sql0 = " EXISTS (SELECT v.visit_id FROM mpi_visit v WHERE v.pat_id = p.pat_id AND ".$sub_sql2;
            endif;
            $sub_sql0 .= ")";
         endif;
         
         if ($sub_sql != "") :
         	$sub_sql .= ")";
         endif;
        
        if ($sub_where == "") :
           if ($sub_sql != "") :
           	$where .=" AND ".$sub_sql;
           endif;
        else :
            if ($sub_sql == "") :
           		$where .=" AND (".$sub_where." OR ".$sub_sql0.")";
           	else :
           		$where .= " AND ((".$sub_where." AND ".$sub_sql.") OR ".$sub_sql0.")";
           endif;
        endif;
        
        if ($where != "") : 
	        $where = trim($where, " AND");
	        $where = trim($where, " ");
        endif;
        return $where;
    }

	/**
     * Count the patient with the specific criteria
     * @param array $criteria
     */
    function count_patient_list($criteria) {
        $sql = "SELECT count(pat_id) as nb_patient FROM mpi_patient p";
        $where = $this->generate_where($criteria);
        if ($where != "") :
            $sql .= " WHERE ".$where;
        endif; 
        
        $query = $this->db->query($sql);
        if ($query->num_rows() <= 0) :
            return 0;
        endif;
        $row = $query->row_array();
        return $row["nb_patient"];
    }

    /**
     * Creating new 
     * @param array $data
     */
    function newPatientFingerprint($data) {
       $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
       $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
       $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
       $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
       $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
       $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];
       for ($i = 1; $i <= 3000; $i++) :
       //$pat_id = uniqid();


       $pat_id = $this->getPatientIdBySiteCode($sitecode);
       //$this->db->trans_start();
       		$field = "	pat_id, 
						pat_gender,
                        pat_age,
                        pat_dob,
                        pat_register_site,
                        date_create";
       		$values = "'".$pat_id."',
	                                       ".$gender.",
	                                       ".$age.",
	                                       ".$dob.",
	                                       ".$site.",
	                                       ".$create_date;
       
       		foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
	    	    ${$fingerprint} = isset($data[$fingerprint]) &&  $data[$fingerprint] != "" ? "'".mysql_real_escape_string($data[$fingerprint])."'" : "NULL";
	    	    $field .= ",".$fingerprint ;
	    	    $values .= ",".${$fingerprint};
	    	endforeach;
	    	
	       $sql = "INSERT INTO mpi_patient(".$field.") 
	                                VALUES(".$values.")";

	       //ILog::info($sql);
	       $res = $this->db->query($sql);
	       if (!$res) :
	       	   ILog::error(mysql_error());
	       endif;
	       /*$sql1 = "pat_id, ";
	       $sql2 = "'".$pat_id."',";
	       foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
	    	    ${$fingerprint} = isset($data[$fingerprint]) &&  $data[$fingerprint] != "" ? "'".mysql_real_escape_string($data[$fingerprint])."'" : "NULL";
	    	    $sql1 .= $fingerprint."," ;
	    	    $sql2 .= ${$fingerprint}.",";
	    	endforeach;
	    	
	    	$sql1 = trim($sql1, ",");
	    	$sql2 = trim($sql2, ",");
	       
	       $sql = "INSERT INTO mpi_fingerprint(".$sql1.") VALUES (".$sql2.")";
	       $this->db->query($sql);*/
	       
       /*$this->db->trans_complete();
       if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method newPatientFingerprint of patient model. ".$this->db->_error_message());
       }*/
       endfor;
       return $pat_id;
    }

	/**
     * Creating new patient without fingerprint
     * @param array $data
     */
    function newPatient($data) {

       $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
       $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
       $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
       $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
       $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
       $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];
       //for ($i=1; $i<=50000;$i++) :
       //$pat_id = uniqid();

       $pat_id = $this->getPatientIdBySiteCode($sitecode);
       $this->db->trans_start();
       
	       $sql = "INSERT INTO mpi_patient(pat_id, 
	                                       pat_gender,
	                                       pat_age,
	                                       pat_dob,
	                                       pat_register_site,
	                                       date_create) 
	                                VALUES('".$pat_id."',
	                                       ".$gender.",
	                                       ".$age.",
	                                       ".$dob.",
	                                       ".$site.",
	                                       ".$create_date.")";

	       $this->db->query($sql);
       $this->db->trans_complete();
       if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method newPatient without fingerprint of patient model. ".$this->db->_error_message());
       }
       //endfor;
       return $pat_id;
    }

    /**
     * Get patiemt with the specific
     * @param String $pat_id
     */
    function getPatientById($pat_id) {
        $sql = "SELECT pat_id,
                       pat_gender,
                       pat_age,
                       date_create,
                       pat_register_site,
                       pat_dob
                  FROM mpi_patient
                 WHERE pat_id = '".mysql_real_escape_string($pat_id)."'";
        $query = $this->db->query($sql);
        if ($query->num_rows() <= 0) :
            return null;
        endif;
        return $query->row_array();
    }

    /**
     * Getting list of visits with the specific several patient id
     * @param Array $var: the arry of patient id
     */
    function getVisits($var) {
       if (count($var) <= 0 ) :
           return null;
       endif;
       $patient_ids = implode("','", $var);
       $patient_ids = "'".$patient_ids."'";
       $sql = "SELECT ps.visit_id,
       				  ps.pat_id, 
                      ps.serv_id, 
                      s.serv_code,
                      ps.site_code,
                      site.site_name,
                      ps.ext_code, 
                      ps.ext_code_2,
                      ps.visit_date,
                      ps.pat_age,
                      ps.info
                  FROM mpi_visit ps
                  LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
                  LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
                  WHERE pat_id IN (".$patient_ids.")";
       return $this->db->query($sql);
    }

    /**
     * Getting list of visit with the specific patient id
     * @param String $pid the patient id
     */
    function getVisitsByPID($pid) {
       $sql = "SELECT ps.visit_id,
       				  ps.pat_id, 
                      ps.serv_id,
                      s.serv_code, 
                      ps.site_code,
                      site.site_name,
                      ps.ext_code, 
                      ps.ext_code_2,
                      ps.visit_date,
                      ps.info,
                      ps.date_create,
                      ps.pat_age
                  FROM mpi_visit ps
                  LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
                  LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
                  WHERE pat_id = '".mysql_real_escape_string($pid)."'
                  ORDER BY visit_date DESC";
       return $this->db->query($sql);
    }

    /**
     * Creating new visit with the specific variables
     * @param array $var: the array of the variables
     */
    function newVisit($var) {
    	$visit_id = null;
    	$create_date = isset($var["date_create"]) && $var["date_create"] != "" ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    	$age = isset($var["age"]) && $var["age"] != "" ? $var["age"] : "NULL";
    	
    	/** Control if visit already exist */
    	$sql = "SELECT visit_id FROM mpi_visit WHERE  pat_id = '".mysql_real_escape_string($var["pat_id"])."' AND
    	                                              serv_id = ".$var["serv_id"]." AND
    	                                              site_code = '".mysql_real_escape_string($var["site_code"])."' AND
    	                                              ext_code = '".mysql_real_escape_string($var["ext_code"])."' AND
    	                                              visit_date = '".$var["visit_date"]."' AND
    	                                              date_create = ".$create_date;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) :
            $row = $query->row_array();
            $visit_id = $row["visit_id"];
            return $visit_id;
        endif;
    	
        $sql = "INSERT INTO mpi_visit(pat_id,
                                            serv_id,
                                            site_code,
                                            ext_code,
                                            ext_code_2,
                                            info,
                                            visit_date,
                                            pat_age,
                                            date_create)
                                      VALUES('".mysql_real_escape_string($var["pat_id"])."',
                                             ".$var["serv_id"].",
                                             '".mysql_real_escape_string($var["site_code"])."',
                                             '".mysql_real_escape_string($var["ext_code"])."',
                                             '".mysql_real_escape_string($var["ext_code_2"])."',
                                             '".mysql_real_escape_string($var["info"])."',
                                             '".$var["visit_date"]."',
                                             ".$age.",
                                             ".$create_date."
                                           )";
        
        $this->db->query($sql);
        $visit_id = $this->db->insert_id();
        
        if ($age != "NULL") :
        	$sql = "UPDATE mpi_patient SET pat_age = ".$age." WHERE pat_id = '".mysql_real_escape_string($var["pat_id"])."'";
        	$this->db->query($sql);
        endif;
        
        return $visit_id;
    }

    /**
     * Getting the sequence of patient and increase 1 in database
     * @param int $province: the province id
     */
    private function getPatientSeqProId($province) {
    	$seq = -1;
    	$this->db->trans_start();
    	
        $sql = "SELECT pro_pat_seq 	FROM mpi_province 
        							WHERE pro_id = ".$province;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 1) :
            $row = $query->row_array();
            $seq = $row["pro_pat_seq"];
        endif;
        
        if ($seq < 0) :
            return -1;
        endif;
        
        $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_id = ".$province;
        $query = $this->db->query($sql);
    	$this->db->trans_complete();
       	if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());
       	}
       	
       	return $seq;
    }

    /**
     * Getting the sequence of patient and increase 1 in database
     * @param int $province: the province code
     */
    private function getPatientSeqProCode($province) {
    	$seq = -1;
    	$this->db->trans_start();
    	
        $sql = "SELECT pro_pat_seq 	FROM mpi_province 
        							WHERE pro_code = '".mysql_real_escape_string($province)."'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) :
            $row = $query->row_array();
            $seq = $row["pro_pat_seq"];
        endif;
        
        if ($seq < 0) :
            return -1;
        endif;
        $seq++;
        $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_code = '".mysql_real_escape_string($province)."'";
        $query = $this->db->query($sql);
    	$this->db->trans_complete();
       	if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());
       	}
       	
       	return $seq;
    }

    /**
     * Return patient id
     * @param String $sitecode: province code
     * @param String $country: country code
     */
    private function getPatientIdBySiteCode ($sitecode, $country="KH") {
    	$version = 1;
    	$sitemodel = $this->load_other_model("site");
    	$site = $sitemodel->getSiteByCode($sitecode);
    	$seq = $this->getPatientSeqProCode($site["pro_code"]);
    	$pid = $country.str_pad($site["pro_code"], 3, "0", STR_PAD_LEFT).$version.str_pad($seq, 8, "0", STR_PAD_LEFT);
    	return $pid;
    }


    /**
     * Update replace patient id
     * @param string $old_masterid
     * @param string $new_patientid
     */
    function updateReplacePatientId($old_masterid, $new_patientid) {
    	$this->db->trans_start();
    	$sql = "UPDATE mpi_visit SET pat_id = '".mysql_real_escape_string($new_patientid)."'
    						WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    	$query = $this->db->query($sql);
    	
    	$sql = "UPDATE mpi_patient SET new_pat_id = '".mysql_real_escape_string($new_patientid)."' 
    						WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    	$query = $this->db->query($sql);
    	
    	$this->db->trans_complete();
       	if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method updateReplacePatientId of patient model. ".$this->db->_error_message());
       	}
    }
}