<? if(count($scope->get_errors()) > 0): ?>
  <? require dirname(dirname(__FILE__)). "/shared/validation_error.php" ?>
<? endif ?>

<?php $class_error = array_key_exists('name', $scope->get_errors()) ? 'error' : '' ?>

<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Name*</div>
  <div class="span9">
    <?= form_input(array( "name" => "name", "id" => "name", "value" => $scope->name )) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Searchable fields</div>
  <div class="span9">
    <p class='field-hint'>
      Only fields listed below can be read by this scope
    </p>
    <?= form_dropdown('searchable_fields[]', $fields, $scope->searchable_fields,
                      'multiple id="searchable_fields" class="tokenizer"' ) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Display fields</div>

  <div class="span9">
    <p class='field-hint'>
      Only fields listed below will be returned by this scope
    </p>
    <?= form_dropdown('display_fields[]', $fields, $scope->display_fields,
                      'multiple id="display_fields" class="tokenizer"' ) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Updatable fields</div>

  <div class="span9">
    <p class='field-hint'>
      Only fields listed below can be updated by this scope
    </p>
    <?= form_dropdown('updatable_fields[]', $fields, $scope->updatable_fields,
                      'multiple id="searchable_fields" class="tokenizer"' ) ?>
  </div>
</div>

<br />

<div class="row-fluid">
  <div class="span2"></div>
  <div class="span2">
    <button class="btn"> Save </button>
    <a href='<?=site_url("scopes/index")?>' class="btn btn-danger"> Cancel </a>
  </div>
</div>
