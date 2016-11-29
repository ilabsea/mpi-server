<script type="text/javascript">
  var dataSources = <?= json_encode($rows) ?> ;
  var head = dataSources[0];
  var rows = [];
  for(var i=1; i<dataSources.length; i++) {
    var row = dataSources[i];
    var dateSplit = row[0].split("-");
    row[0] = new Date(dateSplit[0], dateSplit[1], dateSplit[2]);
    for(var rindex=1; rindex< row.length; rindex++)
      row[rindex] = parseInt(row[rindex]);

    rows.push(row);
  }

  google.charts.load('current', {packages: ['corechart', 'line']});
  google.charts.setOnLoadCallback(drawCrosshairs);

  function drawCrosshairs() {
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Date');
    for(var i=1; i<head.length; i++){
      data.addColumn('number', head[i]);
    }

    data.addRows(rows);
    var options = {
      hAxis: {
        title: 'Date'
      },
      vAxis: {
        title: 'Total access'
      },
      crosshair: {
        color: '#000',
        trigger: 'selection'
      },
      backgroundColor: '#F4F4F4',
      chartArea: {
        backgroundColor: {
        fill: '#F4F4F4',
        opacity: 100
      },
    }
  };

  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

  chart.draw(data, options);
  chart.setSelection([{row: 38, column: 1}]);

  }
</script>

<div id="chart_div" style="width: 100%; height: 300px; background: #eee;"></div>
<? require dirname(__FILE__). "/_aggregate_table.php" ?>
