<!DOCTYPE html>
<html>
<head>
<title>Master Patient Index</title>
<!-- Bootstrap -->
	<link href="<?=base_url("css/bootstrap.min.css")?>" rel="stylesheet">
	<link href="<?=base_url("css/mpi.css")?>" rel="stylesheet">
	<script src="<?=base_url("js/jquery-1.8.2.min.js")?>"></script>
	<script src="<?=base_url("js/bootstrap.min.js")?>"></script>
</head>
<body>
	<div class="container">
	   <div><?php require_once APPPATH."views/general/header.php";?></div>
	   <div><?php require_once APPPATH."views/general/banner_login.php";?></div>
	   <div><?php require_once APPPATH."views/".$k_main_body_view.".php";?></div>
	</div>
</body>
</html>