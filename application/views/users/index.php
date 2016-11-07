<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Users List</li>
</ul>

<h3>Users List
  <div style="float:right;">
    <a class='btn' href='<?=site_url("users/add")?>'> New User</a>
  </div>
</h3>

<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<table class="table table-striped">
   <tr valign="middle">
      <th>Login</th>
      <th>Name</th>
      <th>Group</th>
      <th>Email</th>
      <th width='90' valign='middle'> Action </th>
   </tr>

   <?php foreach($users as $index => $user) : ?>
     <tr>
        <td> <?=$user->user_login?> </td>
        <td> <?=$user->full_name()?> </td>
        <td> <?=$user->group_name()?> </td>
        <td> <?=$user->user_email ?> </td>
        <td align="center" >
          <a href="<?= site_url("users/edit/".$user->id()) ?>"> Edit </a> |
          <a href="<?=site_url('users/delete/'.$user->id())?>"
             data-confirm = "Are you sure to delete this user ?"
             class = 'btn-delete'>
             Delete
          </a>
        </td>
     </tr>
   <?php endforeach;?>
</table>
