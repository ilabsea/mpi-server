<?php
class Province extends Imodel {
  var $id = null;
  var $pro_id = null;
  var $pro_code = null;
  var $pro_name = null;
  var $pro_pat_seq = null;

  function increment_sequence(){
    $next_sequence = $this->pro_pat_seq + 1 ;
    return $this->update_attributes(array("pro_pat_seq" => $next_sequence));
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "mpi_province";
  }

  static function class_name(){
    return 'Province';
  }
}
