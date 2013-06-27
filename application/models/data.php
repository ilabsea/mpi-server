<?php
class Data extends Imodel {
	function get_data_from_table($table_name) {
		$sql = "SELECT * FROM ".$table_name;
		$query = $this->db->query($sql);
		return $query;
	}

}