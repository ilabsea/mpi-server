<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("applications/index")?>">Application List</a> <span class="divider">&gt;</span></li>
  <li class="active">Edit</li>
</ul>

<h3>Edit Application</h3>

<?= form_open("applications/update/{$application->id}", '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
