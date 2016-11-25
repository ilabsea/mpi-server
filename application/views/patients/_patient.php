<h3>Patient Detail</h3>
<div class="row-fluid">
  <div class="span3">Master ID: </div>
  <div class="span3"><?=$patient->pat_id?></div>
</div>

<div class="row-fluid">
  <div class="span3">Gender: </div>
  <div class="span3"><?= $patient->gender() ?></div>
</div>

<div class="row-fluid">
   <div class="span3">Registered at: </div>
   <div class="span3"><?=$patient->pat_register_site ?></div>
</div>
<div class="row-fluid">
   <div class="span3">Registered on: </div>
   <div class="span3"><?=datetime_mysql_to_html($patient->date_create)?></div>
</div>
<div class="row-fluid">
   <div class="span3">Age: </div>
   <div class="span3"><?=$patient->pat_age?></div>
</div>
