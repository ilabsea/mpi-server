<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Report Menu</li>
</ul>
<h3>Report Menu</h3>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>Name</th>
      <th>Description</th>
   </tr>
 
   <?php
      $row_nb = 0; 
      foreach($reports as $key=>$report) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td><a href="<?=site_url("reports/".$key)?>"><?=htmlspecialchars($report["display"])?></a></td>
      <td><?=htmlspecialchars($report["description"])?></td>
   </tr>
   <?php endforeach;?>
</table>