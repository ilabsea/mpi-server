<div class="well">
  <h4>Access Logs filter</h4>
  <?= form_open('field_logs/index', array("method"=> "GET")) ?>
    <span class="label-inline"> </span>
    <?php
      $applications = array();
      foreach(Application::mapper() as $app) {
        $applications[$app] = $app;
      }
    ?>
    <?= form_dropdown('application_name', AppHelper::merge_array(array(""=>"All"),  $applications), $params["application_name"],
                      'id="application_name" class="tokenizer tokenizer-short"' ) ?>

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
