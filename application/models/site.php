<?php
/**
 * Site model
 * @author Sokha RUM
 */
class Site extends Imodel {
	/**
	 * Getting site information with the specific site code
	 * @param string $site_code
	 */
    function getSiteByCode ($site_code) {
    	
       $sql = "SELECT 	site_id, 
       					site_code, 
       					site_name, 
       					pro_code, 
       					serv_id 
       				FROM mpi_site
       			   WHERE site_code = '".mysql_real_escape_string($site_code)."'";
       $query = $this->db->query($sql);
        if ($query->num_rows() <= 0) :
            return null;
        endif;
        return $query->row_array();
   }
   
   /**
    * Search sites with specific criteria
    * @param array $criteria
    */
   function getSites($criteria, $start, $rows) {
       $sql = "SELECT s.site_id,
                      s.site_code,
                      s.site_name,
                      s.pro_code,
                      s.serv_id,
                      pr.pro_name,
                      ser.serv_code
                FROM mpi_site s
                LEFT JOIN mpi_province pr ON (pr.pro_code = s.pro_code)
                LEFT JOIN mpi_service ser ON (ser.serv_id = s.serv_id)";
       
       $where = $this->generate_where($criteria);
       if ($where != "") :
       		$sql .= " WHERE ".$where;
       endif;
       $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"]."
                  LIMIT ".$start.", ".$rows;
       return $this->db->query($sql);
   }
   
	private function generate_where($criteria) {
    	$where = "";
        
    	if ($criteria["cri_serv_id"] != "") :
             $where .= " AND s.serv_id = ".$criteria["cri_serv_id"];
        endif;
        
        if ($criteria["cri_pro_code"] != "") :
            	$where .= " AND s.pro_code = '".mysql_real_escape_string($criteria["cri_pro_code"])."'";
        endif;
        
        if ($criteria["cri_site_code"] != "") :
            	$where .= " AND s.site_code LIKE '%".mysql_real_escape_string($criteria["cri_site_code"])."%'";
        endif;
    	
        if ($where != "") :
            $where = trim($where, " AND");
	        $where = trim($where, " ");
        endif;
        
        return $where;
        
    }
   
    /**
     * Getting the provinces
     */
	function getProvinces() {
       $sql = "SELECT pro_code,
                      pro_name,
                      pro_pat_seq
                 FROM mpi_province
                ORDER BY pro_name ASC";
       return $this->db->query($sql);
   }
   
   /**
    * Count number of sites
    * @param array $criteria
    */
   function count_site_list($criteria) {
   	   $sql = "SELECT COUNT(site_id) as nb_site FROM mpi_site s";
   	   $where = $this->generate_where($criteria);
       if ($where != "") :
       		$sql .= " WHERE ".$where;
       endif;
       $query = $this->db->query($sql);
       if ($query->num_rows() <= 0) :
            return 0;
       else :
           $row = $query->row_array();
           return $row["nb_site"];
       endif;
   }
   
   
}