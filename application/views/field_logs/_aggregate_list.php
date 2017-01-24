<div>
  <table class="table table-striped">
    <thead>
      <tr>
        <?php foreach($rows[0] as  $value): ?>
          <th> <?= $value ?> </th>
        <? endforeach ?>
        <th> Total </th>
      </tr>
    <thead>

    <tbody>
      <?php
        $total = array();
        $sum = 0;
        for($i=1; $i< count($rows); $i++ ):
          $total_per_row = 0;
          $row = $rows[$i] ?>

        <tr>
        <?php
          foreach($row as $rindex => $value):
            $total[$rindex] = isset($total[$rindex]) ? $total[$rindex] + intval($value) : intval($value);
            if($rindex !=0){
              $sum += intval($value);
              $total_per_row += intval($value);
            }
        ?>
          <td> <?= $value ?> </td>
        <? endforeach ?>
        <td><?= $total_per_row;?></td>
        </tr>
      <? endfor; ?>
    </tbody>
    <tfoot>
      <tr>
        <?php foreach($rows[0] as $rindex => $value): ?>
          <th> <?= $rindex == 0 ? "Total" : $total[$rindex] ;?> </th>
        <? endforeach ?>
        <td><?= $sum ?> </td>
      </tr>
    </tfoot>
  </table>
</div>
