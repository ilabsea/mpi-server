<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Field Logs</li>
</ul>

<? require dirname(__FILE__). "/_search.php" ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th width='120'>Field Name</th>
      <th>Field Code</th>
      <th>Modified At</th>
      <th>Modified By</th>
      <th>Changes</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($paginate_logs->records as $index => $log): ?>
      <tr>
        <td><?= Paginator::offset() + $index + 1 ?></td>
        <td><?= $log->field_name ?></td>
        <td><?= $log->field_code ?></td>
        <td><?= AppHelper::timeago($log->modified_at) ?> </td>
        <td>
          <span class="label item">
            <?= $log->application_name ?>
          </span>
        </td>
        <td>
          <?php foreach($log->modified_attrs as $key => $value): ?>
            From <strong> <?= $value["from"] ?> </strong> , To <strong> <?= $value["to"] ?> </strong> 
          <? endforeach; ?>
        </td>
        <td><a href='<?= site_url("field_logs/revert/{$log->id}")?>' > Revert Change</a></td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>
<?= $paginate_logs->render(); ?>
