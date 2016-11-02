<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>
<form method="get" action="<?=site_url("patients/patientlist")?>">

  <div class="row-fluid input-row" >
    <div class="span2">Service</div>
    <div class="span9">
      <?= form_dropdown('cri_serv_id', array_merge(array("" => "All"), $services), $cri_serv_id,
                        'id="cri_serv_id" class="tokenizer tokenizer-short"' ) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Master Id</div>
    <div class="span9">
      <?=form_input(array("name" => "cri_master_id", "value" => $cri_master_id )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">Sex</div>
    <div class="span9">
      <?foreach(array("" => "All", "1" => "Male", "2" => "Female") as $value => $label):?>
        <span style="margin-right: 40px;">
          <?=form_radio(array("name" => "cri_pat_gender", "value" => "{$value}", "checked" => ($cri_pat_gender == $value) )) ?>
          <?= $label ?>
        </span>
      <? endforeach; ?>
    </div>
  </div>


  <div class="row-fluid input-row" >
    <div class="span2">Visit Date</div>
    <div class="span9">
      <span>From</span>
      <?=form_input(array("name" => "from", "id" => "from", "class" => "short", "value" =>$date_from )) ?>

      <span style="margin-left: 20px;">To </span>
      <?=form_input(array("name" => "date_to", "id" => "to","class" => "short", "value" => $date_to )) ?>
    </div>
  </div>


  <div class="row-fluid input-row" >
    <div class="span2">Visit site code</div>
    <div class="span9">
      <?=form_input(array("name" => "cri_site_code", "value" => $cri_site_code )) ?>
    </div>
  </div>


  <div class="row-fluid input-row" >
    <div class="span2">External Code</div>
    <div class="span9">
      <?=form_input(array("name" => "cri_external_code", "value" => $cri_external_code )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2">External Code2</div>
    <div class="span9">
      <?=form_input(array("name" => "cri_external_code2", "value" => $cri_external_code2 )) ?>
    </div>
  </div>

  <div class="row-fluid input-row" >
    <div class="span2"></div>
    <div class="span9">
      <input type="submit" value="Search" class="btn btn-primary" />
    </div>
  </div>

</form>
