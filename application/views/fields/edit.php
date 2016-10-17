<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("fields/index")?>">Field List</a> <span class="divider">&gt;</span></li>
  <li class="active">Edit</li>
</ul>

<h3>Edit Field</h3>

<?= form_open("fields/update/{$field->id}", '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
