<script type="text/javascript">
$(document).ready(function() {

	$('#datepicker').datepicker({ dateFormat: 'dd.mm.yy' }); 
	$( "#from" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		dateFormat: 'dd/mm/yy',
		onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#to" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		dateFormat: 'dd/mm/yy',
		onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li><a href="<?=site_url("reports/reportmenu")?>">Report Menu</a> <span class="divider">&gt;</span></li>
	<li class="active">routine</li>
</ul>
<h3>Routine Report</h3>
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
<form method="post" action="<?=site_url("reports/submitroutine")?>">
<table>
	<tr>
       <td width="15%">Province</td>
       <td>
           <select name="cri_pro_code">
               <option value="">Select a province</option>
               <?php foreach($provinces->result_array() as $row): 
               $selected = $cri_pro_code == $row["pro_code"] ? "selected" : "";
               ?>
               <option value="<?=$row["pro_code"]?>" <?=$selected?>><?=htmlspecialchars($row["pro_name"])?></option>
               <?php endforeach;?>
           </select>
       </td>
   </tr>
	<tr>
		<td>Start Date</td>
		<td>
		 <input type="text" id="from" name="date_from" value="<?=htmlspecialchars($date_from)?>"/> &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;
		End Date <input type="text" id="to" name="date_to" value="<?=htmlspecialchars($date_to)?>"/></td>
	</tr>
</table>
<br/>
<input type="submit" value="Submit"/>
</form> 

<?php if ($reports != null) : ?>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>Site code</th>
      <th>Site Name</th>
      <th>Last sync date</th>
      <th>Nb. of days from now</th>
   </tr>
   <?php
      $row_nb = 0; 
      foreach($reports as $sitecode => $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><?=htmlspecialchars($row["site_code"])?></td>
      <td><?=htmlspecialchars($row["site_name"])?></td>
      <td align="center"><?=(!isset($row["last_sync_date"]) ? "&nbsp;" : datetime_mysql_to_html($row["last_sync_date"]))?></td>
      <td align="center"><?=(!isset($row["period_from_now"]) ? "&nbsp;" : $row["period_from_now"])?></td>
      
   </tr>
   <?php endforeach;?>
</table>
<br/>
<input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportroutine")?>'"/>
<?php endif; ?>