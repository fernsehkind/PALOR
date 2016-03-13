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

    $device = NULL;
    $syncInfo = NULL;
    $dailySummaries = NULL;
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

        $dailySummaries = \Palor\PbHelper::getPbDailySummaries(
            \Palor\PbFactory::getDailySummariesLast31Days(
                \Palor\Settings::DEVICE_DATA_PATH, $deviceId,
                $userId));

        $entryFound = False;
        foreach ($dailySummaries as $dailySummary) {
            if ($dailySummary !== NULL) $entryFound = True;
        }

        if ($entryFound === False) {
            $dailySummaries = NULL;
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
    <title>PALOR - Recent activity</title>

    <!-- Bootstrap -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">

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
                    <li class="active"><a href="recent.php?id=<?php echo $deviceId; ?>">Device <?php echo $deviceId; ?></a></li>
                    <li><a href="monthly.php?id=<?php echo $deviceId; ?>">Monthly activity</a></li>
                    <li><a href="daily.php?id=<?php echo $deviceId; ?>">Daily activity</a></li>
                    <?php endif; ?>
                    <li><a href="../../about.php">About</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container">

        <div class="starter-template">
<?php if ($device !== NULL): ?>
            <h1>Recent activity for device <?php echo $deviceId; ?></h1>
            <div class="row">
                <?php if ($dailySummaries !== NULL): ?>
                <div class="col-md-6">
                    <div id="stepsContainer" style="height:300px"></div>
                </div>
                <div class="col-md-6">
                    <div id="caloriesContainer" style="height:300px"></div>
                </div>
                <?php else: ?>
                    No data found for the last 31 days.
                <?php endif; ?>
            </div>
            <div class="row">
                <h1>Synchronisation information</h1>
                <?php if ($syncInfo !== NULL): ?>
                <div class="device_box col-md-6 col-md-offset-3">
                    <div class="device_box_border">
                        <table class="table-striped">
                            <tr>
                                <td class="bold">Last modified:</td>
                                <td>
                                <?php echo
                                    \Palor\PbHelper::toStringPbSystemDateTime(
                                        $syncInfo['last_modified']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="bold">Last synchronized:</td>
                                <td>
                                <?php echo
                                    \Palor\PbHelper::toStringPbSystemDateTime(
                                        $syncInfo['last_synchronized']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="bold">Full sync required:</td>
                                <td><?php echo \Palor\PbHelper::toStringBoolYesNo($syncInfo['full_sync_required']); ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Changed path(s):</td>
                                <td>
                                    <?php
                                        $arr = $syncInfo['changed_path'];
                                        if (count($arr) == 0) {
                                            echo 'None';
                                        }
                                        else {
                                            foreach ($arr as $path) {
                                                echo $path . '<br/>';
                                            }
                                        }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <p class="error">Failed to get synchronisation info!</p>
            <?php endif; ?>
            <div class="row">
                <h1>Detailed device information</h1>
                <?php if ($device !== NULL): ?>
                <div class="device_box col-md-6">
                    <div class="device_box_border">
                        <h3>Hardware</h3>
                        <table class="table-striped">
                            <tr>
                                <td class="bold">Model name:</td>
                                <td><?php echo $device['model_name']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Device ID:</td>
                                <td><?php echo $device['deviceID']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Serial number:</td>
                                <td><?php echo $device['electrical_serial_number']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">System id:</td>
                                <td><?php echo $device['system_id']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Hardware code:</td>
                                <td><?php echo $device['hardware_code']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Color:</td>
                                <td><?php echo $device['product_color']; ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Product design:</td>
                                <td><?php echo $device['product_design']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="device_box col-md-6">
                    <div class="device_box_border">
                        <h3>Software</h3>
                        <table class="table-striped">
                            <tr>
                                <td class="bold">Bootloader version:</td>
                                <td><?php echo \Palor\PbHelper::toStringPbVersion($device['bootloader_version']); ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Platform version:</td>
                                <td><?php echo \Palor\PbHelper::toStringPbVersion($device['platform_version']); ?></td>
                            </tr>
                            <tr>
                                <td class="bold">Device version:</td>
                                <td><?php echo \Palor\PbHelper::toStringPbVersion($device['device_version']); ?></td>
                            </tr>
                            <tr>
                                <td class="bold">SVN revision:</td>
                                <td><?php echo $device['svn_rev']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <p class="error">Failed to get extended device information!</p>
                <?php endif; ?>
            </div>
        </div>
<?php else: ?>
            <h1>Device</h1>
            Given device id does not exist. Please select a device <a href="index.php">here</a>.
<?php endif; ?>

    </div><!-- /.container -->

    <script src="../../canvasjs/canvasjs.min.js"></script>
    <script type="text/javascript">

    window.onload = function () {

<?php
    $drawer = new \Palor\StepsChartDrawer();
    echo $drawer->generateColumnChartByDay($dailySummaries,
        'stepsContainer',
        sprintf('daily.php?id=%s', $deviceId),
        'Steps (last 31 days)');
?>

<?php
    $drawer = new \Palor\CaloriesChartDrawer();
    echo $drawer->generateColumnChartByDay($dailySummaries,
        'caloriesContainer',
        sprintf('daily.php?id=%s', $deviceId),
        'Calories (last 31 days)');
?>
    };
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../../js/bootstrap.min.js"></script>
  </body>
</html>




