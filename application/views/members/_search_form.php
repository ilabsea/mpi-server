<form method="GET" action="<?=site_url("members/index")?>">
  <?=form_input(array("name" => "order_by",
                      "id" => "order_by",
                      "type" => "hidden",
                      "value" => $params["order_by"] )) ?>

  <?=form_input(array("name" => "order_direction",
                      "id" => "order_direction",
                      "type" => "hidden",
                      "value" => $params["order_direction"] )) ?>

   <div class="row-fluid input-row" >
     <div class="span2">Service</div>
     <div class="span9">
       <?= form_dropdown('serv_id', AppHelper::merge_array(array("" => "All"), $services), $params["serv_id"],
                         'id="serv_id" class="tokenizer tokenizer-short"' ) ?>
     </div>
   </div>

   <div class="row-fluid input-row" >
     <div class="span2">Site code</div>
     <div class="span9">
       <?=form_input(array("name" => "site_code", "value" => $params["site_code"] )) ?>
     </div>
   </div>

   <div class="row-fluid input-row" >
     <div class="span2">Member Login</div>
     <div class="span9">
       <?=form_input(array("name" => "member_login", "value" => $params["member_login"] )) ?>
     </div>
   </div>

   <div class="row-fluid input-row" >
     <div class="span2"></div>
     <div class="span9">
       <input type="submit" value="Search" class="btn btn-primary" />
     </div>
   </div>
</form>
