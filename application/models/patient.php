<?php
/**
 * The patient model class
 * @author Sokha RUM
 */
class Patient extends CI_Model {
    function search ($gender="", $dob="") {
    	$sql = "SELECT p.pat_id,
    	               p.pat_gender,
    	               p.pat_dob,
    	               p.pat_age,
    	               f.fingerprint_r1,
    	               f.fingerprint_r2,
    	               f.fingerprint_r3,
    	               f.fingerprint_r4,
    	               f.fingerprint_r5,
    	               f.fingerprint_l1,
    	               f.fingerprint_l2,
    	               f.fingerprint_l3,
    	               f.fingerprint_l4,
    	               f.fingerprint_l5
    	        FROM mpi_patient p
    	        LEFT JOIN mpi_fingerprint f ON (f.pat_id = p.pat_id)";
    	$where = "";
    	/*if ($gender != "") :
    	    $where = "(p.pat_gender=".$gender." OR p.pat_gender IS NULL)";
    	endif;*/
    	
    	if ($where != "") :
    	    $sql.= " WHERE (p.pat_gender = ".$gender." OR p.pat_gender IS NULL)";
    	endif;
    	return $this->db->query($sql);
    }
    
    /**
     * getting the list of patients list
     * @param unknown_type $data
     */
    function patient_list($data) {
    	
        $sql = "SELECT p.pat_id, 
                       p.pat_gender,
                       p.pat_age,
                       p.pat_dob,
                       (SELECT COUNT(ps.pat_serv_id) FROM mpi_visit ps WHERE ps.pat_id = p.pat_id) AS nb_visit
                  FROM mpi_patient p";
        return $this->db->query($sql);
    }
    
    /**
     * Creating new 
     * @param unknown_type $data
     */
    function newPatientFingerprint($data) {
       $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
       $gender = isset($data["gender"]) ? $data["gender"] : "NULL";
       $age = isset($data["age"]) ? $data["age"] : "NULL";
       $dob = isset($data["birthdate"]) ? "'".$data["birthdate"]."'" : "NULL";

    	//for ($i=1; $i<=50000;$i++) :
       $pat_id = uniqid(); 
       $this->db->trans_start();
       
	       $sql = "INSERT INTO mpi_patient(pat_id, 
	                                       pat_gender,
	                                       pat_age,
	                                       pat_dob,
	                                       date_create) 
	                                VALUES('".$pat_id."',
	                                       ".$gender.",
	                                       ".$age.",
	                                       ".$dob.",
	                                       ".$create_date.")";
	       $this->db->query($sql) or die(mysql_error());
	       
	       $sql1 = "pat_id, ";
	       $sql2 = "'".$pat_id."',";
	       foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
	    	    ${$fingerprint} = isset($data[$fingerprint]) &&  $data[$fingerprint] != "" ? "'".mysql_real_escape_string($data[$fingerprint])."'" : "NULL";
	    	    $sql1 .= $fingerprint."," ;
	    	    $sql2 .= ${$fingerprint}.",";
	    	endforeach;
	    	
	    	$sql1 = trim($sql1, ",");
	    	$sql2 = trim($sql2, ",");
	       
	       $sql = "INSERT INTO mpi_fingerprint(".$sql1.") VALUES (".$sql2.")";
	       $this->db->query($sql) or die(mysql_error());
       $this->db->trans_complete();
       if ($this->db->trans_status() === FALSE) {
        	throw new Exception("There is error during calling method newPatientFingerprint of patient model. ".$this->db->_error_message());
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
       $sql = "SELECT ps.pat_id, 
                      ps.serv_id, 
                      s.serv_code,
                      ps.site_code,
                      site.site_name,
                      ps.ext_code, 
                      ps.ext_code_2,
                      ps.visit_date,
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
       $sql = "SELECT ps.pat_id, 
                      ps.serv_id,
                      s.serv_code, 
                      ps.site_code,
                      site.site_name,
                      ps.ext_code, 
                      ps.ext_code_2,
                      ps.visit_date,
                      ps.info,
                      ps.date_create
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
    	$create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
        $sql = "INSERT INTO mpi_visit(pat_id,
                                            serv_id,
                                            site_code,
                                            ext_code,
                                            ext_code_2,
                                            info,
                                            visit_date,
                                            date_create)
                                      VALUES('".mysql_real_escape_string($var["pat_id"])."',
                                             ".$var["serv_id"].",
                                             '".mysql_real_escape_string($var["site_code"])."',
                                             '".mysql_real_escape_string($var["ext_code"])."',
                                             '".mysql_real_escape_string($var["ext_code_2"])."',
                                             '".mysql_real_escape_string($var["info"])."',
                                             '".$var["visit_date"]."',
                                             ".$create_date."
                                           )";
        $this->db->query($sql);
        $visit_id = $this->db->insert_id();
        return $visit_id;
    }
}