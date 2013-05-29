<!DOCTYPE html>
<html>
<head>
<title>Master Patient Index</title>
<!-- Bootstrap -->
	<link href="<?=base_url("css/bootstrap.min.css")?>" rel="stylesheet">
	<link href="<?=base_url("css/mpi.css")?>" rel="stylesheet">
	<script src="<?=base_url("js/jquery-1.8.2.min.js")?>"></script>
	<link href="<?=base_url("js/jquery/ui/themes/redmond/jquery.ui.all.css")?>" rel="stylesheet">
	
	
	<script src="<?=base_url("js/jquery/ui/jquery.ui.core.js")?>"></script>
	<script src="<?=base_url("js/jquery/ui/jquery.ui.widget.js")?>"></script>
	<script src="<?=base_url("js/jquery/ui/jquery.ui.datepicker.js")?>"></script>
	
	
	<script src="<?=base_url("js/bootstrap.min.js")?>"></script>
</head>
<body>
	<div class="container">
	   <div><?php require_once APPPATH."views/general/header.php";?></div>
	   <div><?php require_once APPPATH."views/general/banner.php";?></div>
	   <div><?php require_once APPPATH."views/".$k_main_body_view.".php";?></div>
	</div>
	<br/>
</body>
</html>