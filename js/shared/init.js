$(function(){
  InitDatePicker();
  InitTimeago();
})

function InitTimeago(){
  jQuery(".timeago").timeago();
}

function InitDatePicker(){
  $('input.date-picker').datepicker({
    format: 'yyyy-mm-dd'
  });
}
