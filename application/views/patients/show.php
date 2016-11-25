<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("patients/index")?>">Patients</a> <span class="divider">&gt;</span></li>
  <li class="active">Detail</li>
</ul>


<? require_once dirname(__FILE__) ."/_patient.php"; ?>
<? require_once dirname(__FILE__) ."/_helper.php"; ?>

<table  class="table table-striped">
   <tr valign="middle">
      <th data-field-id='serv_code' class="headerclickable">Service <?=pagination_direction("serv_code", $params)?></th>
      <th data-field-id='site_code' class="headerclickable">Site <?=pagination_direction("site_code", $params)?></th>
      <th data-field-id='site_name' class="headerclickable">Site Name <?=pagination_direction("site_name", $params)?></th>
      <th data-field-id='visit_date' class="headerclickable">Visit Date <?=pagination_direction("visit_date", $params)?></th>
      <th data-field-id='ext_code' class="headerclickable">External Code <?=pagination_direction("ext_code", $params)?></th>
      <th data-field-id='ext_code_2' class="headerclickable">External Code 2 <?=pagination_direction("ext_code_2", $params)?></th>
      <th data-field-id='info' class="headerclickable">Information <?=pagination_direction("info", $params)?></th>
   </tr>
   <?php foreach($visits as $row) : ?>
   <tr style="<?= strtolower($row->info) == "positive" ? "color: red" : "" ?>" >
      <td align="center"><?=$row->serv_code?></td>
      <td align="center"><?=$row->site_code?></td>
      <td align="center"><?=$row->site_name?></td>
      <td align="center"><?=date_mysql_to_html($row->visit_date)?></td>
      <td align="center"><?=$row->ext_code?></td>
      <td align="center"><?=$row->ext_code_2?></td>
      <td><?=$row->info?></td>
   </tr>
   <?php endforeach;?>
</table>
