<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Scope Lists</li>
</ul>
<h3>Dynamic Scope Lists
  <div style="float:right;">
    <a class='btn' href='<?=site_url("scopes/add")?>'> New Scope</a>
  </div>
</h3>

<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th width='120'>Name</th>
      <th>Searchable</th>
      <th>Updatable</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th width='80' valign='middle'>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($scopes as $index => $scope): ?>
      <tr>
        <td><?= (($page-1) * Imodel::PER_PAGE) + $index + 1 ?></td>
        <td><?= $scope->name ?></td>
        <td>
          <?php foreach($scope->searchable_fields as $field_id): ?>
            <span style="margin-right: 5px" class="label item">
              <?= $fields[$field_id] ?>
            </span>
          <?php endforeach; ?>
        </td>
        <td>
          <?php foreach($scope->updatable_fields as $field_id): ?>
            <span style="margin-right: 5px" class="label item">
              <?= $fields[$field_id] ?> </span>
          <?php endforeach; ?>
        </td>
        <td><?= $scope->created_at?></td>
        <td><?= $scope->updated_at?> </td>
        <td>
          <a href="<?= site_url("scopes/edit/".$scope->id) ?>"> Edit </a> |
          <a href="<?= site_url("scopes/delete/".$scope->id) ?>"
             data-confirm='Are you sure you want to delete?' class='btn-delete'> Delete </a>
        </td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>

<!-- <div class='new-item'>
   <a class='btn' href='<?=site_url("scopes/add")?>'> New Scope</a>
</div> -->
