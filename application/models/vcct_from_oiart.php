<?php

class VcctFromOIART extends Imodel {
  var $vcct_no_fp_id = null;
  var $vcct_external_code = null;
  var $vcct_site = null;
  var $oiart_pat_id = null;

  static function primary_key() {
    return "vcct_no_fp_id";
  }

  static function table_name() {
    return "mpi_vcct_from_oiart";
  }

  static function class_name(){
    return 'VcctFromOIART';
  }
}
