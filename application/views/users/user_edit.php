<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li><a href="<?=site_url("users/userlist")?>">User List</a> <span class="divider">&gt;</span></li>
	<li class="active">Modification</li>
</ul>
<h3>User Modification</h3>
<form method="post" action="<?=site_url("users/userupdate")?>">
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
<input type="hidden" name="user_id" value="<?=$user_id?>"/>
<div class="row-fluid">
   <div class="span2">Login*</div>
   <div class="span2"><input type="text" name="user_login" value="<?=htmlspecialchars($user_login)?>"></div>
</div>
<div class="row-fluid">
   <div class="span2">First Name*</div>
   <div class="span2"><input type="text" name="user_fname" value="<?=htmlspecialchars($user_fname)?>"></div>
</div>
<div class="row-fluid">
   <div class="span2">Last Name*</div>
   <div class="span2"><input type="text" name="user_lname" value="<?=htmlspecialchars($user_lname)?>"></div>
</div>
<div class="row-fluid">
   <div class="span2">Group*</div>
   <div class="span2">
       <select name="grp_id">
           <?php 
               foreach($group_list->result_array() as $row) :
                 $selected = $row["grp_id"] == $grp_id ? "selected" : "";
           ?>
                <option value="<?=$row["grp_id"]?>" <?=$selected?>><?=htmlspecialchars($row["grp_name"])?></option>
           <?php endforeach;?>
       </select>
   </div>
</div>
<div class="row-fluid">
   <div class="span2">Email</div>
   <div class="span2"><input type="text" name="user_email" value="<?=htmlspecialchars($user_email)?>"></div>
</div>
<div>
   <button type="submit" class="btn">Save</button> &nbsp; <button type="reset" class="btn" onclick="window.location='<?=site_url("users/useredit/".$user_id)?>'">Cancel</button>
</div>
</form>