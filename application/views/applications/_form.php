<? if(count($application->get_errors()) > 0): ?>
  <? require dirname(dirname(__FILE__)). "/shared/validation_error.php" ?>
<? endif ?>

<?php $class_error = array_key_exists('name', $application->get_errors()) ? 'error' : '' ?>

<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Name*</div>
  <div class="span10">
    <?= form_input(array("name" => "name", "id" => "name", "value" => $application->name )) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Scope*</div>
  <div class="span10">
    <?= form_dropdown('scope_id', $scopes, $application->scope_id,
                      'id="scope_id" class="tokenizer tokenizer-short"' ) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Whitelist access API</div>
  <div class="span10">
    <p class='field-hint'>
      If you enter IP address(comma separated), only server from this IP can access the API.
    </p>
    <?= form_input(array("name" => "whitelist", "id" => "whitelist", "value" => $application->whitelist)) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Status*</div>
  <div class="span10">
    <?= form_dropdown('status', Application::statuses(), $application->status,
                      'id="status" class="tokenizer tokenizer-short"' ) ?>
  </div>
</div>

<br />

<div class="row-fluid">
  <div class="span2"></div>
  <div class="span2">
    <button class="btn"> Save </button>
    <a href='<?=site_url("applications/index")?>' class="btn btn-danger"> Cancel </a>
  </div>
</div>
