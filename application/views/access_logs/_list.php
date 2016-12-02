<table class="table table-striped">
  <thead>
    <tr>
      <th>No</th>
      <th width='120'>Name</th>
      <th>Access Time</th>
      <th>IP</th>
      <th>Status</th>
      <!-- <th>Params</th> -->
      <th>Action</th>
      <th>Type</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($paginate_logs->records as $index => $log): ?>
      <? $klass = $log->status >=400 ? 'item-danger' : '' ?>
      <tr class='<?= $klass?>'>
        <td><?= Paginator::offset() + $index + 1 ?></td>
        <td><?= $log->application_name ?></td>
        <td><?= $log->created_at ?></td>
        <td><?= $log->ip ?></td>
        <td><span class="label item <?=$klass ?>"><?= $log->status ?></span></td>
        <!-- <td>
          <?php foreach($log->params as $key => $value): ?>
            <span style="margin-right: 5px" class="label item">
              <?= "{$key}={$value}" ?>
            </span>
          <?php endforeach; ?>
        </td> -->
        <td><?= $log->action ?></td>
        <td><span class="label item <?=$klass ?>"><?= $log->http_verb ?></span></td>
        <td><a href="<?= site_url("access_logs/show/".$log->id) ?>"> View </a></td>
      </tr>
    <? endforeach ?>
  </tbody>
</table>
<?= $paginate_logs->render(); ?>
