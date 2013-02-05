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
	             WHERE site_code = '".mysql_real_escape_string($sitecode)."'";
	    return $this->db->query($sql);
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
}