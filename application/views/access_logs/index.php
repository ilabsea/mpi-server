<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Access Logs</li>
</ul>

<? require dirname(__FILE__). "/_search.php" ?>

<div class='item-nav-group'>
	<button class='btn btn-primary' style="margin-left: 20px;"> Export As CSV </button>

  <a href='<?=AppHelper::url(array("type" => "list")) ?>'
     class='item-nav <?= $params['type'] != 'graph' ? 'active' : ''  ?>'><i class="icon icon-th"></i></a>

  <a href='<?=AppHelper::url(array("type" => "graph")) ?>'
     class='item-nav <?= $params['type'] == 'graph' ? 'active' : '' ?>'><i class="icon icon-signal"></i></a>
</div>

<? if($params['type'] == 'graph'): ?>
  <? require dirname(__FILE__). "/_graph.php" ?>
<? else: ?>
  <? require dirname(__FILE__). "/_list.php" ?>
<? endif; ?>
