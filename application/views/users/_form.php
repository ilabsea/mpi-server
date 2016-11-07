<? if(count($user->get_errors()) > 0): ?>
  <? require dirname(dirname(__FILE__)). "/shared/validation_error.php" ?>
<? endif ?>
<? require dirname(dirname(__FILE__)). "/shared/flash.php" ?>

<? $class_error = array_key_exists('user_login', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">*Login</div>
  <div class="span9">
    <?= form_input(array("name" => "user_login", "id" => "user_login", "value" => $user->user_login )) ?>
  </div>
</div>

<? $class_error = array_key_exists('user_fname', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">First Name*</div>
  <div class="span9">
    <?= form_input(array("name" => "user_fname", "id" => "user_fname", "value" => $user->user_fname )) ?>
  </div>
</div>

<? $class_error = array_key_exists('user_lname', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Last Name*</div>
  <div class="span9">
    <?= form_input(array("name" => "user_lname", "id" => "user_lname", "value" => $user->user_lname )) ?>
  </div>
</div>

<div class="row-fluid input-row" >
  <div class="span2">Group*</div>
  <div class="span9">
    <?= form_dropdown('grp_id', User::groups(), $user->grp_id,
                      'id="grp_id" class="tokenizer tokenizer-short"' ) ?>
  </div>
</div>

<?php if($user->new_record()): ?>
<? $class_error = array_key_exists('user_pwd', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Password*</div>
  <div class="span9">
    <?= form_input(array("name" => "user_pwd",
                         "id" => "password",
                         "type" => "password",
                         "value" => $user->user_pwd )) ?>
  </div>
</div>

<? $class_error = array_key_exists('user_confirm_pwd', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Confirm*</div>
  <div class="span9">
    <?= form_input(array("name" => "user_confirm_pwd",
                         "id" => "user_confirm_pwd",
                         "type" => "password",
                         "value" => $user->user_confirm_pwd )) ?>
  </div>
</div>
<? endif ?>

<? $class_error = array_key_exists('user_email', $user->get_errors()) ? 'error' : '' ?>
<div class="row-fluid input-row <?=$class_error ?>" >
  <div class="span2">Email*</div>
  <div class="span9">
    <?= form_input(array("name" => "user_email",
                         "id" => "user_email",
                         "type" => "email",
                         "value" => $user->user_email )) ?>
  </div>
</div>

<div class="row-fluid">
  <div class="span2"></div>
  <div class="span10">
    <button class="btn"> Save </button>
    <? if(!$user->new_record()): ?>
    <a href='<?=site_url("users/regenerate_pwd/".$user->id())?>'
       class="btn btn-primary btn-confirm"
       data-confirm="Are you sure to regenerate password"> Regenerate password </a>
    <? endif;?>
    <a href='<?=site_url("users/index")?>' class="btn btn-danger"> Cancel </a>
  </div>
</div>
