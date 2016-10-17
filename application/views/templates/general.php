<!DOCTYPE html>
<html>
<head>
<title>Master Patient Index</title>

  <link href="<?=base_url("css/bootstrap.min.css")?>" rel="stylesheet">
  <link href="<?=base_url("css/mpi.css")?>" rel="stylesheet">
  <link href="<?=base_url("css/share/component.css")?>" rel="stylesheet">
  <link href="<?=base_url("css/share/overide.css")?>" rel="stylesheet">
  <link href="<?=base_url("css/libs/select2.min.css")?>" rel="stylesheet">
  <link href="<?=base_url("js/jquery/ui/themes/redmond/jquery.ui.all.css")?>" rel="stylesheet">

  <script src="<?=base_url("js/jquery-1.9.1.js")?>"></script>


  <script src="<?=base_url("js/jquery/ui/jquery.ui.core.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.widget.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.datepicker.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.mouse.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.draggable.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.position.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.resizable.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.button.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.dialog.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.effect.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.effect-blind.js")?>"></script>
  <script src="<?=base_url("js/jquery/ui/jquery.ui.effect-explode.js")?>"></script>

  <script src="<?=base_url("js/libs/select2.min.js")?>"></script>
  <script src="<?=base_url("js/shared/form-delete.js")?>"></script>
  <script src="<?=base_url("js/shared/tokenizer.init.js")?>"></script>
  <script src="<?=base_url("js/shared/content-hidden.js")?>"></script>

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
