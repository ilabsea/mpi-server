<?php

class Site extends Imodel {
  var $site_id = null;
  var $site_code = null;
  var $site_name = null;
  var $pro_code = null;
  var $serv_id = null;
  var $od_name = null;

  static function primary_key() {
    return "site_id";
  }

  static function table_name() {
    return "mpi_site";
  }

  static function class_name(){
    return 'Site';
  }

  function getSiteByCode ($site_code) {
    $sql = "SELECT site_id,
                  site_code,
                  site_name,
                  pro_code,
                  od_name,
                  serv_id
            FROM mpi_site
            WHERE site_code = '".mysql_real_escape_string($site_code)."'";
    $query = $this->db->query($sql);
    if ($query->num_rows() <= 0)
      return null;
    return $query->row_array();
  }

  function getSites($criteria, $start, $rows) {
    $sql = "SELECT s.site_id,
                  s.site_code,
                  s.site_name,
                  s.pro_code,
                  s.serv_id,
                  pr.pro_name,
                  ser.serv_code,
                  s.od_name
            FROM mpi_site s
            LEFT JOIN mpi_province pr ON (pr.pro_code = s.pro_code)
            LEFT JOIN mpi_service ser ON (ser.serv_id = s.serv_id)";

    $where = $this->generate_where($criteria);
    if ($where != "")
      $sql .= " WHERE ".$where;

    $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"]."
              LIMIT ".$start.", ".$rows;
    return $this->db->query($sql);
  }

  private function generate_where($criteria) {
    $where = "";

    if($criteria["cri_serv_id"] != "")
      $where .= " AND s.serv_id = ".$criteria["cri_serv_id"];

    if ($criteria["cri_pro_code"] != "")
      $where .= " AND s.pro_code = '".mysql_real_escape_string($criteria["cri_pro_code"])."'";

    if ($criteria["cri_site_code"] != "")
      $where .= " AND s.site_code LIKE '%".mysql_real_escape_string($criteria["cri_site_code"])."%'";

    if ($where != ""){
      $where = trim($where, " AND");
      $where = trim($where, " ");
    }

    return $where;
  }

  function getProvinces() {
    $sql = "SELECT pro_code,
                  pro_name,
                  pro_pat_seq
             FROM mpi_province
            ORDER BY pro_name ASC";
    return $this->db->query($sql);
  }

  function count_site_list($criteria) {
    $sql = "SELECT COUNT(site_id) as nb_site FROM mpi_site s";
    $where = $this->generate_where($criteria);

    if ($where != "")
      $sql .= " WHERE ".$where;

    $query = $this->db->query($sql);

    if ($query->num_rows() <= 0)
      return 0;

    else{
      $row = $query->row_array();
      return $row["nb_site"];
    }
  }
}
