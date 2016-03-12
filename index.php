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

    require_once('backend/PbFactory.php');
    require_once('backend/PbHelper.php');

    $pbDevices = \Palor\PbFactory::getAllDevices(
        \Palor\Settings::DEVICE_DATA_PATH);
    $devices = array();
    foreach ($pbDevices as $pbDevice) {
        $devices[] = \Palor\PbHelper::getPbDeviceInfo($pbDevice);
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>PALOR</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
    <link href="css/loading.css" rel="stylesheet">

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
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container">

        <div class="starter-template">
        <div id="loading" class=""></div>
        <h1>Devices</h1>
            <div class="row">
                <button id="sync_button" type="button" class="btn btn-danger active">Synchronize all connected devices.</button>
                <div id="connect_log" class="hidden">
                    <p><b>Synchronize log:</b></p>
                    <div id="connected_devices"></div>
                    <p>The page will reload in 5 seconds.</p>
                </div>
            </div>
            <div class="row">
            <?php if (count($devices) > 0): ?>
            <p>Please select the device you are interested in:</p>
            <?php
                $countDevices = count($devices);
                for ($i = 0; $i < $countDevices; $i += 2) {
                    $class = "col-md-6";
                    $col = 1;
                    if (($i + 1) < $countDevices) {
                        $class = "col-md-6";
                        $col = 2;
                    }
            ?>
        <?php for ($j = 0; $j < $col; $j++): ?>
            <?php $modelType = \Palor\PbHelper::toModelType($devices[$i + $j]); ?>

                <div class="device_box <?php echo $class; ?>">
                    <div class="device_box_border">
                        <h3>
                            <?php if ($modelType !== NULL): ?>
                            <a href="device/<?php echo $modelType; ?>/recent.php?id=<?php echo $devices[$i + $j]['deviceID'] ?>"><?php echo $devices[$i + $j]['deviceID'] ?>
                            </a>
                            <?php else: ?>
                            <?php echo $devices[$i + $j]['deviceID'] ?> - Not supported    
                            <?php endif; ?>
                        </h3>
                        <table class="table-striped">
                        <tr>
                            <td class="bold">Model name:</td>
                            <td><?php echo $devices[$i + $j]['model_name'] ?></td>
                        </tr>
                        <tr>
                            <td class="bold">Color:</td>
                            <td><?php echo $devices[$i + $j]['product_color'] ?></td>
                        </tr>
                        <tr>
                            <td class="bold">Design:</td>
                            <td><?php echo $devices[$i + $j]['product_design'] ?></td>
                        </tr>
                        <tr>
                            <td class="bold">Hardware code:</td>
                            <td><?php echo $devices[$i + $j]['hardware_code'] ?></td>
                        </tr>
                        </table>
                    </div>
                </div>
        <?php endfor; ?>
        <?php } ?>
        <?php else: ?>
            We could not find any device information. Please connect your activity tracker and use the sync button above.
        <?php endif; ?>
            </div>
        </div>
        <div id="connected_devices"></div>

    </div><!-- /.container -->

    <script>
        window.onload = function () {
            $('#sync_button').on('click', function (e) {
                $('#loading').addClass("loading");
                $('#connected_devices').load('./python/dump_devices.py', function() {
                    $('#loading').removeClass("loading");
                    $('#connect_log').removeClass("hidden");
                    $('#reload').removeClass('hidden');
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 5000);
                });
            });
        };
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>




