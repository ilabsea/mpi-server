<script type="text/javascript">
$(document).ready(function() {
  $("#form_patient_detail" ).dialog({
     height:400,
     width: 800,
     modal: true,
     autoOpen: false,
     closeOnEscape: true,
     position: { my: "center", at: "center", of: window },
     resizable: true

  });
});

function link_detail_patient_click(v_pat_id) {
  v_url = '<?=site_url("reports/patient_detail")?>' + '/' + v_pat_id;
  $.ajax({
    url: v_url,
    cache: false
  }).done(function( html ) {
    //alert(html);
    $("#form_patient_detail").html(html);
    $("#form_patient_detail" ).dialog("open");
  });

}
</script>

<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("reports/reportmenu")?>">Report Menu</a> <span class="divider">&gt;</span></li>
  <li class="active">Duplicate</li>
</ul>
<h3>Duplicate Report</h3>
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
<form method="post" action="<?=site_url("reports/submitduplicate")?>">
<table>
  <tr>
    <td width="20%">Start Date</td>
    <td>
      <input type="text" class='date-picker' id="from" name="date_from" value="<?=htmlspecialchars($date_from)?>"/>
      &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; End Date
      <input type="text" class='date-picker' id="to" name="date_to" value="<?=htmlspecialchars($date_to)?>"/>
    </td>
  </tr>
  <tr valign="top">
    <td>Report</td>
    <td>
       <input type="radio" name="report_type" <?=($report_type == 0 ? "checked" : "")?> value="0" /> Number of Patient with register more than one site (OI/ART)<br/>
       <input type="radio" name="report_type" <?=($report_type == 1 ? "checked" : "")?> value="1" /> Number of Patient test in VCCT more than one time
    </td>
  </tr>
</table>
<br/>
<input type="submit" value="Submit"/>
</form>

<?php if ($reports != null) : ?>
  <?php if ($report_type == 0): ?>
  &nbsp; Patients found: <?=count($reports)?><br/>
  <table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
     <tr valign="middle">
        <th>Master id</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Nb. Register</th>
     </tr>
     <?php if (count($reports) <= 0) : ?>
     <tr>
           <td align="center" colspan="4"><b class="error" style="color: blue">Record not found</b></td>
        </tr>
     <?php endif;?>

     <?php
        $row_nb = 0;
        foreach($reports as $row) :
          $row_nb++;
     ?>
     <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
        <td align="center">
            <a href="#" onclick="link_detail_patient_click('<?=$row["pat_id"]?>')"><?=htmlspecialchars($row["pat_id"])?></a>
        </td>
        <td align="center"><?=htmlspecialchars($row["pat_gender"] == "1" ? "Male" : "Female")?></td>
        <td align="center"><?=htmlspecialchars($row["pat_age"])?></td>
        <td align="center"><?=htmlspecialchars($row["nb_register"])?></td>
     </tr>
     <?php endforeach;?>
  </table>
  <br/>
  <input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportduplicateoiart")?>'"/>
  <?php endif; ?>

  <?php if ($report_type == 1): ?>
  &nbsp; Patients found: <?=count($reports)?><br/>
  <table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
     <tr valign="middle">
        <th>Master id</th>
        <th>Nb. of test</th>
        <th>Positive</th>
        <th>Negative</th>

     </tr>
     <?php if (count($reports) <= 0) : ?>
     <tr>
           <td align="center" colspan="4"><b class="error" style="color: blue">Record not found</b></td>
        </tr>
     <?php endif;?>

     <?php
        $row_nb = 0;
        foreach($reports as $pat_id => $row) :
          $row_nb++;
     ?>
     <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
        <td align="center">
            <a href="#" onclick="link_detail_patient_click('<?=$pat_id?>')"><?=htmlspecialchars($pat_id)?></a>
        </td>
        <td align="center"><?=($row["positive"] + $row["negative"])?></td>
        <td align="center"><?=$row["positive"]?></td>
        <td align="center"><?=$row["negative"]?></td>
     </tr>
     <?php endforeach;?>
  </table>
  <br/>
  <input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportduplicatevcct")?>'"/>
  <?php endif; ?>
<?php endif; ?>

<div id="form_patient_detail" title="Paitient Detail">
</div>
