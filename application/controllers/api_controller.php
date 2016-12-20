<?php
class ApiController extends BaseController {
  var $oauth = null;
  function init(){
    parent::init();
    $this->oauth = new ApiOauthHelper();
  }

  function catch_exception($exception) {
    $type = get_class($exception);
    if($type == 'RecordNotFoundException')
      return $this->render_bad_request(array("error"=>404,
                                             "error_description" => $exception->getMessage()));
  }
}
