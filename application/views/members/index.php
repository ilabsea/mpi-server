<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Member List</li>
</ul>

<? require_once dirname(dirname(__FILE__)) ."/shared/helper.php"; ?>
<? require_once dirname(__FILE__) ."/_search_form.php"; ?>
<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>

<table  class="table table-striped" cellspacing="0" cellpadding="0" width="100%">

  <tr valign="middle">
    <th data-field-id='member_login' class="headerclickable">Login <?=pagination_direction("member_login",$params)?></th>
    <th data-field-id='site_code' class="headerclickable">Site Code <?=pagination_direction("site_code",$params)?></th>
    <th data-field-id='site_name' class="headerclickable">Site Name <?=pagination_direction("site_name",$params)?></th>
    <th data-field-id='serv_code' class="headerclickable">Service <?=pagination_direction("serv_code",$params)?></th>
    <th data-field-id='date_create' class="headerclickable">Registered Date <?=pagination_direction("date_create",$params)?></th>
    <th width='60'>Action</th>
   </tr>

   <?php if ($paginate_members->total_counts <= 0) :?>
     <tr>
       <td align="center" colspan="6">
         <b class="error">Record not found</b>
       </td>
     </tr>
   <?php endif;?>

  <?php foreach($paginate_members->records as $record) : ?>
    <tr>
      <td align="center"><?=$record->member_login?></td>
      <td align="center"><?=$record->site_code?></td>
      <td><?=$record->site_name?></td>
      <td align="center"><?=$record->serv_code?></td>
      <td align="center"><?=$record->date_create?></td>
      <td align="right">
        <a href="<?=site_url("members/delete/".$record->member_id)?>"
           data-confirm='Are you sure you want to delete?'
           class='btn-delete'>
           Delete
        </a>
      </td>
    </tr>
  <?php endforeach;?>
</table>

<?= $paginate_members->render() ?>
