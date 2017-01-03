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

<?php if ($patients != null) : ?>
<center><b>
<?php if ($report_type == 1) :?>
VCCT Patients shown at OI/ART
<?php endif;?>
</b></center>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>Master Id</th>
      <th>Registered site</th>
      <th>Gender</th>
      <th>Age</th>
      <th>Nb. visits</th>
   </tr>

   <?php
      $row_nb = 0;
      foreach($patients as $sitecode => $row) :
        $row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center">
        <a href="#" onclick="link_detail_patient_click('<?=$row["pat_id"]?>')"><?=htmlspecialchars($row["pat_id"])?></a>
      </td>
      <td align="center"><?=htmlspecialchars($row["pat_register_site"])?></td>
      <td align="center"><?=($row["pat_gender"] == 1 ? "Male" : "Female")?></td>
      <td align="right"><?=($row["pat_age"])?></td>
      <td align="right"><?=($row["nb_visit"])?></td>
   </tr>
   <?php endforeach;?>
</table>
<br/>
<?php endif;?>

<div id="form_patient_detail" title="Paitient Detail">
</div>
