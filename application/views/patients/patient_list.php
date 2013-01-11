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
$(document).ready(function() {
	$(".header_clickable").click(function() {
	});
});

function header_click(orderby, orderdirection) {
	var newdirection = 'ASC';
	if (orderby == '<?=$orderby?>') {
		if ('<?=$orderdirection?>' == 'ASC') {
			newdirection = "DESC";
		} else {
			newdirection = "ASC";
		}
	}
	window.location='<?=site_url("patients/patientlist?orderby=")?>' + orderby + "&orderdirection=" + newdirection;
}
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Patients List</li>
</ul>

<?php if ((isset($error) && $error != "") || (isset($error_list) && $error_list != "") || (isset($success) && $success != "")) : ?>
<div class="row-fluid">
   <div class="span12">
      <?php if (!is_null($error)) : ?>
      <span class="label label-important"><?=htmlspecialchars($error)?></span>
      <?php elseif(!is_null($error_list)) :?>
      <div class="label label-important"><h4>Error:</h4><?=$error_list?></div><br/>
      <?php elseif(!is_null($success)) :?>
      <span class="label label-success"><?=$success?></span>
      <?php endif;?>
   </div>
</div>
<?php endif; ?>

<form method="post" action="<?=site_url("patients/search")?>">
<table>
   <tr>
       <td width="40%">Service</td>
       <td>
           <select name="cri_serv_id">
               <option value="">All services</option>
               <?php foreach($services->result_array() as $row): 
               $selected = $cri_serv_id == $row["serv_id"] ? "selected" : "";
               ?>
               <option value="<?=$row["serv_id"]?>" <?=$selected?>><?=htmlspecialchars($row["serv_code"])?></option>
               <?php endforeach;?>
           </select>
       </td>
   </tr>
   <tr>
       <td>Sex</td>
       <td valign="middle">
           <input type="radio" name="cri_pat_gender" value="" style="vertical-align: middle; margin: 0px;" <?=($cri_pat_gender==""?"checked":"")?>> All &nbsp; &nbsp; &nbsp; &nbsp;
           <input type="radio" name="cri_pat_gender" value="1" style="vertical-align: middle; margin: 0px;" <?=($cri_pat_gender==1?"checked":"")?>> Male &nbsp; &nbsp; &nbsp; &nbsp;
           <input type="radio" name="cri_pat_gender" value="2" style="vertical-align: middle; margin: 0px;" <?=($cri_pat_gender==2?"checked":"")?>> Female
       </td>
   </tr>
   <tr>
       <td>Site code</td>
       <td><input type="text" name="cri_site_code" value="<?=htmlspecialchars($cri_site_code)?>"></td>
   </tr>
   <tr>
       <td>External Code</td>
       <td><input type="text" name="cri_external_code" value="<?=htmlspecialchars($cri_external_code)?>"></td>
   </tr>
   <tr>
       <td>External Code2</td>
       <td><input type="text" name="cri_external_code2" value="<?=htmlspecialchars($cri_external_code2)?>"></td>
   </tr>
</table>
<input type="submit" value="Search" />
</form>
 
<?php if ($nb_of_page > 1) :?>
<?php 
    $previous_page = $cur_page <= 1 ? 1 : $cur_page - 1;
    $next_page = $cur_page >= $nb_of_page ? $nb_of_page : $cur_page + 1;
?>
 
<div class="pagination">  
  <ul>  
    <li><a href="<?=site_url("patients/patientlist?cur_page=".$previous_page)?>">Prev</a></li>
    <?php for ($i=1; $i<=$nb_of_page; $i++) : ?>	
	    <?php if ($i == $cur_page) :  ?>
	        <li class="active"><a href="#"><?=$i?></a></li>
	    <?php else: ?>
	    	<li><a href="<?=site_url("patients/patientlist?cur_page=".$i)?>"><?=$i?></a></li>
	    <?php endif;?>
    <?php endfor; ?>  
    <li><a href="<?=site_url("patients/patientlist?cur_page=".$next_page)?>">Next</a></li>
  </ul>  
</div> 
<?php endif; ?>
<?php if ($patient_list != null && $patient_list->num_rows() > 0) : ?>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th onclick="header_click('pat_id')" class="headerclickable">Master ID <?=pagination_direction("pat_id", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_gender')" class="headerclickable">Gender <?=pagination_direction("pat_gender", $orderby, $orderdirection)?></th>
      <th onclick="header_click('date_create')" class="headerclickable">Register Date <?=pagination_direction("date_create", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_age')" class="headerclickable">Age <?=pagination_direction("pat_age", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_dob')" class="headerclickable">Birth date <?=pagination_direction("pat_dob", $orderby, $orderdirection)?></th>
      <th onclick="header_click('nb_visit')" class="headerclickable">Number Visits <?=pagination_direction("nb_visit", $orderby, $orderdirection)?></th>
   </tr>	
   <?php
      $row_nb = 0; 
      foreach($patient_list->result_array() as $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><a href="<?=site_url("patients/patientdetail/".$row["pat_id"])?>"><?=htmlspecialchars($row["pat_id"])?></a></td>
      <td align="center"><?=($row["pat_gender"] == 2 ? "Female" : "Male")?></td>
      <td align="center"><?=date_mysql_to_html($row["date_create"])?></td>
      <td align="center"><?=$row["pat_age"]?></td>
      <td align="center"><?=htmlspecialchars(date_mysql_to_html($row["pat_dob"]))?></td>
      <td align="center"><?=$row["nb_visit"]?></td>
   </tr>
   <?php endforeach;?>
</table>
<div><?=$patient_list->num_rows()?> / <?=$total_record?></div>
<?php endif;?>
