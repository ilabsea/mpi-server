<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Dynamic Field Lists</li>
</ul>

<h3>Dynamic field lists
  <div style='float:right;'>
    <a class='btn btn-new' href='<?=site_url("fields/add")?>'> New Dynamic Field</a>
  </div>
</h3>

<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th>Name</th>
      <th>Code</th>
      <th>Encryption</th>
      <th>Field Type</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($paginate_fields->records as $index => $field): ?>
      <tr>
        <td><?= Paginator::offset() + $index + 1 ?></td>
        <td><?= $field->name ?></td>
        <td><?= $field->code ?></td>
        <td>
          <span class="label item">
            <?= $field->is_encrypted ? "Encrypted" : "Plain" ?>
          </span>
        </td>
        <td> <span class="label item"><?= $field->type ?></span></td>
        <td><?= $field->created_at?></td>
        <td><?= $field->updated_at?> </td>
        <td>
          <? if($field->dynamic_field == 1): ?>
            <a href="<?= site_url("fields/edit/".$field->id) ?>"> Edit </a> |
            <a href="<?= site_url("fields/delete/".$field->id) ?>"
               data-confirm='Are you sure you want to delete?' class='btn-delete'> Delete </a>
          <? else: ?>
            Built-in
          <? endif ?>
        </td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $paginate_fields->render(); ?>
