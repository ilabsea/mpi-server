<?php 
   $mpi_user = Isession::getUser();
?>
<div id="bar_login">
   <div class="banner" align="right">
      Welcome <b><?=$mpi_user["user_login"]?></b> | <a href="<?=site_url("main/changepwd")?>">Change Password</a> | <a href="<?=site_url("main/logout")?>">Logout</a>
   </div>
</div>