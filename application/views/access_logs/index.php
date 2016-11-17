<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Access Logs</li>
</ul>
<h3>Access Logs</h3>
<? require dirname(__FILE__). "/_search.php" ?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th width='120'>Name</th>
      <th>Access Time</th>
      <th>IP</th>
      <th>Status</th>
      <th>Params</th>
      <th>Action</th>
      <th>Type</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($paginate_logs->records as $index => $log): ?>
      <tr>
        <td><?= Paginator::offset() + $index + 1 ?></td>
        <td><?= $log->application_name ?></td>
        <td><?= $log->created_at ?></td>
        <td><?= $log->ip ?></td>
        <td><?= $log->status ?></td>
        <td>
          <?php foreach($log->params as $key => $value): ?>
            <span style="margin-right: 5px" class="label item">
              <?= "{$key}={$value}" ?>
            </span>
          <?php endforeach; ?>
        </td>
        <td><?= $log->action ?></td>
        <td><?= $log->http_verb ?></td>
        <td><a href="<?= site_url("access_logs/show/".$log->id) ?>"> View </a></td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $paginate_logs->render(); ?>
