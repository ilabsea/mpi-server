<div class="well">
  <h4>Access Logs filter</h4>
  <?= form_open('access_logs/index', array("method"=> "GET")) ?>
  <?= form_input(array("name" => "type",
                       "id" => "type",
                       "type" => "hidden",
                       "value" => $params["type"])) ?>

    <span class="label-inline"> </span>
    <?= form_dropdown('application_id', AppHelper::merge_array(array(""=>"Select Application"),  Application::mapper()), $params["application_id"],
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
  </form>
</div>
