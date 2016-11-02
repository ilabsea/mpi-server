<?php if ($nb_of_page > 1) :?>
  <?php
      $previous_page = $cur_page <= 1 ? 1 : $cur_page - 1;
      $next_page = $cur_page >= $nb_of_page ? $nb_of_page : $cur_page + 1;
  ?>

  <?php if ($show_partial_page == 0) : ?>
    <div class="pagination pagination-mini">
      <ul>
        <li><a href="<?=site_url("patients/patientlist?cur_page=".$previous_page)?>">&laquo;</a></li>
        <?php for ($i=1; $i<=$nb_of_page; $i++) : ?>
          <?php if ($i == $cur_page) :  ?>
              <li class="active"><a href="#"><?=$i?></a></li>
          <?php else: ?>
            <li><a href="<?=site_url("patients/patientlist?cur_page=".$i)?>"><?=$i?></a></li>
          <?php endif;?>
        <?php endfor; ?>
        <li><a href="<?=site_url("patients/patientlist?cur_page=".$next_page)?>">&raquo;</a></li>
      </ul>
    </div>
  <?php else: ?>
    <div class="pagination pagination-mini">
      <ul>
        <?php if ($start_page > 1) :?>
          <li><a href="<?=site_url("patients/patientlist?cur_page=1")?>" title="Go to the first page">First</a></li>
        <?php endif;?>

        <li><a href="<?=site_url("patients/patientlist?cur_page=".$previous_page)?>">&laquo;</a></li>
        <?php for ($i=$start_page; $i<=$end_page; $i++) : ?>
          <?php if ($i == $cur_page) :  ?>
            <li class="active"><a href="#"><?=$i?></a></li>
          <?php else: ?>
            <li><a href="<?=site_url("patients/patientlist?cur_page=".$i)?>"><?=$i?></a></li>
          <?php endif;?>
        <?php endfor; ?>

        <li><a href="<?=site_url("patients/patientlist?cur_page=".$next_page)?>">&raquo;</a></li>
        <?php if ($end_page < $nb_of_page) :?>
          <li><a href="<?=site_url("patients/patientlist?cur_page=".$nb_of_page)?>" title="Go to the last page">Last</a></li>
        <?php endif;?>
      </ul>
    </div>
  <?php endif;?>
<?php endif; ?>
