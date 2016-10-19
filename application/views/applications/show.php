<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("applications/index")?>">Application List</a> <span class="divider">&gt;</span></li>
  <li class="active">Application credential</li>
</ul>

<h3><?= $application->name ?></h3>

<table class="table_list">
  <tbody>
      <tr>
        <td width='180'> Name</td>
        <td><?= $application->name ?></td>
      </tr>

      <tr>
        <td> Scope</td>
        <td>
            <span style="margin-right: 5px" class="label item">
              <?= $scopes[$application->scope_id] ?>
            </span>
        </td>
      </tr>

      <tr>
        <td>Whitelist</td>
        <td><?= $application->whitelist ?></td>
      </tr>

      <tr>
        <td>Status</td>
        <td>
          <span style="margin-right: 5px" class="label item">
            <?= Application::statuses()[$application->status] ?>
          </span>
        </td>
      </tr>

      <tr>
        <td>Created at </td>
        <td><?= $application->created_at?></td>
      </tr>

      <tr>
        <td>Updated at</td>
        <td><?= $application->updated_at?></td>
      </tr>

      <tr>
        <td>API key</td>
        <td><?= $application->api_key?></td>
      </tr>

      <tr>
        <td>API secret</td>
        <td class='content-hidden' data-hidden-content="<?= $application->api_secret ?>">
          <?= str_repeat("&#x2022;", strlen($application->api_secret))?>
        </td>
      </tr>
  </tbody>
</table>

<!-- <div class='new-item'>
   <a class='btn' href='<?=site_url("applications/add")?>'> New Application</a>
</div> -->
