<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("fields/index")?>">Field List</a> <span class="divider">&gt;</span></li>
  <li class="active">Add</li>
</ul>

<h3>Add Field</h3>

<?= form_open('fields/create', '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
