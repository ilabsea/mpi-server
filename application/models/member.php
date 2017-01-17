<?php

class Member extends Imodel {
  var $member_id = null;
  var $member_code = null;
  var $member_login = null;
  var $member_pwd = null;
  var $site_code = null;
  var $date_create = null;
  var $member_fp_r1 = null;
  var $member_fp_r2 = null;
  var $member_fp_r3 = null;
  var $member_fp_r4 = null;
  var $member_fp_r5 = null;
  var $member_fp_l1 = null;
  var $member_fp_l2 = null;
  var $member_fp_l3 = null;
  var $member_fp_l4 = null;
  var $member_fp_l5 = null;

  static function primary_key() {
    return "member_id";
  }

  static function table_name() {
    return "mpi_member";
  }

  static function class_name(){
    return 'Member';
  }

  function before_create(){
    $this->member_pwd = Member::hex_digest($this->member_pwd);
    if(!$this->date_create)
      $this->set_attribute('date_create',  $this->current_time());

    if(!$this->member_code){
      $secure_random = openssl_random_pseudo_bytes (30);
      $this->set_attribute('member_code', $secure_random);
    }
  }

  function before_save(){
    $this->uniqueness_by_site_code_and_login();
    $this->site_exist();
  }

  function site_exist(){
    $site = Site::find_by(array("site_code" => $this->site_code));
    if(!$site)
      $this->_errors["site_code"] = "Site with code ". $this->site_code ." does not exist";
  }

  function uniqueness_by_site_code_and_login(){
    $conditions = array("site_code" => $this->site_code, "member_login" => $this->member_login);

    if(!$this->new_record()){
      $key = "member_id != ". $this->member_id;
      $conditions[$key] = null;
    }
    $member = Member::find_by($conditions);

    if($member) {
      $this->form_validation->set_message('site_code', "Already exist");
      $this->form_validation->set_message('member_login', "Already exist");
      $this->_errors = array("site_code" => "Site Code and Member Login already exists");
    }
  }

  function validation_rules(){
    $this->form_validation->set_rules('site_code', 'Site Code', "trim|required");
    $this->form_validation->set_rules('member_login', 'Login', "trim|required");
    $this->form_validation->set_rules('member_pwd', 'Password', "trim|required");

    return true;
  }

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
    if ($query->num_rows <= 0)
        return null;
    return $query->row_array();
  }

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
    if ($query->num_rows <= 0)
      return null;
    return $query->row_array();
  }

  function createNew($var) {
    $sql1 = "member_login, member_pwd, site_code, date_create, ";
    $sql2 = "'".mysql_real_escape_string($var["member_login"])."', SHA1('".mysql_real_escape_string($var["member_pwd"])."'),'"
               .mysql_real_escape_string($var["site_code"])."', CURRENT_TIMESTAMP(),";
    foreach (Iconstant::$MPI_USER_FP as $fingerprint){
      ${$fingerprint} = isset($var[$fingerprint]) &&  $var[$fingerprint] != "" ? "'".mysql_real_escape_string($var[$fingerprint])."'" : "NULL";
      $sql1 .= $fingerprint."," ;
      $sql2 .= ${$fingerprint}.",";
    }

    $sql1 = trim($sql1, ",");
    $sql2 = trim($sql2, ",");

    $sql = "INSERT INTO mpi_member(".$sql1.") VALUES (".$sql2.")";
    $this->db->query($sql);
  }

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
    if ($where != "")
      $sql .= " AND ".$where;

    $sql .= " ORDER BY ".$criteria["orderby"]." ".$criteria["orderdirection"]."
              LIMIT ".$start.", ".$rows;
    return $this->db->query($sql);
  }

  private function generate_where($criteria) {
    $where = "";

    if ($criteria["cri_serv_id"] != "")
      $where .= " AND s.serv_id = ".$criteria["cri_serv_id"];

    if ($criteria["cri_site_code"] != "")
      $where .= " AND s.site_code LIKE '%".mysql_real_escape_string($criteria["cri_site_code"])."%'";

    if ($criteria["cri_member_login"] != "")
      $where .= " AND m.member_login LIKE '%".mysql_real_escape_string($criteria["cri_member_login"])."%'";

    if ($where != ""){
      $where = trim($where, " AND");
      $where = trim($where, " ");
    }
    return $where;
  }

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

  static function paginate_filter($criterias) {
    $total_counts = Member::count_filter($criterias);
    $records = Member::all_filter($criterias);
    $paginator = new Paginator($total_counts, $records);
    return $paginator;
  }

  static function all_filter($criterias){
    $active_record = new Member();
    $active_record->db->select("member.member_id, member.member_code, member.member_login, member.date_create, site.site_id, site.site_code, site.site_name,
                               site.pro_code, site.serv_id, province.pro_name, service.serv_code");

    $active_record = Member::where_filter($active_record, $criterias);

    if($criterias["order_direction"])
      $active_record->db->order_by($criterias["order_by"], $criterias["order_direction"]);
    $active_record->db->limit(Paginator::per_page());
    $active_record->db->offset(Paginator::offset());
    $query = $active_record->db->get();
    return $query->result();
  }

  static function count_filter($criterias){
    $active_record = new Member();
    $active_record = Member::where_filter($active_record, $criterias);
    $count = $active_record->db->count_all_results();
    return $count;
  }

  static function where_filter($active_record, $criterias){
    $active_record->db->from('mpi_member member');
    $active_record->db->join("mpi_site site", "site.site_code = member.site_code", "left");
    $active_record->db->join("mpi_province province", "province.pro_code = site.pro_code", "left");
    $active_record->db->join("mpi_service service", "service.serv_id = site.serv_id", "left");

    $conditions = array();

    if ($criterias["serv_id"] != "")
      $conditions["service.serv_id"] = $criterias["serv_id"];

    if ($criterias["site_code"] != "")
      $conditions["site.site_code LIKE "] =  "%".$criterias["site_code"]."%";

    if ($criterias["member_login"] != "")
      $conditions["member.member_login LIKE "] = "%".$criterias["member_login"]."%";

    foreach($conditions as $key => $value)
      $active_record->db->where($key, $value);

    return $active_record;
  }
}
