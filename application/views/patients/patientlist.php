<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Patients List</li>
</ul>

<? require_once dirname(__FILE__) ."/_patientlist.js.php"; ?>
<? require_once dirname(__FILE__) ."/_search_form.php"; ?>
<? require_once dirname(__FILE__) ."/_pagination.php"; ?>

<?php if ($patient_list != null) : ?>
  <table  class="table table-striped" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="middle">
      <th onclick="header_click('pat_id')" class="headerclickable">Master ID <?=pagination_direction("pat_id", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_gender')" class="headerclickable">Gender <?=pagination_direction("pat_gender", $orderby, $orderdirection)?></th>
      <th onclick="header_click('date_create')" class="headerclickable">Register Date <?=pagination_direction("date_create", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_age')" class="headerclickable">Age <?=pagination_direction("pat_age", $orderby, $orderdirection)?></th>
      <th onclick="header_click('pat_register_site')" class="headerclickable">Registered at <?=pagination_direction("pat_register_site", $orderby, $orderdirection)?></th>
      <th onclick="header_click('nb_visit')" class="headerclickable">Number Visits <?=pagination_direction("nb_visit", $orderby, $orderdirection)?></th>
      <th onclick="header_click('new_pat_id')" class="headerclickable">New Master Id<?=pagination_direction("new_pat_id", $orderby, $orderdirection)?></th>
    </tr>

    <?php if ($patient_list->num_rows() <= 0) :?>
      <tr>
        <td align="center" colspan="7"><b class="error" style="color: blue">Record not found</b></td>
      </tr>
    <?php endif;?>
    <?php
      $row_nb = 0;
      foreach($patient_list->result_array() as $row) :
        $row_nb++;
    ?>
      <tr <?=($row["nb_visit_positive"]>0?"style=\"background: red\"":"")?>>
        <td align="center"><a href="<?=site_url("patients/patientdetail/".$row["pat_id"])?>"><?=htmlspecialchars($row["pat_id"])?></a></td>
        <td align="center"><?=($row["pat_gender"] == 2 ? "Female" : "Male")?></td>
        <td align="center"><?=date_mysql_to_html($row["date_create"])?></td>
        <td align="center"><?=$row["pat_age"]?></td>
        <td align="center"><?=htmlspecialchars($row["pat_register_site"])?></td>
        <td align="center"><?=$row["nb_visit"]?></td>
        <td align="center"><a href="<?=site_url("patients/patientdetail/".$row["new_pat_id"])?>"><?=htmlspecialchars($row["new_pat_id"])?></a></td>
      </tr>
    <?php endforeach;?>
  </table>
  <div><?=$patient_list->num_rows()?> / <?=$total_record?></div>
<?php endif;?>
