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

function verify_option() {
	if ( $("#mpi_patient").is(":checked") || $("#mpi_visit").is(":checked")) {
		return true;
	}
	alert("You have to select at least one table to export.");
	return false;
}
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>	
	<li class="active">CSV Export</li>
</ul>
<h3>CSV Export</h3>
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
<form method="post" action="<?=site_url("datas/downloadcsv")?>">
Please select the following tables to export: <br/>
&nbsp; &nbsp; <input id="mpi_patient" type="checkbox" name="mpi_patient" value="1" <?=(isset($mpi_patient) ? "checked" : "")?>> mpi_patient <br/> 
&nbsp; &nbsp; <input id="mpi_visit" type="checkbox" name="mpi_visit" value="1" <?=(isset($mpi_visit) ? "checked" : "")?>> mpi_visit <br/>

<br/>
<input type="submit" value="Export to CSV" class="btn" onclick="return verify_option();"/>
</form> 
