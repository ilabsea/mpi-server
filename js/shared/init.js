$(function(){
  InitDatePicker();
})

function InitDatePicker(){
  $('input.date-picker').datepicker({
    format: 'yyyy-mm-dd'
  });
}
