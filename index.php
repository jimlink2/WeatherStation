	<?php
		require "config.php";

    $moistureText = array(
        "Very low",
        "Low",
        "Moderate",
        "Elevated",
        "High",
        "Very high",
        "Extreme"
    );

    $dewText = array(
        "Are you kidding?",
        "Not even close",
        "Dew unlikely",
        "Dew possible",
        "Dew likely",
        "Dew or fog imminent"
    );
    
    $connText = array(
        "ONLINE",
        "DEGRADED",
        "OFFLINE"
    );

		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DBNAME);
		/* check connection */
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

		$query  = sprintf("SELECT * FROM %s ORDER BY id DESC LIMIT 1", TABLENAME);
		$result = $mysqli->query($query, MYSQLI_USE_RESULT);
		if ($result) {
			while ($row = $result->fetch_assoc()) {
			  $temp = $row['temp'];
			  $rawtemp = $row['rawtemp'];
			  $adjfactor = $row['adjfactor'];
			  $dewpt = $row['dewpt'];
			  $humidity = $row['humidity'];
			  $moist = $row['moist'];
			  $dewpot = $row['dewpot'];
			  $presshpa = $row['presshpa'];
			  $pressinhg = $row['pressinhg'];
			  $winddir = $row['winddir'];
			  $windspd = $row['windspd'];
			  $windlast = $row['windlast'];
			  $windgust = $row['windgust'];
        $rainrate = $row['rainrate'];
        $rainday = $row['rainday'];
        $raintips = $row['raintips'];
        $outuptime = $row['outuptime'];
        $time = $row['time'];
        $nextupl = $row['nextupl'];
			  $lastconn = $row['lastconn'];
			  $WUstat = $row['WUstat'];
		  }
		  
		  $moistDisp = $moistureText[$moist];
		  $dewDisp = $dewText[$dewpot];
		  $connDisp = $connText[$WUstat];
		  if ($WUstat == 0) {
		    $statColor = '090';
		  } else {
		    $statColor = '900';
		  }
		  $outDays = floor($outuptime / 86400);
      $outHours = floor(($outuptime % 86400) / 3600);
      $outMinutes = floor(($outuptime % 3600) / 60);
      $outSeconds = $outuptime % 60;

      $outUptimeDisp = '';

      if ($outDays > 0) {
          $outUptimeDisp .= $outDays . 'd ';
      }

      if ($outHours > 0 || $outDays > 0) {
          $outUptimeDisp .= $outHours . 'h ';
      }

      $outUptimeDisp .= $outMinutes . 'm ' . $outSeconds . 's';
		}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <meta http-equiv='refresh' content='30'>
  <title>Rockwall Weather</title>
  <style>
  body {
  font-family: Arial; background:#f0f0f0; padding:20px; }.card
  { background:white; padding:20px; margin-bottom:20px;
  border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.2);
  }.label { font-size:20px; color:#00C; font-weight:bold;
  }.noemph { font-size:20px; color:black; font-weight:normal;
  }.value { font-size:40px; font-weight:bold;
  }
  </style>
</head>
<body>
  <div class='card'>
    <div class='label'>
      Temperature
    </div>
    <div class='value'>
      <?php echo number_format($temp, 1); ?>°F &nbsp;&nbsp;&nbsp; 
      <span class='noemph'>Raw temp: <?php echo number_format($rawtemp, 1); ?>°F &nbsp;&nbsp;&nbsp; 
        Adj factor: <?php echo number_format($adjfactor, 3); ?></span>
    </div>
  </div>
  <div class='card'>
    <div class='label'>
      Dew Point
    </div>
    <div class='value'>
      <?php echo number_format($dewpt, 1); ?>
    </div>
    <div class='noemph'>
      Humidity: <?php echo $humidity ?> %
    </div>
    <div class='noemph'>
      Moisture content: <?php echo $moistDisp ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dew
      potential: <?php echo $dewDisp ?>
    </div>
  </div>
  <div class='card'>
    <div class='label'>
      Pressure
    </div>
    <div class='noemph'>
      <?php echo number_format($presshpa,1,'.',''); ?> hPa
    </div>
    <div class='value'>
      <?php echo number_format($pressinhg,2); ?> inHg
    </div>
  </div>
  <div class='card'>
    <div class='label'>
      Wind
    </div>
    <div class='value'>
      <?php echo $winddir ?>&deg; at <?php echo $windspd ?> mph
      &nbsp;&nbsp;&nbsp;<span class='noemph'>
        Last reported avg direction: <?php echo $windlast ?>&deg;</span>
    </div>
    <div class='noemph'>
      Gust <?php echo $windgust ?> mph
    </div>
  </div>
  <div class='card'>
      <div class='label'>
        Rain
      </div>

      <div class='value'>
        Rate: <?php echo number_format($rainrate,2); ?> in/hr
      </div>

      <div class='value'>
        Day: <?php echo number_format($rainday,2); ?> in
      </div>

      <div class='noemph'>
        Rain gauge tips: <?php echo (int)$raintips; ?>
      </div>
  </div>
  <div class='card'>
    <div class='label'>
      Outdoor Station
    </div>

    <div class='noemph'>
      C3 uptime: <?php echo $outUptimeDisp; ?>
    </div>

    <div class='noemph'>
      Uptime seconds: <?php echo (int)$outuptime; ?>
    </div>

    <div class='noemph'>
      Rain gauge tips: <?php echo (int)$raintips; ?>
    </div>
  </div>
  
  <div class='card'>
    <div class='label'>
      Time
    </div>
    <div class='value'>
      <?php echo $time ?>
    </div>
    <div style='font-size:20px;' class='value'>
      Next upload time: <?php echo $nextupl ?>
    </div>
  </div>
  <div class='card'>
    <div class='label'>
      Weather Underground
    </div>
    <div class='noemph'>
      Last successful connection: <?php echo $lastconn ?> minute(s) ago
    </div>
    <div style='color:#<?php echo $statColor ?>;font-size:20px;'>
      <?php echo $connDisp ?>
    </div>
  </div>
</body>
</html>
