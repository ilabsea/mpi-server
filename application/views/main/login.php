<br/><br/><br/><br/> 
<form method="post" action="<?=site_url("main/authentication")?>">
<div class="row-fluid">
   <div class="span1 offset4">Login:</div>
   <div class="span2"><input type="text" name="user_login" value=""></div>
</div>

<div class="row-fluid">
   <div class="span1 offset4">Password:</div>
   <div class="span2"><input type="password" name="user_pwd" value=""></div>
</div>
<div class="row-fluid">
   <div class="span8" align="right">
      <?php if (!is_null($error)) : ?>
      <span class="label label-important"><?=htmlspecialchars($error)?></span>
      <?php endif;?>
   </div>
</div>
<div class="row-fluid">
   <div class="span2 offset6" align="right"><button type="submit" class="btn">Submit</button></div>
</div>
</form>
