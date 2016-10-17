<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("scopes/index")?>">Scope List</a> <span class="divider">&gt;</span></li>
  <li class="active">Add</li>
</ul>

<h3>Create Field Permission</h3>
<?= form_open('scopes/create', '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
