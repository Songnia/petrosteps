<script>
var chart = AmCharts.makeChart("chartdiv", {
  "type": "serial",
  "theme": "light",
  "titles": [ {
    "text": "Cash flow and production profile of upstream assets",
    "size": 15
  } ],
  "legend": {
    "align": "center",
    "equalWidths": false,
    "valueAlign": "left",
    "valueText": "[[value]]",
    "valueWidth": 100
  },
  "dataProvider": <?php echo json_encode($graph_array); ?>,
  "valueAxes": [ {
    "id": "cashflowAxis",
    "gridAlpha": 0.07,
    "position": "left",
    "title": "Cash flow",
    "axisColor": "#67b7dc",
    "color": "#67b7dc"
  }, {
    "id": "productionAxis",
    "gridAlpha": 0.07,
    "position": "right",
    "title": "Production",
    "axisColor": "#fdd400",
    "color": "#fdd400"
  } ],
  "graphs": [ {
    "valueAxis": "cashflowAxis",
    "fillAlphas": 0.55,
    "lineAlpha": 0.65,
    "lineColor": "#67b7dc",
    "fillColors": "#67b7dc",
    "gradientOrientation": "vertical",
    "lineThickness": 2,
    "title": "Cash Flow",
    "valueField": "cashflow",
    "balloonText": "Cash flow: [[value]]"
  }, {
    "valueAxis": "productionAxis",
    "fillAlphas": 0.45,
    "lineAlpha": 0.65,
    "lineColor": "#fdd400",
    "fillColors": "#fdd400",
    "gradientOrientation": "vertical",
    "lineThickness": 2,
    "title": "Production",
    "valueField": "production",
    "balloonText": "Production: [[value]]"
  } ],
  "plotAreaBorderAlpha": 0,
  "marginLeft": 0,
  "marginBottom": 0,
  "chartCursor": {
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "project_year",
  "categoryAxis": {
    "startOnAxis": true,
    "axisColor": "#DADADA",
    "gridAlpha": 0.07,
    "title": "Project year"
  }
});

<?php if(@$_SESSION['chartshow'] == 'hide'): ?>
$('#mainChartDiv').hide();
$('.see_graph_btn').show();
$('.hide_graph_btn').hide();
<?php else: ?>
$('#mainChartDiv').show();
$('.see_graph_btn').hide();
$('.hide_graph_btn').show();
<?php endif; ?>
</script>
