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
