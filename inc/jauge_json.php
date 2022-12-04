<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>OpenSimulator Manager Web</title>
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" media="all" type="text/css" id="css" href="<?php echo $url; ?>" />
    <link rel="stylesheet" href="css/btn3d.css" type="text/css" />
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link rel="stylesheet" href="css/custom.css" type="text/css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
		
    <script>
        $('#myTab a').click(function (e) {e.preventDefault(); $(this).tab('show');})
        $('#myTab a[href="#profile"]').tab('show')
        $('#myTab a:first').tab('show')
        $('#myTab a:last').tab('show')
        $('#myTab li:eq(2) a').tab('show')
    </script>
</head>
<body>

<html>
<head>
<title><?php echo $_GET['name'];?></title>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" language="JavaScript">
 
SimFPS = 0;
PhyFPS = 0;
Memory = 0;
RootAg = 0;
ChldAg = 0;
Uptime = "";
Version = "";
 
setInterval(function(){
$.getJSON(
  "<?php echo $_GET['url'];?>/?callback=?",  
  function(data){
  SimFPS = Math.round(data.SimFPS);
  PhyFPS = Math.round(data.PhyFPS);
  Memory = Math.round(data.Memory);
  ChldAg = data.ChldAg;
  RootAg = data.RootAg;
  Uptime = data.Uptime;
  Version = data.Version;
  drawChart();
  setTags();
  })}, 3000
);
 
 
google.load("visualization", "1", {packages:["gauge"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
  var cdata = new google.visualization.DataTable();
  cdata.addColumn('string', 'Label');
  cdata.addColumn('number', 'Value');
  cdata.addRows(3);
  cdata.setValue(0, 0, 'SimFPS');
  cdata.setValue(0, 1, SimFPS);
  cdata.setValue(1, 0, 'PhyFPS');
  cdata.setValue(1, 1, PhyFPS);
  cdata.setValue(2, 0, 'Memory');
  cdata.setValue(2, 1, Memory);
 
  var chart = new
    google.visualization.Gauge(document.getElementById('chart_div'));
  var options = {width: 400, height: 200, redFrom: 90, redTo: 100,
    yellowFrom:75, yellowTo: 90, minorTicks: 5};
  chart.draw(cdata, options);
}
 
$(function() {
    timer.start(100);
    });
 
function setTags() {
  $("#par-uptime").text("Uptime: "  + Uptime);
  $("#par-ragent").text("Root Agent: " + RootAg);
  $("#par-version").text("Version: " + Version);
  $("#par-cagent").text("Child Agent: " + ChldAg);
}
</script>
 
</head>
<body>
<center>
	<table>
	  <tr><td>
		  <div id="par-version">version</div>
		</td><td>
		  <div id="par-ragent">root agent</div>
	  </td></tr><tr><td>
		  <div id="par-uptime">uptime</div>
		</td><td>
		  <div id="par-cagent">child agent</div>
	  </td></tr>
	</table>

	<div id="chart_div"></div>
</center>
</body>
</html>