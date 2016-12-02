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

  static function fingerprint_fields(){
    return array(
      "fingerprint_r1",
      "fingerprint_r2",
      "fingerprint_r3",
      "fingerprint_r4",
      "fingerprint_r5",
      "fingerprint_l1",
      "fingerprint_l2",
      "fingerprint_l3",
      "fingerprint_l4",
      "fingerprint_l5"
    );
  }

  function gender(){
    $gender = $this->pat_gender == 2 ? "Female" : "Male";
    return $gender;
  }

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
    $limit=100;
    $count = Patient::count(array("visits_count" => 0, "visit_positives_count" => 0));

    $repeat = ceil($count/$limit);
    for($i=0; $i< $repeat; $i++) {
      Patient::foreach_migrate($limit);
    }
  }

  static function foreach_migrate($limit=100) {
    $order_by=null;
    $offset=null;
    $patients = Patient::all(array("visits_count" => 0, "visit_positives_count" => 0),
                    $order_by,
                    $offset,
                    $limit);
    foreach($patients as $patient){
      Visit::update_count_cache($patient);
    }
  }

  function search ($gender="") {
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

  static function count_filter($criterias){
    $active_record = new Patient();
    $active_record->db->select("count(*)");
    $active_record = Patient::where_filter($active_record, $criterias);
    $count = $active_record->db->count_all_results();
    return $count;
  }

  static function all_filter($criterias){
    $active_record = new Patient();
    $active_record->db->select("p.pat_id, p.pat_gender, p.pat_age, p.pat_dob, p.date_create, p.pat_register_site, p.visits_count, p.visit_positives_count,p.new_pat_id");
    $active_record = Patient::where_filter($active_record, $criterias);

    if($criterias["order_direction"])
      $active_record->db->order_by($criterias["order_by"], $criterias["order_direction"]);
    $active_record->db->limit(Paginator::per_page());
    $active_record->db->offset(Paginator::offset());
    $query = $active_record->db->get();
    return $query->result();
  }

  static function where_filter($active_record, $criterias){
    $active_record->db->from('mpi_patient p');

    $patient_conditions = array();
    $visit_conditions = array();

    if($criterias['pat_gender'])
      $patient_conditions['p.pat_gender'] = $criterias['pat_gender'];

    if($criterias["master_id"])
      $patient_conditions['p.pat_id LIKE'] = "%". $criterias["master_id"] . "%";

    if($criterias["date_from"]){
      $patient_conditions["p.date_create >="] = Imodel::beginning_of_day($criterias["date_from"]);
      $visit_conditions["v.visit_date >="] = $criterias["date_from"];
    }

    if($criterias["date_to"]){
      $patient_conditions["p.date_create <="] = Imodel::end_of_day($criterias["date_to"]);
      $visit_conditions["v.visit_date <="] = $criterias["date_to"];
    }

    if($criterias["site_code"]){
      $patient_conditions['p.pat_register_site'] = $criterias["site_code"];
      $visit_conditions['v.site_code = '] = $criterias["site_code"];
    }

    if($criterias["serv_id"])
      $visit_conditions["v.serv_id = "] = $criterias["serv_id"];

    if($criterias["external_code"])
      $visit_conditions["v.ext_code = "] = $criterias["external_code"];

    if($criterias["external_code2"])
      $visit_conditions["v.ext_code_2 = "] = $criterias["external_code2"];

    foreach($patient_conditions as $key => $value)
      $active_record->db->where($key, $value);

    if(count($visit_conditions) >0 ){
      $query_string = array();
      foreach($visit_conditions as $key => $value)
        $query_string[] = $key . "" . mysql_real_escape_string($value);

      $where = implode(" AND ", $query_string);
      $visit_query = "SELECT v.pat_id FROM mpi_visit v WHERE {$where} GROUP BY v.pat_id";
      $active_record->db->where("p.pat_id IN ($visit_query) ");
    }

    return $active_record;
  }

  static function paginate_filter($criterias) {
    $total_counts = Patient::count_filter($criterias);
    $records = Patient::all_filter($criterias);
    $paginator = new Paginator($total_counts, $records);
    return $paginator;
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

  static function visits($patient_ids, $order_by, $order_direction) {
    $active_record = new Patient();
    $ids = implode(",", $patient_ids);
    $order_by = $order_by == "" ? "visit_date" : $order_by;
    $order_direction = $order_direction == "" ? "DESC" : $order_direction;

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
              WHERE pat_id IN ({$ids}) ORDER BY {$order_by} {$order_direction} ";

    $query = $active_record->db->query($sql);
    return $query->result();
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

    $sql = "SELECT pro_pat_seq FROM mpi_province WHERE pro_code = '".mysql_real_escape_string($province)."'";
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
