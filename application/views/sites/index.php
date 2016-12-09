
<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Site List</li>
</ul>
<? require_once dirname(dirname(__FILE__)) ."/shared/helper.php"; ?>
<? require_once dirname(__FILE__) ."/_search_form.php"; ?>

<table class="table table-striped" cellspacing="0" cellpadding="0" width="100%">
  <tr valign="middle">
    <th data-field-id='site_code' class="headerclickable">Site Code <?=pagination_direction("site_code",$params)?></th>
    <th data-field-id='site_name' class="headerclickable">Site Name <?=pagination_direction("site_name",$params)?></th>
    <th data-field-id='pro_code' class="headerclickable">Province Code <?=pagination_direction("pro_code",$params)?></th>
    <th data-field-id='pro_name' class="headerclickable">Province Name <?=pagination_direction("pro_name",$params)?></th>
    <th data-field-id='od_name' class="headerclickable">OD Name <?=pagination_direction("od_name",$params)?></th>
    <th data-field-id='serv_code' class="headerclickable">Service <?=pagination_direction("serv_code",$params)?></th>
  </tr>


  <?php if ($paginate_sites->total_counts == 0) :?>
   <tr>
     <td align="center" colspan="6">
       <b class="error">Record not found</b>
     </td>
   </tr>
  <?php endif;?>

  <?php foreach($paginate_sites->records as $record) : ?>
    <tr>
      <td align="center"><?=$record->site_code?></td>
      <td align="center"><?=$record->site_name?></td>
      <td><?=$record->pro_code?></td>
      <td><?=$record->pro_name?></td>
      <td align="center"><?=$record->od_name?></td>
      <td align="center"><?=$record->serv_code?></td>
    </tr>
  <?php endforeach;?>
</table>
<?= $paginate_sites->render() ?>
