<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("users/index")?>">User List</a> <span class="divider">&gt;</span></li>
  <li class="active">Edit</li>
</ul>

<h3>Edit User</h3>
<?= form_open("users/update/{$user->id()}", '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
