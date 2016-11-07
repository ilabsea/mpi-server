<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("users/edit/".$user->id())?>">Edit User</a> <span class="divider">&gt;</span></li>
  <li class="active">User</li>
</ul>

<h3>Regenerate password</h3>
<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<table class="table table-striped">
  <tbody>
      <tr>
        <td width='180'> Login</td>
        <td><?= $user->user_login ?></td>
      </tr>

      <tr>
        <td>Full name</td>
        <td>
            <span style="margin-right: 5px" class="label item">
              <?= $user->full_name() ?>
            </span>
        </td>
      </tr>

      <tr>
        <td>Group</td>
        <td>
          <span style="margin-right: 5px" class="label item">
            <?= $user->group_name() ?>
          </span>
        </td>
      </tr>


      <tr>
        <td>Created at </td>
        <td><?= $user->created_at?></td>
      </tr>

      <tr>
        <td>Updated at</td>
        <td><?= $user->updated_at?></td>
      </tr>

      <tr>
        <td>Email</td>
        <td><?= $user->user_email?></td>
      </tr>

      <tr>
        <td>Password</td>
        <td class='content-hidden' data-hidden-content="<?= $new_pwd ?>">
          <?= str_repeat("&#x2022;", strlen($new_pwd))?>
        </td>
      </tr>
  </tbody>
</table>
