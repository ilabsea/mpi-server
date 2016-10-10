<div class="<?= array_key_exists('name', $field->get_errors()) ? "row-fluid error" : 'row-fluid' ?>" >
  <div class="span2">Name*</div>
  <div class="span2">
    <?= form_input(array( "name" => "name",
                          "id" => "name",
                          "value" => $field->name
                        ) ) ?>
  </div>
</div>

<div class="<?= array_key_exists('code', $field->get_errors()) ? "row-fluid error" : 'row-fluid' ?>" >
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
                            "value" => 0,
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
