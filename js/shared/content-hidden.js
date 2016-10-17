$(function(){
  ContentHidden();
});

function ContentHidden(){
  $(".content-hidden").click(function(){
    var $this = $(this);
    var content = $this.html();
    var contentHidden = $this.attr("data-hidden-content");

    $this.html(contentHidden);
    $this.attr("data-hidden-content", content);
  })
}
