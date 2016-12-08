<h3>Patient Detail</h3>
<div class="row-fluid">
  <div class="span3">Master ID: </div>
  <div class="span3"><?=$dynamic_patient['pat_id']?></div>
</div>

<div class="row-fluid">
  <div class="span3">Gender: </div>
  <div class="span3"><?= $dynamic_patient['pat_gender']?> </div>
</div>

<div class="row-fluid">
   <div class="span3">Registered at: </div>
   <div class="span3"><?=$dynamic_patient['pat_register_site']?> </div>
</div>
<div class="row-fluid">
   <div class="span3">Registered on: </div>
   <div class="span3"><?=$dynamic_patient['date_create']?></div>
</div>
<div class="row-fluid">
   <div class="span3">Age: </div>
   <div class="span3"><?=$dynamic_patient['pat_age']?></div>
</div>
<?php foreach($dynamic_fields as $dynamic_field):?>
  <? if($dynamic_field->is_patient_field()): ?>
    <div class="row-fluid">
       <div class="span3"><?=$dynamic_field->name ?> :</div>
       <div class="span3"><?=AppHelper::h_c($dynamic_patient, $dynamic_field->code)?></div>
    </div>
  <? endif; ?>
<?php endforeach;?>
