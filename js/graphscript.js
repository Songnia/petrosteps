var chart = AmCharts.makeChart( "chartdiv", {
  "type": "serial",
  "theme": "light",
  "titles": [ {
    "text": "Cash flow and production profile of upstream assets",
    "size": 15
  } ],
  "legend": {
    "align": "center",
    "equalWidths": false,
    "periodValueText": "total: [[value.sum]]",
    "valueAlign": "left",
    //"valueText": "[[value]] ([[percents]]%)",
    "valueText": "[[value]] ",
    "valueWidth": 100
  },
  "dataProvider": <?php echo json_encode($graph_array);   ?>,
  "valueAxes": [ {
    "id": "v1",
    "stackType": "regular",
    "gridAlpha": 0.07,
    "position": "left",
    "title": "Cash flow"
  }, {
    "id": "v2",
    "stackType": "regular",
    "gridAlpha": 0,
    "axisAlpha": 0,
    "labelsEnabled": false
  } ],
  "graphs": [ {
    "valueAxis": "v1",
    "fillAlphas": 0.5,
    "lineAlpha": 0.5,
    "title": "Cash Flow",
    "valueField": "cashflow"
  }, {
    "valueAxis": "v2",
    "fillAlphas": 0.5,
    "lineAlpha": 0.5,
    "title": "Production",
    "valueField": "production"
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
} );
/**
 * Calculate common minimum and maximum for all axis
 */
chart.addListener("dataUpdated", setAxiScale);
function setAxiScale() {
  // calculate the total minimum and maximum range
  var min = 0, max = 0;
  for ( var x = 0; x < chart.valueAxes.length; x++ ) {
    var axis = chart.valueAxes[x];
    if ( axis.minRR < min )
      min = axis.minRR;
    if ( axis.maxRR > max )
      max = axis.maxRR;
  }
  
  // apply the same scale to all axes
  for ( var x = 0; x < chart.valueAxes.length; x++ ) {
    chart.valueAxes[x].minimum = min;
    chart.valueAxes[x].maximum = max;
  }
  // refresh the chart
  chart.validateNow();
}