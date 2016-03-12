<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Ralph Haußmann
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the 'Software'), to
 * deal in the software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

?>

<?php

    require_once('../../backend/ActivityClassDayChartDrawer.php');
    require_once('../../backend/ActivityClassTimesChartDrawer.php');
    require_once('../../backend/CaloriesChartDrawer.php');
    require_once('../../backend/PbFactory.php');
    require_once('../../backend/PbHelper.php');
    require_once('../../backend/Settings.php');
    require_once('../../backend/StepsChartDrawer.php');

    if (isset($_GET['id'])) {
        $deviceId = $_GET['id'];
    }
    else {
        $deviceId = NULL;
    }

    $day = intval(date('d'));
    if (isset($_GET['day'])) {
        if (is_numeric($_GET['day']) && ($_GET['day'] > 0) && ($_GET['day'] < 32)) {
            $day = $_GET['day'];
        }
    }

    $month = intval(date('n'));
    if (isset($_GET['month'])) {
        if (is_numeric($_GET['month']) && ($_GET['month'] > 0) && ($_GET['month'] < 13)) {
            $month = $_GET['month'];
        }
    }
    $monthName = \DateTime::createFromFormat('!m', $month)->format('F');

    $year = intval(date('Y'));
    if (isset($_GET['year'])) {
        if (is_numeric($_GET['year']) && ($_GET['year'] > 2000) && ($_GET['year'] < 2200)) {
            $year = $_GET['year'];
        }
    }

    if (!checkdate($month, $day, $year)) {
        $day = intval(date('d'));
        $month = intval(date('n'));
        $year = intval(date('Y'));
    }

    $device = NULL;
    $syncInfo = NULL;
    $actSamples = NULL;
    if ($deviceId !== NULL) {
        $syncInfo = \Palor\PbHelper::getPbSyncInfo(
            \Palor\PbFactory::getSyncInfo(
                \Palor\Settings::DEVICE_DATA_PATH, $deviceId));
        $device = \Palor\PbHelper::getPbDeviceInfo(
            \Palor\PbFactory::getDevice(
                \Palor\Settings::DEVICE_DATA_PATH, $deviceId));

        $userId = \Palor\PbHelper::getPbUserDatabase(
            \Palor\PbFactory::getUserDatabase(
                \Palor\Settings::DEVICE_DATA_PATH, $deviceId));

        $pbActSamples = \Palor\PbFactory::getActSamples(
                \Palor\Settings::DEVICE_DATA_PATH,
                $deviceId, $userId, $year, $month, $day);
        if ($pbActSamples !== NULL) {
            $actSamples = \Palor\PbHelper::getPbActivitySamples(
                $pbActSamples);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>PALOR - Daily activity</title>

    <!-- Bootstrap -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-datepicker3.min.css" rel="stylesheet">

    <link href="../../css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">PALOR</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="../../index.php">Home</a></li>
                    <?php if ($device !== NULL): ?>
                    <li><a href="recent.php?id=<?php echo $deviceId; ?>">Device <?php echo $deviceId; ?></a></li>
                    <li><a href="monthly.php?id=<?php echo $deviceId; ?>">Monthly activity</a></li>
                    <li class="active"><a href="daily.php?id=<?php echo $deviceId; ?>">Daily activity</a></li>
                    <?php endif; ?>
                    <li><a href="../../about.php">About</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container">

        <div class="starter-template">
            <h1>Daily activity for device <?php echo $deviceId ?></h1>

            <div class="row">
                <div class="datepicker"></div>
            </div>
<?php if ($actSamples !== NULL): ?>
            <div class="row">
                <div class="col-md-12">
                    <div id="activityClassContainer" style="height:300px"></div>
                </div>
            </div>
<?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    No data found for the selected day.
                </div>
            </div>
<?php endif; ?>
        </div>


    </div><!-- /.container -->

    <script src="../../canvasjs/canvasjs.min.js"></script>
    <script type="text/javascript">

    window.onload = function () {

<?php
    if ($actSamples !== NULL) {
        $drawer = new \Palor\ActivityClassDayChartDrawer();
        $date = new \DateTime();
        $date->setDate($year, $month, $day);
        $date = $date->format(\Palor\Settings::DATE_FORMAT);
        echo $drawer->generateStepAreaChartByDay($actSamples,
            'activityClassContainer', sprintf('Course of day - %s', $date));
    }
?>

    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
    }).on('changeDate', function(e) {
        var currDay = new Date(e.date).getDate();
        var currMonth = new Date(e.date).getMonth() + 1;
        var currYear = String(e.date).split(" ")[3];
        window.open("daily.php?id=<?php echo $deviceId; ?>&day=" + currDay + "&month=" + currMonth + "&year=" + currYear, "_self");
    });

};
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/bootstrap-datepicker.min.js"></script>
  </body>
</html>




