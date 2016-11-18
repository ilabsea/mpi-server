<div>
  <table class="table table-striped">
    <thead>
      <tr>
        <?php foreach($rows[0] as $name => $value): ?>
          <th> <?= $value ?> </th>
        <? endforeach ?>
      </tr>
    <thead>

    <tbody>
      <?php for($i=1; $i< count($rows); $i++ ): ?>
        <tr>
        <?php foreach($rows[$i] as $name => $value): ?>
          <td> <?= $value ?> </td>
        <? endforeach ?>
        </tr>
      <? endfor; ?>
    </tbody>
  </table>
</div>
