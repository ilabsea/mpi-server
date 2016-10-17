<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Application Lists</li>
</ul>
<h3>Application Lists
  <div style="float:right;">
    <a class='btn' href='<?=site_url("applications/add")?>'> New Application</a>
  </div>
</h3>

<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<table class="table_list">
  <thead>
    <tr>
      <th>No</th>
      <th width='120'>Name</th>
      <th>Scope</th>
      <th>Whitelist</th>
      <th>Status</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th width='120' valign='middle'>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($applications as $index => $application): ?>
      <tr>
        <td><?= (($page-1) * Imodel::PER_PAGE) + $index + 1 ?></td>
        <td><?= $application->name ?></td>
        <td>
            <span style="margin-right: 5px" class="label item">
              <?= $scopes[$application->scope_id] ?>
            </span>
        </td>
        <td><?= $application->whitelist ?></td>
        <td>
          <span style="margin-right: 5px" class="label item">
            <?= Application::statuses()[$application->status] ?>
          </span>
        </td>
        <td><?= $application->created_at?></td>
        <td><?= $application->updated_at?></td>
        <td>
          <a href="<?= site_url("applications/show/".$application->id) ?>"> View </a> |
          <a href="<?= site_url("applications/edit/".$application->id) ?>"> Edit </a> |
          <a href="<?= site_url("applications/delete/".$application->id) ?>"
             data-confirm='Are you sure you want to delete?' class='btn-delete'> Delete </a>
        </td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>

<!-- <div class='new-item'>
   <a class='btn' href='<?=site_url("applications/add")?>'> New Application</a>
</div> -->
