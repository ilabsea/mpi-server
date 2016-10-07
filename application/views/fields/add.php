<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("fields/index")?>">Field List</a> <span class="divider">&gt;</span></li>
  <li class="active">Add</li>
</ul>

<h3>Add Field</h3>
<? if(count($field->errors) >0): ?>
  <div class="well">
    <?= validation_errors(); ?>
  </div>
<? endif ?>

<?= form_open('fields/create', '') ?>
  <div class="<?= array_key_exists('name', $field->errors) ? "row-fluid error" : 'row-fluid' ?>" >
    <div class="span2">Name*</div>
    <div class="span2">
      <?= form_input(array( "name" => "name",
                            "id" => "name",
                            "value" => $field->name
                          ) ) ?>
    </div>
  </div>

  <div class="<?= array_key_exists('code', $field->errors) ? "row-fluid error" : 'row-fluid' ?>" >
    <div class="span2">Code*</div>
    <div class="span2">
      <?= form_input(array( "name" => "code", "id" => "code", "value" => $field->code )) ?>
    </div>
  </div>

  <div class="row-fluid">
    <div class="span2">Type*</div>
    <div class="span2">
      <?= form_dropdown('type', Field::types(), $field->type, 'id="type"' ) ?>
    </div>
  </div>

  <div class="row-fluid">
    <div class="span2">Encrypted*</div>
    <div class="span2">
      <?= form_checkbox(array("name"=> "is_encrypted",
                              "id"=>"is_encrypted",
                              "value" => $field->is_encrypted,
                              "checked" => true,
                              "class" => "hidden") )?>

      <?= form_checkbox(array("name"=> "is_encrypted",
                              "id"=>"is_encrypted",
                              "value" => 1,
                              "checked" => $field->is_encrypted == "1" ? true : false) )?>
    </div>
  </div>

  <div class="row-fluid">
    <div class="span2"></div>
    <div class="span2">
      <button> Save </button>
    </div>
  </div>

</form>
