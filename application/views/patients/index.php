<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Patients List</li>
</ul>

<? require_once dirname(__FILE__) ."/_helper.php"; ?>
<? require_once dirname(__FILE__) ."/_index.js.php"; ?>
<? require_once dirname(__FILE__) ."/_search_form.php"; ?>

<?php if (count($paginate_patients->records)) : ?>
  <table  class="table table-striped" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="middle">
      <th data-field-id='pat_id' class="headerclickable">Master ID <?=pagination_direction("pat_id", $params)?></th>
      <th data-field-id='pat_gender' class="headerclickable">Gender <?=pagination_direction("pat_gender", $params)?></th>
      <th data-field-id='date_create' class="headerclickable">Register Date <?=pagination_direction("date_create", $params)?></th>
      <th data-field-id='pat_age' class="headerclickable">Age <?=pagination_direction("pat_age", $params)?></th>
      <th data-field-id='pat_register_site' class="headerclickable">Registered at <?=pagination_direction("pat_register_site", $params)?></th>
      <th data-field-id='visits_count' class="headerclickable">Number Visits <?=pagination_direction("visits_count", $params)?></th>
      <th data-field-id='new_pat_id' class="headerclickable">New Master Id<?=pagination_direction("new_pat_id", $params)?></th>
    </tr>

    <?php if ($paginate_patients->total_counts <= 0) :?>
      <tr>
        <td align="center" colspan="7">
          <b class="error" style="color: blue">Record not found</b>
        </td>
      </tr>
    <?php endif;?>

    <?php
      $row_nb = 0;
      foreach($paginate_patients->records as $row) :
        $row_nb++;
    ?>
      <tr style="<?=$row->visit_positives_count > 0 ? 'color:red' : '' ?>" >
        <td>
          <a href="<?=site_url("patients/show/".$row->pat_id)?>">
            <?=$row->pat_id?>
          </a>
          </td>
        <td><?=($row->pat_gender== 2 ? "Female" : "Male")?></td>
        <td><?=date_mysql_to_html($row->date_create)?></td>
        <td><?=$row->pat_age?></td>
        <td><?=$row->pat_register_site?></td>
        <td><?=$row->visits_count?></td>
        <td>
          <a href="<?=site_url("patients/show/".$row->new_pat_id)?>">
            <?=$row->new_pat_id?>
          </a>
        </td>
      </tr>
    <?php endforeach;?>
  </table>
<?php endif;?>
<?= $paginate_patients->render() ?>
