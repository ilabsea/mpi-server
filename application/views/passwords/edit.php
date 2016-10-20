<ul class="breadcrumb">
  <li><a href="<?=site_url("homes/index")?>">Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Change Password</li>
</ul>

<h3>Change Password</h3>

<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<?= form_open('passwords/update', '') ?>

  <div class="row-fluid input-row" >
    <div class="span2">Old Password*</div>
    <div class="span9">
      <?= form_input(array("type" =>"password" ,
                           "name" => "old_password",
                           "id" => "old_password",
                           "value" => $view_params->get('old_password') )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Password*</div>
    <div class="span9">
      <?= form_input(array("type" =>"password" ,
                           "name" => "new_password",
                           "id" => "new_password",
                           "value" => $view_params->get('new_password') )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Confirm Password*</div>
    <div class="span9">
      <?= form_input(array("type" =>"password" ,
                           "name" => "confirm_password",
                           "id" => "confirm_password",
                           "value" => $view_params->get('confirm_password') )) ?>
    </div>
  </div>

  <div class="row-fluid input-row">
    <div class="span2"></div>
    <div class="span9">
      <button class="btn"> Change Password </button>
      <a href='<?=site_url("homes/index")?>' class="btn btn-danger"> Cancel </a>
    </div>
  </div>
</form>
