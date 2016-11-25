<?php
function pagination_direction($field, $params) {
  if ($field == $params["order_by"]){
    $image_url = $params["order_direction"] == "DESC" ? base_url("img/down.png") : base_url("img/up.png");
    return "<img src='{$image_url}' />";
  }
  return "";
}
?>
