<?php
/**
 * Service Model
 * @author Sokha RUM
 */
class Service extends Imodel {
	/**
	 * Getting All services
	 */
    function getServices() {
        $sql = "SELECT serv_id, serv_code, serv_desc FROM mpi_service";
        return $this->db->query($sql);
    }
}