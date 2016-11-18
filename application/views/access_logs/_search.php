<?= form_open('access_logs/index', array("method"=> "GET")) ?>
  <div class="well">
    <span class="label-inline"> </span>
    <?= form_dropdown('application_id', array(""=>"Select Application") + Application::mapper(), $params["application_id"],
                      'id="application_id" class="tokenizer tokenizer-short"' ) ?>

    <span class="label-inline"> </span>
    <?= form_input(array("name" => "from",
                         "id" => "from",
                         "value" => $params["from"],
                         "class" => "date-picker",
                         "style" => "margin-top: 10px; margin-left: 10px;",
                         "placeholder" => "From Date(YYYY-MM-DD)")) ?>

    <span class="label-inline"> </span>
    <?= form_input(array("name" => "to",
                         "id" => "to",
                         "value" => $params["to"],
                         "class" => "date-picker",
                         "style" => "margin-top: 10px; margin-left: 10px;",
                         "placeholder" => "To Date(YYYY-MM-DD)")) ?>

    <button class='btn btn-primary' style="margin-left: 20px;"> Show </button>
    </div>
  </div>
</form>
