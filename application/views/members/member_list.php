<?php 

function pagination_direction($field, $orderby, $direction) {
    if ($field == $orderby) :
        if ($direction == "DESC") :
        	return "<img src=\"".base_url("img/down.png")."\" />";
        else:
            return "<img src=\"".base_url("img/up.png")."\" />";
        endif;
    endif;
    return "";
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$(".header_clickable").click(function() {
	});
});

function header_click(orderby, orderdirection) {
	var newdirection = 'ASC';
	if (orderby == '<?=$orderby?>') {
		if ('<?=$orderdirection?>' == 'ASC') {
			newdirection = "DESC";
		} else {
			newdirection = "ASC";
		}
	}
	window.location='<?=site_url("members/memberlist?orderby=")?>' + orderby + "&orderdirection=" + newdirection;
}
</script>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Member List</li>
</ul>

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

<form method="post" action="<?=site_url("members/search")?>">
<table>
   <tr>
       <td width="40%">Service</td>
       <td>
           <select name="cri_serv_id">
               <option value="">All services</option>
               <?php foreach($services->result_array() as $row): 
               $selected = $cri_serv_id == $row["serv_id"] ? "selected" : "";
               ?>
               <option value="<?=$row["serv_id"]?>" <?=$selected?>><?=htmlspecialchars($row["serv_code"])?></option>
               <?php endforeach;?>
           </select>
       </td>
   </tr>
   <tr>
       <td>Site code</td>
       <td><input type="text" name="cri_site_code" value="<?=htmlspecialchars($cri_site_code)?>"></td>
   </tr>
   <tr>
       <td>Login</td>
       <td><input type="text" name="cri_member_login" value="<?=htmlspecialchars($cri_member_login)?>"></td>
   </tr>
</table>
<input type="submit" value="Search" />
</form>
 
<?php if ($nb_of_page > 1) :?>
<?php 
    $previous_page = $cur_page <= 1 ? 1 : $cur_page - 1;
    $next_page = $cur_page >= $nb_of_page ? $nb_of_page : $cur_page + 1;
?>

<div class="pagination pagination-mini">  
  <ul>  
    <li><a href="<?=site_url("members/memberlist?cur_page=".$previous_page)?>">&laquo;</a></li>
    <?php for ($i=1; $i<=$nb_of_page; $i++) : ?>	
	    <?php if ($i == $cur_page) :  ?>
	        <li class="active"><a href="#"><?=$i?></a></li>
	    <?php else: ?>
	    	<li><a href="<?=site_url("members/memberlist?cur_page=".$i)?>"><?=$i?></a></li>
	    <?php endif;?>
    <?php endfor; ?>  
    <li><a href="<?=site_url("members/memberlist?cur_page=".$next_page)?>">&raquo;</a></li>
  </ul>  
</div> 
<?php endif; ?>
<?php if ($member_list != null) : ?>
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th onclick="header_click('member_login')" class="headerclickable">Login <?=pagination_direction("member_login", $orderby, $orderdirection)?></th>
      <th onclick="header_click('site_code')" class="headerclickable">Site Code <?=pagination_direction("site_code", $orderby, $orderdirection)?></th>
      <th onclick="header_click('site_name')" class="headerclickable">Site Name <?=pagination_direction("site_name", $orderby, $orderdirection)?></th>
      <th onclick="header_click('serv_code')" class="headerclickable">Service <?=pagination_direction("serv_code", $orderby, $orderdirection)?></th>
      <th onclick="header_click('date_create')" class="headerclickable">Registered Date <?=pagination_direction("date_create", $orderby, $orderdirection)?></th>
      <th>Delete</th>
   </tr>
   <?php if ($member_list->num_rows() <= 0) :?>
   <tr>
      <td align="center" colspan="6"><b class="error" style="color: blue">Record not found</b></td>
   </tr>
   <?php endif;?>
   <?php
      $row_nb = 0; 
      foreach($member_list->result_array() as $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td align="center"><?=htmlspecialchars($row["member_login"])?></td>
      <td align="center"><?=htmlspecialchars($row["site_code"])?></td>
      <td><?=htmlspecialchars($row["site_name"])?></td>
      <td align="center"><?=htmlspecialchars($row["serv_code"])?></td>
      <td align="center"><?=htmlspecialchars(date_mysql_to_html($row["date_create"]))?></td>
      <td align="center"><a href="<?=site_url("members/memberdelete/".$row["member_id"])?>" onclick="return confirm('Are you sure to delete this member ?')"><img src="<?=base_url("img/delete.png")?>"/></a></td>
   </tr>
   <?php endforeach;?>
</table>
<div><?=$member_list->num_rows()?> / <?=$total_record?></div>
<?php endif;?>
