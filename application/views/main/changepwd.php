<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Change Password</li>
</ul>
<h3>Change Password</h3>
<form method="post" action="<?=site_url("main/changepwdsave")?>">
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
<div class="row-fluid">
   <div class="span3">Old Password*</div>
   <div class="span1"><input type="password" name="user_pwd" value=""></div>
</div>
<br/><br/>
<div class="row-fluid">
  <div class="span3">New Password*</div>
   <div class="span1"><input type="password" name="user_new_pwd" value=""></div>
</div>
<div class="row-fluid">
  <div class="span3">Password Confirmation*</div>
   <div class="span1"><input type="password" name="user_confirm_pwd" value=""></div>
</div>
<div>
   <button type="submit" class="btn">Save</button> &nbsp; <button type="reset" class="btn">Cancel</button>
</div>
</form>