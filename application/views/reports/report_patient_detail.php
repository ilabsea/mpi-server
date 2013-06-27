
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
      <th>Service</th>
      <th>Site</th>
      <th>Site Name</th>
      <th>Visit Date</th>
      <th>External Code</th>
      <th>External Code 2</th>
      <th>Information</th>
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
<div id="form_patient_detail" title="Paitient Detail">
</div>

