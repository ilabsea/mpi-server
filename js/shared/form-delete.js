$(function(){
  deleteButtonClick();
})

function deleteButtonClick() {
  $(".btn-delete").on('click', function(e){
    var confirmMessage = $(this).attr('data-confirm');
    if(!confirm(confirmMessage)){
      e.preventDefault();
    }
  })
}
