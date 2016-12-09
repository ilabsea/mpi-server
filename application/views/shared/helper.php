<?php
function pagination_direction($field, $params) {
  if ($field == $params["order_by"])
    return $params["order_direction"] == "DESC" ? "<i class='icon icon-chevron-down'>" : "<i class='icon icon-chevron-up'>";
  return "";
}
?>
