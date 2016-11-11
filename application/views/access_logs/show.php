<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("access_logs/index")?>">Access logs</a> <span class="divider">&gt;</span></li>
  <li class="active">Detail</li>
</ul>

<h3><?= $log->application_name ?></h3>

<table class="table table-striped">
  <tbody>
      <tr>
        <td width='180'> Name</td>
        <td><?= $log->application_name ?></td>
      </tr>

      <tr>
        <td width='180'> Access time</td>
        <td><?= $log->created_at ?></td>
      </tr>

      <tr>
        <td>IP</td>
        <td><?= $log->ip ?></td>
      </tr>

      <tr>
        <td>Status</td>
        <td>
          <span style="margin-right: 5px" class="label item">
            <?= $log->status ?>
          </span>
        </td>
      </tr>

      <tr>
        <td> Params </td>
        <td>
          <? foreach($log->params as $key => $value): ?>
            <span style="margin-right: 5px" class="label item">
              <?= "{$key}={$value}" ?>
            </span>
          <? endforeach; ?>
        </td>
      </tr>

      <tr>
        <td>Action</td>
        <td><?= $log->action?></td>
      </tr>

      <tr>
        <td>Type</td>
        <td><?= $log->http_verb?></td>
      </tr>

      <tr>
        <td>Url</td>
        <td><?= $log->url ?></td>
      </tr>
  </tbody>
</table>
