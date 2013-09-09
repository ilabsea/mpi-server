<?php 

function pagination_direction($field, $orderby, $direction) {
    if ($field == $orderby) :
        if ($direction == "DESC") :
        	return "<img src=\"".base_url("img/down.png")."\" />";
        else:
            return "<img src=\"".base_url("img/up.png")."\" />";
        endif;
    endif;
    return "";
}
?>

<script type="text/javascript">
function header_click(orderby, orderdirection) {
	var newdirection = 'ASC';
	if (orderby == '<?=$orderby?>') {
		if ('<?=$orderdirection?>' == 'ASC') {
			newdirection = "DESC";
		} else {
			newdirection = "ASC";
		}
	}
	window.location='<?=site_url("patients/patientdetail/".$patient["pat_id"]."?orderby=")?>' + orderby + "&orderdirection=" + newdirection;
}
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li><a href="<?=site_url("patients/patientlist")?>">Patients List</a> <span class="divider">&gt;</span></li>
	<li class="active">Patient Detail</li>
</ul>
<h3>Patient Detail</h3>
<div class="row-fluid">
   <div class="span3">Master ID: </div>
   <div class="span3"><?=htmlspecialchars($patient["pat_id"])?></div>
</div>
<div class="row-fluid">
   <div class="span3">Gender: </div>
   <div class="span3"><?=($patient["pat_gender"] == 2 ? "Female" : "Male")?></div>
</div>

<div class="row-fluid">
   <div class="span3">Registered at: </div>
   <div class="span3"><?=$patient["pat_register_site"]?></div>
</div>
<div class="row-fluid">
   <div class="span3">Registered on: </div>
   <div class="span3"><?=datetime_mysql_to_html($patient["date_create"])?></div>
</div>
<div class="row-fluid">
   <div class="span3">Age: </div>
   <div class="span3"><?=$patient["pat_age"]?></div>
</div>

<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th onclick="header_click('serv_code')" class="headerclickable">Service <?=pagination_direction("serv_code", $orderby, $orderdirection)?></th>
      <th onclick="header_click('site_code')" class="headerclickable">Site <?=pagination_direction("site_code", $orderby, $orderdirection)?></th>
      <th onclick="header_click('site_name')" class="headerclickable">Site Name <?=pagination_direction("site_name", $orderby, $orderdirection)?></th>
      <th onclick="header_click('visit_date')" class="headerclickable">Visit Date <?=pagination_direction("visit_date", $orderby, $orderdirection)?></th>
      <th onclick="header_click('ext_code')" class="headerclickable">External Code <?=pagination_direction("ext_code", $orderby, $orderdirection)?></th>
      <th onclick="header_click('ext_code_2')" class="headerclickable">External Code 2 <?=pagination_direction("ext_code_2", $orderby, $orderdirection)?></th>
      <th onclick="header_click('info')" class="headerclickable">Information <?=pagination_direction("info", $orderby, $orderdirection)?></th>
   </tr>
   <?php
      $row_nb = 0; 
      foreach($visit_list->result_array() as $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?> <?=(strtolower($row["info"])=="positive"?"style=\"background: red\"":"")?>>
      <td align="center"><?=htmlspecialchars($row["serv_code"])?></td>
      <td align="center"><?=htmlspecialchars($row["site_code"])?></td>
      <td align="center"><?=htmlspecialchars($row["site_name"])?></td>
      <td align="center"><?=htmlspecialchars(date_mysql_to_html($row["visit_date"]))?></td>
      <td align="center"><?=$row["ext_code"]?></td>
      <td align="center"><?=$row["ext_code_2"]?></td>
      <td><?=htmlspecialchars($row["info"])?></td>
   </tr>
   <?php endforeach;?>
</table>

<?php 
/*
    uuid_create(&$context);
    uuid_make($context, UUID_MAKE_V4);
    uuid_export($context, UUID_FMT_STR, &$uuid);
    echo "result=". trim($uuid);
*/
?>