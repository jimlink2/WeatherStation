<?php
require "config.php";
if (isset($_GET['temp']) && !empty($_GET['temp'])) {
  $datarecvd = true;
  $temp = $_GET['temp'];
  $rawtemp = $_GET['rawtemp'];
  $adjfactor = $_GET['adjfactor'];
  $dewpt = $_GET['dewpt'];
  $humidity = $_GET['humidity'];
  $moist = $_GET['moist'];
  $dewpot = $_GET['dewpot'];
  $presshpa = $_GET['presshpa'];
  $pressinhg = $_GET['pressinhg'];
  $winddir = $_GET['winddir'];
  $windspd = $_GET['windspd'];
  $windlast = $_GET['windlast'];
  $windgust = $_GET['windgust'];
  $rainrate = $_GET['rainrate'];
  $rainday = $_GET['rainday'];
  $time = $_GET['time'];
  $nextupl = $_GET['nextupl'];
  $lastconn = $_GET['lastconn'];
  $WUstat = $_GET['WUstat'];
} else {
  $datarecvd = false;
}

if ($datarecvd) {
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DBNAME);
		/* check connection */
		if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
		}
		$query  = sprintf("
INSERT INTO %s
(temp
,rawtemp
,adjfactor
,dewpt
,humidity
,moist
,dewpot
,presshpa
,pressinhg
,winddir
,windspd
,windlast
,windgust
,rainrate
,rainday
,time
,nextupl
,lastconn
,WUstat
)
VALUES
(%f
,%f
,%f
,%f
,%d
,%d
,%d
,%f
,%f
,%d
,%f
,%d
,%d
,%f
,%f
,'%s'
,'%s'
,%d
,%d
)", TABLENAME,
    $temp,
    $rawtemp,
    $adjfactor,
    $dewpt,
    $humidity,
    $moist,
    $dewpot,
    $presshpa,
    $pressinhg,
    $winddir,
    $windspd,
    $windlast,
    $windgust,
    $rainrate,
    $rainday,
    $time,
    $nextupl,
    $lastconn,
    $WUstat
    );
    
    $result = $mysqli->query($query);
    $mysqli->close();
}
?>
