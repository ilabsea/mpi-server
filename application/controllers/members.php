<?php

class Members extends MpiController {
  function index() {
    $params = $this->filter_params(array("serv_id", "site_code", "member_login"  ,"order_by", "order_direction"));

    $paginate_members = Member::paginate_filter($params);
    $this->set_view_variables(array("params" => $params, "paginate_members" => $paginate_members));
    $this->render_view();
  }

  function delete($member_id) {
    $member = Member::find($member_id);
    if($member && $member->delete())
      Isession::setFlash("success", "Member has been removed");
    else
      Isession::setFlash("error", "Failed to remove the member");
    redirect(site_url("members/index"));
  }
}
