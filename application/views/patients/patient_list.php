<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Patients List</li>
</ul>
<h3>Patients List</h3>
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

<table  class="table_list" cellspacing="0" cellpadding="0" width="80%">
   <tr valign="middle">
      <th>Id</th>
      <th>Gender</th>
      <th>Age</th>
      <th>Birth date</th>
      <th>Number Visits</th>
   </tr>	
   <?php
      $row_nb = 0; 
      foreach($patient_list->result_array() as $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><a href="<?=site_url("patients/patientdetail/".$row["pat_id"])?>"><?=htmlspecialchars($row["pat_id"])?></a></td>
      <td align="center"><?=($row["pat_gender"] == 2 ? "Female" : "Male")?></td>
      <td align="center"><?=$row["pat_age"]?></td>
      <td align="center"><?=htmlspecialchars(date_mysql_to_html($row["pat_dob"]))?></td>
      <td align="center"><?=$row["nb_visit"]?></td>
   </tr>
   <?php endforeach;?>
</table>

