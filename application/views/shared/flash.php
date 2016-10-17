<?php if(count(ISession::getFlashes()) > 0) : ?>
  <?php foreach(ISession::getFlashes() as $key => $value):?>
    <div class="label label-important label-<?=$key ?> flash">
      <?= $value ?>
    </div>
  <?php endforeach; ?>
  <?php Isession::clearFlashes() ?>
<? endif; ?>
