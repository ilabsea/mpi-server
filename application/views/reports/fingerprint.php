<script type="text/javascript">
$(document).ready(function() {

  // $('#datepicker').datepicker({ dateFormat: 'dd.mm.yy' });
  //
  // $( "#from" ).datepicker({
  //   defaultDate: "+1w",
  //   changeMonth: true,
  //   numberOfMonths: 1,
  //   dateFormat: 'dd/mm/yy',
  //   onClose: function( selectedDate ) {
  //     $( "#to" ).datepicker( "option", "minDate", selectedDate );
  //   }
  // });
  //
  // $( "#to" ).datepicker({
  //   defaultDate: "+1w",
  //   changeMonth: true,
  //   numberOfMonths: 1,
  //   dateFormat: 'dd/mm/yy',
  //   onClose: function( selectedDate ) {
  //     $( "#from" ).datepicker( "option", "maxDate", selectedDate );
  //   }
  // });

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
  <li class="active">Fingerprint</li>
</ul>
<h3>Fingerprint Report</h3>
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
<form method="post" action="<?=site_url("reports/submitfingerprint")?>">
<input type="hidden" name="report_type" value="fingeprint">
<table>
  <tr>
    <td>Start Date</td>
    <td>
      <input class='date-picker' type="text" id="from" name="date_from" value="<?=htmlspecialchars($date_from)?>"/>
      &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;
      End Date
      <input class='date-picker' type="text" id="to" name="date_to" value="<?=htmlspecialchars($date_to)?>"/></td>
  </tr>

  <tr>
    <td>Exclude patients without visit:</td>
    <td><input type="checkbox" name="empty_exclude" <?=(isset($empty_exclude) ? "checked" : "")?>></td>
  </tr>
</table>
<br/>
<input type="submit" value="Submit" class="btn"/>
</form>

<?php if ($reports != null) : ?>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>No.</th>
      <th>Number of fingeprints</th>
      <th>Patient List</th>
   </tr>
   <?php
      $row_nb = 0;
      foreach($reports as $sitecode => $row) :
        $pat_id_list = array();
        foreach ($row as $pat_id=>$nb_row) :
          if (!isset($empty_exclude) || (isset($empty_exclude) && $nb_row > 0)) :
            $display = "<a href=\"#\" onclick=\"link_detail_patient_click('".$pat_id."')\">".$pat_id."</a>";
            array_push($pat_id_list, $display);
          else :
              continue;
          endif;

        endforeach;
        if (count($pat_id_list) <= 1) :
          continue;
        endif;
        $row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><?=$row_nb?></td>
      <td align="center"><?=count($pat_id_list)?></td>
      <td align="left"><?=implode(":", $pat_id_list) ?></td>

   </tr>
   <?php endforeach;?>
</table>
<br/>
<input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportfingerprint")?>'"/>
<?php endif; ?>

<div id="form_patient_detail" title="Paitient Detail">
</div>
