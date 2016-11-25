<script type="text/javascript">
  $(function(){
    handleTableOrderClickable()
  })

  function handleTableOrderClickable() {
    $(".headerclickable").on('click', function(){
      var orderBy = $(this).attr("data-field-id");
      $("#order_by").val(orderBy);
      var newDir = $("#order_direction").val() == "ASC" ? "DESC" : "ASC";
      $("#order_direction").val(newDir);
    
      $("#order_direction").get(0).form.submit();
    });
  }

</script>
