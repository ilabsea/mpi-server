<?php
function pagination_direction($field, $orderby, $direction) {
  if ($field == $orderby){
    if ($direction == "DESC")
      return "<img src=\"".base_url("img/down.png")."\" />";
    else
      return "<img src=\"".base_url("img/up.png")."\" />";
  }
  return "";
}
?>
<script type="text/javascript">
  $(document).ready(function() {
    $(".header_clickable").click(function() {
  });

  $('#datepicker').datepicker({ dateFormat: 'dd.mm.yy' });

  $( "#from" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 1,
    dateFormat: 'dd/mm/yy',
    onClose: function( selectedDate ) {
      $( "#to" ).datepicker( "option", "minDate", selectedDate );
    }
  });

  $( "#to" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 1,
    dateFormat: 'dd/mm/yy',
    onClose: function( selectedDate ) {
      $( "#from" ).datepicker( "option", "maxDate", selectedDate );
    }
  });
});

function header_click(orderby, orderdirection) {
  var newdirection = 'ASC';
  if (orderby == '<?=$orderby?>') {
    if ('<?=$orderdirection?>' == 'ASC')
      newdirection = "DESC";
    else
      newdirection = "ASC";
  }
  window.location='<?=site_url("patients/patientlist?orderby=")?>' + orderby + "&orderdirection=" + newdirection;
}
</script>
