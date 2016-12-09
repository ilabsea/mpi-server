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


  static function paginate_filter($criterias) {
    $total_counts = Site::count_filter($criterias);
    $records = Site::all_filter($criterias);
    $paginator = new Paginator($total_counts, $records);
    return $paginator;
  }

  static function all_filter($criterias){
    $active_record = new Site();
    $active_record->db->select("site.site_id,site.site_code,site.site_name,site.pro_code,site.serv_id,site.od_name,
                                province.pro_name, service.serv_code");

    $active_record = Site::where_filter($active_record, $criterias);

    if($criterias["order_direction"])
      $active_record->db->order_by($criterias["order_by"], $criterias["order_direction"]);
    $active_record->db->limit(Paginator::per_page());
    $active_record->db->offset(Paginator::offset());
    $query = $active_record->db->get();
    return $query->result();
  }

  static function count_filter($criterias){
    $active_record = new Site();
    $active_record = Site::where_filter($active_record, $criterias);
    $count = $active_record->db->count_all_results();
    return $count;
  }

  static function where_filter($active_record, $criterias){
    $active_record->db->from('mpi_site site');
    $active_record->db->join("mpi_province province", "site.pro_code = province.pro_code", "left");
    $active_record->db->join("mpi_service service", "service.serv_id = site.serv_id", "left");

    $conditions = array();

    if ($criterias["serv_id"] != "")
      $conditions["service.serv_id"] = $criterias["serv_id"];

    if ($criterias["site_code"] != "")
      $conditions["site.site_code LIKE "] =  "%".$criterias["site_code"]."%";

    if ($criterias["pro_code"] != "")
        $conditions["province.pro_code"] = $criterias["pro_code"];

    foreach($conditions as $key => $value)
      $active_record->db->where($key, $value);

    return $active_record;
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
