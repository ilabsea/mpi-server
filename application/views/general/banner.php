<div id="bar_login">
<?php if($current_user): ?>
  <div class="banner" align="right">
    Welcome <b>
    <?=$current_user->user_login?> </b> |
    <a href="<?=site_url("passwords/edit")?>">Change Password</a> |
    <a href="<?=site_url("logout")?>">Logout</a>
  </div>
<? else: ?>
 <br>
<? endif ?>
</div>
