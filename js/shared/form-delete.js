$(function(){
  confirmButtonClick();
})

function confirmButtonClick() {
  $(".btn-delete, .btn-confirm").on('click', function(e){
    var confirmMessage = $(this).attr('data-confirm');
    if(!confirm(confirmMessage)){
      e.preventDefault();
    }
  })
}
