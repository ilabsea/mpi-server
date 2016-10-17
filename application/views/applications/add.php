<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("applications/index")?>">Application List</a> <span class="divider">&gt;</span></li>
  <li class="active">Add</li>
</ul>

<h3>Create Application</h3>
<?= form_open('applications/create', '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
