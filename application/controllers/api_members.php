<?php
//name should be ApiVisits but can not find a way to map this to CI router
class Api_members extends ApiAccessController{

  //POST api/members/create
  function create(){

    $params = $this->member_params();
    $member = new Member($params);
    if($member->save())
      return $this->render_json($member);
    else
      return $this->render_bad_request($member->get_errors());
  }

  function member_params() {
    $params = $this->filter_params(array(
      "site_code", "member_pwd", "member_login", "member_code", "member_fp_r1", "member_fp_r2",
      "member_fp_r3", "member_fp_r4", "member_fp_r5", "member_fp_l1", "member_fp_l2",
      "member_fp_l3", "member_fp_l4", "member_fp_l5"), 'post');
  }
}
