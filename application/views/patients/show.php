<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("patients/index")?>">Patients</a> <span class="divider">&gt;</span></li>
  <li class="active">Detail</li>
</ul>

<? require_once dirname(__FILE__) ."/_patient.php"; ?>
<? require_once dirname(dirname(__FILE__)) ."/shared/helper.php"; ?>

<div class="scroll-container">
  <table  class="table table-striped" >
    <tr valign="middle">
        <th>Service</th>
        <th>Site</th>
        <th>Site Name</th>
        <th>Visit Date</th>
        <th>External Code</th>
        <th>External Code 2</th>
        <th>Information</th>
        <?php foreach($dynamic_fields as $dynamic_field):?>
          <? if($dynamic_field->is_visit_field()): ?>
            <th><?= $dynamic_field->name ?> </th>
          <? endif; ?>
        <?php endforeach;?>
     </tr>
    <?php foreach($dynamic_visits as $row) : ?>
      <tr style="<?= strtolower($row['info']) == "positive" ? "color: red" : "" ?>" >
          <td align="center"><?=$row['serv_code']?></td>
          <td align="center"><?=$row['site_code']?></td>
          <td align="center"><?=$row['site_name']?></td>
          <td align="center"><?=date_mysql_to_html($row['visit_date'])?></td>
          <td align="center"><?=$row['ext_code']?></td>
          <td align="center"><?=$row['ext_code_2']?></td>
          <td><?=$row['info']?></td>
          <?php foreach($dynamic_fields as $dynamic_field):?>
            <? if($dynamic_field->is_visit_field()): ?>
              <th><?= AppHelper::h_c($row, $dynamic_field->code) ?> </th>
            <? endif; ?>
          <?php endforeach;?>
       </tr>
    <?php endforeach;?>
  </table>
</div>
