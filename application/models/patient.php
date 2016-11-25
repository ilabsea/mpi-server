<?php
class Patient extends Imodel {
  var $pat_id = null;
  var $pat_gender = null;
  var $pat_dob = null;
  var $pat_age = null;
  var $date_create = null;
  var $pat_version = null;
  var $pat_register_site = null;
  var $new_pat_id = null;

  var $fingerprint_r1 = "";
  var $fingerprint_r2 = "";
  var $fingerprint_r3 = "";
  var $fingerprint_r4 = "";
  var $fingerprint_r5 = "";

  var $fingerprint_l1 = "";
  var $fingerprint_l2 = "";
  var $fingerprint_l3 = "";
  var $fingerprint_l4 = "";
  var $fingerprint_l5 = "";

  var $visits_count = 0;
  var $visit_positives_count = 0;
  var $created_at = null;
  var $updated_at = null;

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "pat_id";
  }

  static function table_name() {
    return "mpi_patient";
  }

  static function class_name(){
    return 'Patient';
  }

  static function migrate_counter_cache() {
    $limit=100
    $count = Patient::count(array("visits_count" => 0, "visit_positives_count" => 0));

    $repeat = ceil($count/$limit);
    for($i=0; $i< $repeat; $i++) {
      Patient::foreach_migrate($limit);
    }
  }

  static function foreach_migrate($limit=100) {
    $order_by=null
    $offset=null
    $patients = Patient::all(array("visits_count" => 0, "visit_positives_count" => 0),
                    $order_by,
                    $offset,
                    $limit);
    foreach($patients as $patient){
      Visit::update_count_cache($patient);
    }
  }

  function search ($gender="") {
    ILog::debug("search patient");
    $sql = "SELECT p.pat_id,
                   p.pat_gender,
                   p.pat_dob,
                   p.pat_age,
                   p.pat_register_site,
                   p.date_create,
                   p.fingerprint_r1,
                   p.fingerprint_r2,
                   p.fingerprint_r3,
                   p.fingerprint_r4,
                   p.fingerprint_r5,
                   p.fingerprint_l1,
                   p.fingerprint_l2,
                   p.fingerprint_l3,
                   p.fingerprint_l4,
                   p.fingerprint_l5
            FROM mpi_patient p";

    if ($gender != "")
      $sql.= " WHERE (p.pat_gender = ".$gender." OR p.pat_gender IS NULL)";

    $query = $this->db->query($sql);
    if (!$query)
      ILog::error(mysql_error());
    return $query;
  }

  function patient_list($criteria, $start, $rows) {
    $sql = "SELECT p.pat_id,
                   p.pat_gender,
                   p.pat_age,
                   p.pat_dob,
                   p.date_create,
                   p.pat_register_site,
                   p.visits_count, //nb_visit
                   p.visit_postives_count, // nb_visit_positive,
                   p.new_pat_id
              FROM mpi_patient p";

    $where = $this->generate_where($criteria);

    if ($where != "")
      $sql .= " WHERE ".$where;
    if($criteria["orderby"])
      $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"];

    $sql .= " LIMIT ".$start.", ".$rows;

    return $this->db->query($sql);
  }

  private function generate_where($criteria) {
    $where = "";
    $conditions = array();

    if ($criteria["cri_pat_gender"])
      $conditions[] =  "p.pat_gender =".$criteria["cri_pat_gender"];

    if ($criteria["cri_master_id"])
      $conditions[] = "p.pat_id LIKE '%".mysql_real_escape_string($criteria["cri_master_id"])."%'";

    $sub_where = [];


    if ($criteria["date_from"])
      $sub_where[] = "p.date_create >= '".date_html_to_mysql($criteria["date_from"])." 00:00:00'";

    if ($criteria["date_to"])
      $sub_where[] = "p.date_create <= '".date_html_to_mysql($criteria["date_to"])." 23:59:59'";

    if ($criteria["cri_site_code"])
      $sub_where[] = "p.pat_register_site ='".mysql_real_escape_string($criteria["cri_site_code"])."'";

    $sub_sql = [];
    $sub_sql2 = [];
    $sub_sql0 = "";


    $sub_sql2[] = "v.pat_id = p.pat_id";

    if ($criteria["date_from"])
      $sub_sql2[] = " v.visit_date >= '".date_html_to_mysql($criteria["date_from"])."'";

    if ($criteria["date_to"])
      $sub_sql2[] = " v.visit_date <= '".date_html_to_mysql($criteria["date_to"])."'";

    if ($criteria["cri_site_code"] )
      $sub_sql2[] = " v.site_code = '".mysql_real_escape_string($criteria["cri_site_code"])."'";


    if ($criteria["cri_serv_id"] || $criteria["cri_external_code"] || $criteria["cri_external_code2"] ){
      $sub_sql[] = " EXISTS (SELECT v.visit_id FROM mpi_visit v WHERE";

      $sub_sql[] = " v.pat_id = p.pat_id";
      if ($criteria["cri_serv_id"])
        $sub_sql[] = " v.serv_id = ".$criteria["cri_serv_id"];


      if ($criteria["cri_external_code"])
        $sub_sql[] = " v.ext_code = '".mysql_real_escape_string($criteria["cri_external_code"])."'";

      if ($criteria["cri_external_code2"])
        $sub_sql [] = " v.ext_code_2 = '".mysql_real_escape_string($criteria["cri_external_code2"])."'";
    }

    if (count($sub_sql2) ){
      if (count($sub_sql))
        $sub_sql0 = implode(" AND ", $sub_sql) . " AND " . implode(" AND ", $sub_sql2) ;
      else
        $sub_sql0 = " EXISTS (SELECT v.visit_id FROM mpi_visit v WHERE". implode(" AND", $sub_sql2) . ")";
    }

    if ($sub_where == ""){
      if ($sub_sql != "")
        $where .=" AND ".$sub_sql;
      else{
        if ($sub_sql == "")
          $where .=" AND (".$sub_where." OR ".$sub_sql0.")";
        else
          $where .= " AND ((".$sub_where." AND ".$sub_sql.") OR ".$sub_sql0.")";
      }

      if ($where != ""){
        $where = trim($where, " AND");
        $where = trim($where, " ");
      }
    }
    return $where;
  }

  function count_patient_list($criteria) {
    $sql = "SELECT count(pat_id) as nb_patient FROM mpi_patient p";
    $where = $this->generate_where($criteria);
    if ($where != "")
      $sql .= " WHERE ".$where;

    $query = $this->db->query($sql);
    if ($query->num_rows() <= 0)
      return 0;
    $row = $query->row_array();
    return $row["nb_patient"];
  }

  function newPatientFingerprint($data) {
    $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
    $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
    $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
    $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
    $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];

    $pat_id = $this->getPatientIdBySiteCode($sitecode);
    $field = "pat_id,
              pat_gender,
              pat_age,
              pat_dob,
              pat_register_site,
              date_create";
    $values = "'".$pat_id."',
               ".$gender.",
               ".$age.",
               ".$dob.",
               ".$site.",
               ".$create_date;

    foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint){
      ${$fingerprint} = isset($data[$fingerprint]) &&  $data[$fingerprint] != "" ? "'".mysql_real_escape_string($data[$fingerprint])."'" : "NULL";
      $field .= ",".$fingerprint ;
      $values .= ",".${$fingerprint};
    }

    $sql = "INSERT INTO mpi_patient(".$field.") VALUES(".$values.")";

    $res = $this->db->query($sql);
    if (!$res)
      ILog::error(mysql_error());

    return $pat_id;
  }

  function newPatient($data) {
    $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
    $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
    $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
    $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
    $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];

    $pat_id = $this->getPatientIdBySiteCode($sitecode);
    $this->db->trans_start();

    $sql = "INSERT INTO mpi_patient(pat_id,
                                   pat_gender,
                                   pat_age,
                                   pat_dob,
                                   pat_register_site,
                                   date_create)
                            VALUES('".$pat_id."',
                                   ".$gender.",
                                   ".$age.",
                                   ".$dob.",
                                   ".$site.",
                                   ".$create_date.")";

    $this->db->query($sql);
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method newPatient without fingerprint of patient model. ".$this->db->_error_message());

    return $pat_id;
  }

  function getPatientById($pat_id) {
    $sql = "SELECT pat_id,
                   pat_gender,
                   pat_age,
                   date_create,
                   pat_register_site,
                   pat_dob
              FROM mpi_patient
             WHERE pat_id = '".mysql_real_escape_string($pat_id)."'";
    $query = $this->db->query($sql);
    if ($query->num_rows() <= 0)
      return null;

    return $query->row_array();
  }

  function getVisits($var, $orderby="visit_date", $orderdirection="DESC") {
    if (count($var) <= 0 )
       return null;

    $patient_ids = implode("','", $var);
    $patient_ids = "'".$patient_ids."'";
    $sql = "SELECT ps.visit_id,
                  ps.pat_id,
                  ps.serv_id,
                  s.serv_code,
                  ps.site_code,
                  site.site_name,
                  ps.ext_code,
                  ps.ext_code_2,
                  ps.visit_date,
                  ps.pat_age,
                  ps.info
              FROM mpi_visit ps
              LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
              LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
              WHERE pat_id IN (".$patient_ids.") ORDER BY ".$orderby." ".$orderdirection;
    return $this->db->query($sql);
  }

  function getVisitsByPID($pid) {
    $sql = "SELECT ps.visit_id,
                  ps.pat_id,
                  ps.serv_id,
                  s.serv_code,
                  ps.site_code,
                  site.site_name,
                  ps.ext_code,
                  ps.ext_code_2,
                  ps.visit_date,
                  ps.info,
                  ps.date_create,
                  ps.pat_age,
                  ps.refer_to_vcct,
                  ps.refer_to_oiart,
                  ps.refer_to_std
              FROM mpi_visit ps
              LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
              LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
              WHERE pat_id = '".mysql_real_escape_string($pid)."'
              ORDER BY visit_date DESC";
    return $this->db->query($sql);
  }

  function newVisit($var) {
    $visit_id = null;
    $create_date = isset($var["date_create"]) && $var["date_create"] != "" ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $age = isset($var["age"]) && $var["age"] != "" ? $var["age"] : "NULL";

    /** Control if visit already exist */
    $sql = "SELECT visit_id FROM mpi_visit WHERE  pat_id = '".mysql_real_escape_string($var["pat_id"])."' AND
                                                  serv_id = ".$var["serv_id"]." AND
                                                  site_code = '".mysql_real_escape_string($var["site_code"])."' AND
                                                  ext_code = '".mysql_real_escape_string($var["ext_code"])."' AND
                                                  visit_date = '".$var["visit_date"]."' AND
                                                  date_create = ".$create_date;
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0){
        $row = $query->row_array();
        $visit_id = $row["visit_id"];
        return $visit_id;
    }

    $sql = "INSERT INTO mpi_visit(pat_id,
                                        serv_id,
                                        site_code,
                                        ext_code,
                                        ext_code_2,
                                        info,
                                        visit_date,
                                        pat_age,
                                        refer_to_vcct,
                                        refer_to_oiart,
                                        refer_to_std,
                                        date_create)
                                  VALUES('".mysql_real_escape_string($var["pat_id"])."',
                                         ".$var["serv_id"].",
                                         '".mysql_real_escape_string($var["site_code"])."',
                                         '".mysql_real_escape_string($var["ext_code"])."',
                                         '".mysql_real_escape_string($var["ext_code_2"])."',
                                         '".mysql_real_escape_string($var["info"])."',
                                         '".$var["visit_date"]."',
                                         ".$age.",
                                         ".$var["refer_to_vcct"].",
                                         ".$var["refer_to_oiart"].",
                                         ".$var["refer_to_std"].",
                                         ".$create_date."
                                       )";

    $this->db->query($sql);
    $visit_id = $this->db->insert_id();

    if ($age != "NULL"){
      $sql = "UPDATE mpi_patient SET pat_age = ".$age." WHERE pat_id = '".mysql_real_escape_string($var["pat_id"])."'";
      $this->db->query($sql);
    }

    return $visit_id;
  }

  private function getPatientSeqProId($province) {
    $seq = -1;
    $this->db->trans_start();

    $sql = "SELECT pro_pat_seq 	FROM mpi_province WHERE pro_id = ".$province;
    $query = $this->db->query($sql);

    if ($query->num_rows() > 1){
      $row = $query->row_array();
      $seq = $row["pro_pat_seq"];
    }

    if ($seq < 0)
      return -1;

    $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_id = ".$province;
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());

    return $seq;
  }

  private function getPatientSeqProCode($province) {
    $seq = -1;
    $this->db->trans_start();

    $sql = "SELECT pro_pat_seq 	FROM mpi_province WHERE pro_code = '".mysql_real_escape_string($province)."'";
    $query = $this->db->query($sql);

    if ($query->num_rows() > 0){
        $row = $query->row_array();
        $seq = $row["pro_pat_seq"];
    }

    if ($seq < 0)
      return -1;


    $seq++;
    $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_code = '".mysql_real_escape_string($province)."'";
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());
    return $seq;
  }

  private function getPatientIdBySiteCode ($sitecode, $country="KH") {
    $version = 1;
    $sitemodel = $this->load_other_model("site");
    $site = $sitemodel->getSiteByCode($sitecode);
    $seq = $this->getPatientSeqProCode($site["pro_code"]);
    $pid = $country.str_pad($site["pro_code"], 3, "0", STR_PAD_LEFT).$version.str_pad($seq, 8, "0", STR_PAD_LEFT);
    return $pid;
  }

  function updateReplacePatientId($old_masterid, $new_patientid) {
    $this->db->trans_start();
    $sql = "UPDATE mpi_visit SET pat_id = '".mysql_real_escape_string($new_patientid)."'
            WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $sql = "UPDATE mpi_vcct_from_oiart SET oiart_pat_id = '".mysql_real_escape_string($new_patientid)."'
           WHERE oiart_pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $sql = "UPDATE mpi_patient SET new_pat_id = '".mysql_real_escape_string($new_patientid)."'
            WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method updateReplacePatientId of patient model. ".$this->db->_error_message());
  }

  function manageVcctNoFpFromOiart($data) {
    $sql = " SELECT vcct_no_fp_id,
                    vcct_external_code,
                    vcct_site,
                    oiart_pat_id
              FROM mpi_vcct_from_oiart
              WHERE vcct_external_code = '".mysql_real_escape_string($data["ext_code"])."' AND
                    vcct_site = '".mysql_real_escape_string($data["site_code"])."'";
    $query = $this->db->query($sql);

    $record = null;
    if ($query->num_rows() > 0)
            $record = $query->row_array();
    if ($record == null){
      $sql = "INSERT INTO mpi_vcct_from_oiart( vcct_external_code, vcct_site, oiart_pat_id)
                    VALUES('".mysql_real_escape_string($data["ext_code"])."',
                    '".mysql_real_escape_string($data["site_code"])."',
                    '".mysql_real_escape_string($data["pat_id"])."')";
      $this->db->query($sql);

    }
  }
}
