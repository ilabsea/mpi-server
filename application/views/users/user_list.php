<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Users List</li>
</ul>
<h3>Users List</h3>
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
<table  class="table_list" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>Login</th>
      <th>Name</th>
      <th>Group</th>
      <th>Email</th>
      <th>Delete</th>
   </tr>	
   <?php
      $row_nb = 0; 
      foreach($user_list->result_array() as $row) :
      	$row_nb++;
   ?>
   <tr <?=(($row_nb % 2)?"":"class=\"even_row\"")?>>
      <td><a href="<?=site_url("users/useredit/".$row["user_id"])?>"><?=htmlspecialchars($row["user_login"])?></a></td>
      <td><?=htmlspecialchars($row["user_fname"]." ".$row["user_lname"])?></td>
      <td><?=htmlspecialchars($row["grp_name"])?></td>
      <td><?=htmlspecialchars($row["user_email"])?></td>
      <td align="center"><a href="<?=site_url("users/userdelete/".$row["user_id"])?>" onclick="return confirm('Are you sure to delete this user ?')"><img src="<?=base_url("img/delete.png")?>"/></a></td>
   </tr>
   <?php endforeach;?>
</table>
<br/>
<div>
   <button type="button" class="btn" onclick="window.location='<?=site_url("users/usernew")?>'">New User</button>
</div>
