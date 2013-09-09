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

	$("#form-dialog" ).dialog({
		 height:400,
		 width: 500,
		 modal: true,
		 autoOpen: false,
		 closeOnEscape: true,
		 position: { my: "center", at: "center", of: window },
		 resizable: true
		 
	});
	
});

function link_popup_click(v_type, v_site) {
	v_url = '<?=site_url("reports/summary_list")?>' + '/' + v_type + '/' + v_site;
	$.ajax({
		url: v_url,
		cache: false
	}).done(function( html ) {
		//alert(html);
		$("#form-dialog-content").html(html);
		$("#form-dialog" ).dialog("open");
	});
	
}
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li><a href="<?=site_url("reports/reportmenu")?>">Report Menu</a> <span class="divider">&gt;</span></li>
	<li class="active">OI/ART</li>
</ul>
<h3>OI/ART Report</h3>
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
<form method="post" action="<?=site_url("reports/submitoiart")?>">
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
      <th>Fingerprint patient</th>
      <th>Behave of patient</th>
      <th>Shown at STD</th>
   </tr>
   <?php
      $row_nb = 0; 
      foreach($reports as $sitecode => $row) : 
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><?=htmlspecialchars($row["site_code"])?></td>
      <td><?=htmlspecialchars($row["site_name"])?></td>
      <td align="right"><?=(!isset($row["nb_register"]) ? 0 : $row["nb_register"])?></td>
      <td align="right"><?=(!isset($row["nb_on_behave"]) ? 0 : $row["nb_on_behave"])?></td>
      <td align="right">
      	<?php if (!isset($row["nb_reach_std"])) : ?>
      	0
      	<?php else: ?>
      	<a href="#" onclick="link_popup_click(4, '<?=$row["site_code"]?>')"><?=$row["nb_reach_std"]?></a>
      	<?php endif;?>
      </td>
   </tr>
   <?php endforeach;?>
</table>
<br/>
<input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportoiart")?>'"/>
<?php endif; ?>

<div id="form-dialog" title="Patient List">
	<p id="form-dialog-content"></p>
</div>