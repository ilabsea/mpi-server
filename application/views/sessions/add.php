<div style="max-width: 400px; width: 100%;" >
  <? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
  <br />

  <?= form_open('sessions/create', '') ?>

    <div class="row-fluid input-row" >
      <div class="span3">Login*</div>
      <div class="span9">
        <?= form_input(array( "name" => "login", "id" => "login", "value" =>  $view_params->get('login') )) ?>
      </div>
    </div>

    <div class="row-fluid input-row" >
      <div class="span3">Password*</div>
      <div class="span9">
        <?= form_input(array("type" =>"password" ,
                             "name" => "password",
                             "id" => "password",
                             "value" => $view_params->get('password') )) ?>
      </div>
    </div>

    <div class="row-fluid input-row">
      <div class="span3"></div>
      <div class="span9">
        <button class="btn"> Login </button>
      </div>
    </div>

  </form>
</div>
