<?php
/**
 * Member Model
 * @author Sokha RUM
 */
class Member extends Imodel {
	/**
	 * Getting members within a site with specific site code
	 */
	function getMemberBySiteCode($sitecode) {
	    $sql = "SELECT member_id,
	                   member_login,
	                   member_fp_r1,
	                   member_fp_r2,
	                   member_fp_r3,
	                   member_fp_r4,
	                   member_fp_r5,
	                   member_fp_l1,
	                   member_fp_l2,
	                   member_fp_l3,
	                   member_fp_l4,
	                   member_fp_l5
	              FROM mpi_member 
	             WHERE site_code = '".mysql_real_escape_string($sitecode)."'";
	    return $this->db->query($sql);
	}
	
	/**
	 * Getting members by Id
	 */
	function getMemberById($memberId) {
	    $sql = "SELECT member_id,
	                   member_login,
	                   member_fp_r1,
	                   member_fp_r2,
	                   member_fp_r3,
	                   member_fp_r4,
	                   member_fp_r5,
	                   member_fp_l1,
	                   member_fp_l2,
	                   member_fp_l3,
	                   member_fp_l4,
	                   member_fp_l5
	              FROM mpi_member
	             WHERE member_id = '".mysql_real_escape_string($memberId)."'";
	    $query =  $this->db->query($sql);
	    if ($query->num_rows <= 0) :
	        return null;
	    endif;
	    return $query->row_array();
	}
	
	/**
	 * Getting the member with the specific site code and specific login
	 * @param String $sitecode
	 * @param Login $login
	 */
    function getMemberBySiteCodeAndLogin($sitecode, $login) {
	    $sql = "SELECT member_id,
	                   member_login,
	                   member_fp_r1,
	                   member_fp_r2,
	                   member_fp_r3,
	                   member_fp_r4,
	                   member_fp_r5,
	                   member_fp_l1,
	                   member_fp_l2,
	                   member_fp_l3,
	                   member_fp_l4,
	                   member_fp_l5
	              FROM mpi_member
	             WHERE site_code = '".mysql_real_escape_string($sitecode)."' AND
	             member_login = '".mysql_real_escape_string($login)."'";
	    $query =  $this->db->query($sql);
	    if ($query->num_rows <= 0) :
	        return null;
	    endif;
	    return $query->row_array();

	}
	
	/**
	 * Insert member into database
	 * @param array $var
	 */
	function createNew($var) {
	   $sql1 = "member_login, member_pwd, site_code, date_create, ";
       $sql2 = "'".mysql_real_escape_string($var["member_login"])."', SHA1('".mysql_real_escape_string($var["member_pwd"])."'),'"
       					.mysql_real_escape_string($var["site_code"])."', CURRENT_TIMESTAMP(),";
       foreach (Iconstant::$MPI_USER_FP as $fingerprint) :
    	    ${$fingerprint} = isset($var[$fingerprint]) &&  $var[$fingerprint] != "" ? "'".mysql_real_escape_string($var[$fingerprint])."'" : "NULL";
    	    $sql1 .= $fingerprint."," ;
    	    $sql2 .= ${$fingerprint}.",";
    	endforeach;
    	
    	$sql1 = trim($sql1, ",");
    	$sql2 = trim($sql2, ",");
       
       $sql = "INSERT INTO mpi_member(".$sql1.") VALUES (".$sql2.")";
       $this->db->query($sql);
	}
	
	
   /**
    * Search members with specific criteria
    * @param array $criteria
    */
   function getMembers($criteria, $start, $rows) {
       $sql = "SELECT m.member_id,
                      m.member_login,
                      s.site_id,
                      s.site_code,
                      s.site_name,
                      s.pro_code,
                      s.serv_id,
                      pr.pro_name,
                      ser.serv_code,
                      m.date_create
                FROM mpi_member m, mpi_site s
                LEFT JOIN mpi_province pr ON (pr.pro_code = s.pro_code)
                LEFT JOIN mpi_service ser ON (ser.serv_id = s.serv_id)
                WHERE m.site_code = s.site_code ";
       
       $where = $this->generate_where($criteria);
       if ($where != "") :
       		$sql .= " AND ".$where;
       endif;
       $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"]."
                  LIMIT ".$start.", ".$rows;
       return $this->db->query($sql);
   }
	
	/**
	 * Generate where clause
	 * @param array $criteria
	 */
    private function generate_where($criteria) {
    	$where = "";
        
    	if ($criteria["cri_serv_id"] != "") :
             $where .= " AND s.serv_id = ".$criteria["cri_serv_id"];
        endif;
        
        if ($criteria["cri_site_code"] != "") :
            	$where .= " AND s.site_code LIKE '%".mysql_real_escape_string($criteria["cri_site_code"])."%'";
        endif;
        
        if ($criteria["cri_member_login"] != "") :
            	$where .= " AND m.member_login LIKE '%".mysql_real_escape_string($criteria["cri_member_login"])."%'";
        endif;
    	
        if ($where != "") :
            $where = trim($where, " AND");
	        $where = trim($where, " ");
        endif;
        
        return $where;
        
    }
	
	
   /**
    * Count number of members
    * @param array $criteria
    */
   function count_member_list($criteria) {
   	   $sql = "SELECT COUNT(member_id) as nb_member FROM mpi_member m, mpi_site s
   	                    LEFT JOIN mpi_service ser ON (ser.serv_id = s.serv_id)
   	   					WHERE m.site_code = s.site_code";
   	   $where = $this->generate_where($criteria);
       if ($where != "") :
       		$sql .= " AND ".$where;
       endif;
       $query = $this->db->query($sql);
       if ($query->num_rows() <= 0) :
            return 0;
       else :
           $row = $query->row_array();
           return $row["nb_member"];
       endif;
   }
   
   function delete_member($member_id) {
   		$sql = "DELETE FROM mpi_member WHERE member_id = '".mysql_real_escape_string($member_id)."'";
   		$this->db->query($sql);
   }
}