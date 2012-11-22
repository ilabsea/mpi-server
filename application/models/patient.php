<?php
class Patient extends CI_Model {
    function search ($gender="", $dob="") {
    	$sql = "SELECT pat_id,
    	               pat_fingerprint,
    	               pat_fingerprint2,
    	               pat_gender,
    	               pat_dob
    	        FROM mpi_patient";
    	$where = "";
    	if ($gender != "") :
    	    $where = "pat_gender=".$gender;
    	endif;
    	
    	if ($where != "") :
    	    $sql.= " WHERE pat_gender = ".$gender;
    	endif;
    	return $this->db->query($sql);
    }
    
    function patient_list($data) {
    	
        $sql = "SELECT p.pat_id, 
                       p.pat_gender, 
                       p.pat_dob,
                       (SELECT COUNT(ps.pat_serv_id) FROM patient_service ps WHERE ps.pat_id = p.pat_id) AS nb_visit
                  FROM mpi_patient p";
        return $this->db->query($sql);
    }
    
    function newPatient($data) {
    	$create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    	$gender = isset($data["gender"]) ? $data["gender"] : "NULL";
    	$fingerprint2 =  isset($data["fingerprint2"]) ? "'".mysql_real_escape_string($data["fingerprint2"])."'" : "NULL";
    	//for ($i=1; $i<=50000;$i++) :
       $pat_id = uniqid(); 
       $sql = "INSERT INTO mpi_patient(pat_id, 
                                       pat_fingerprint,
                                       pat_fingerprint2,
                                       pat_gender,
                                       date_create) 
                                VALUES('".$pat_id."',
                                       '".mysql_real_escape_string($data["fingerprint"])."',
                                       ".$fingerprint2.",
                                       ".$gender.",
                                       ".$create_date.")";
       $this->db->query($sql) or die(mysql_error());
       //endfor;
       return $pat_id;
    }
    
    function getPatientById($pat_id) {
        $sql = "SELECT pat_id,
                       pat_gender,
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
                      ps.ext_code, 
                      ps.visit_date,
                      ps.info
                  FROM patient_service ps
                  LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
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
                      ps.ext_code, 
                      ps.visit_date,
                      ps.info,
                      ps.date_create
                  FROM patient_service ps
                  LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
                  WHERE pat_id = '".mysql_real_escape_string($pid)."'
                  ORDER BY visit_date DESC";
       return $this->db->query($sql);
    }
    
    function newVisit($var) {
    	$create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
        $sql = "INSERT INTO patient_service(pat_id,
                                            serv_id,
                                            site_code,
                                            ext_code,
                                            info,
                                            visit_date,
                                            date_create)
                                      VALUES('".mysql_real_escape_string($var["pat_id"])."',
                                             ".$var["serv_id"].",
                                             '".mysql_real_escape_string($var["site_code"])."',
                                             '".mysql_real_escape_string($var["ext_code"])."',
                                             '".mysql_real_escape_string($var["info"])."',
                                             '".$var["visit_date"]."',
                                             ".$create_date."
                                           )";
        $this->db->query($sql);
        $visit_id = $this->db->insert_id();
        return $visit_id;
    }
}