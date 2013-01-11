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
}