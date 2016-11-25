<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<form method="get" action="<?=site_url("patients/index")?>" id="form-patient-search">
  <?=form_input(array("name" => "order_by",
                      "id" => "order_by",
                      "type" => "hidden",
                      "value" => $params["order_by"] )) ?>

  <?=form_input(array("name" => "order_direction",
                      "id" => "order_direction",
                      "type" => "hidden",
                      "value" => $params["order_direction"] )) ?>

  <div class="row-fluid input-row" >
    <div class="span2">Service</div>
    <div class="span9">
      <?= form_dropdown('serv_id', array_merge(array("" => "All"), $services), $params["serv_id"],
                        'id="serv_id" class="tokenizer tokenizer-short"' ) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Master Id</div>
    <div class="span9">
      <?=form_input(array("name" => "master_id", "value" => $params["master_id"] )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Sex</div>
    <div class="span9">
      <?foreach(array("" => "All", "1" => "Male", "2" => "Female") as $value => $label):?>
        <span style="margin-right: 40px;">
          <?=form_radio(array("name" => "pat_gender", "value" => "{$value}", "checked" => ($params["pat_gender"] == $value) )) ?>
          <?= $label ?>
        </span>
      <? endforeach; ?>
    </div>
  </div>


  <div class="row-fluid input-row" >
    <div class="span2">Visit Date</div>
    <div class="span9">
      <span>From</span>
      <?=form_input(array("name" => "from", "id" => "from", "class" => "short date-picker", "value" =>$params["date_from"] )) ?>

      <span style="margin-left: 20px;">To </span>
      <?=form_input(array("name" => "date_to", "id" => "to","class" => "short date-picker", "value" => $params["date_to"] )) ?>
    </div>
  </div>


  <div class="row-fluid input-row" >
    <div class="span2">Visit site code</div>
    <div class="span9">
      <?=form_input(array("name" => "site_code", "value" => $params["site_code"] )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">External Code</div>
    <div class="span9">
      <?=form_input(array("name" => "external_code", "value" => $params["external_code"] )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">External Code2</div>
    <div class="span9">
      <?=form_input(array("name" => "external_code2", "value" => $params["external_code2"] )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2"></div>
    <div class="span9">
      <input type="submit" value="Search" class="btn btn-primary" />
    </div>
  </div>

</form>
