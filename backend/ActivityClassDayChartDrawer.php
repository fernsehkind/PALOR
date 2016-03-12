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

namespace Palor;

require_once('PbFactory.php');
require_once('PbHelper.php');
require_once('Settings.php');
require_once('ChartDrawer.php');

class ActivityClassDayChartDrawer extends ChartDrawer {

    private $_templateColumn = 'CanvasJS.addColorSet("$$$divName$$$Color", ["$$$colors$$$"]);

var var$$$divName$$$ = new CanvasJS.Chart("$$$divName$$$", {
    title:{ text: "$$$title$$$", fontSize: 24 },
    colorSet: "$$$divName$$$Color",
    legend: { 
        verticalAlign: "top",
        horizontalAlign: "center",
        fontSize: 12
    },
    axisX:{
        title: "Time",
        titleFontSize: 14,
        labelFontSize: 12,
        valueFormatString: "H:mm",
        labelFormatter: function (e) {
                return CanvasJS.formatDate( e.value, "H:mm");
            },
        labelAngle: 0,
        interval: 1,
        intervalType: "hour",
    },
    axisY:{
        title: "",
        titleFontSize: 14,
        labelFontSize: 12,
        minimum: 0,
        maximum: 1,
        interval: 1,
    },
    toolTip:{
        contentFormatter: function ( e ) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                content += CanvasJS.formatDate(e.entries[i].dataPoint.x, "H:mm");
            }
            return content;
            alert(e.entries[0].dataPoint);
            return CanvasJS.formatDate(e.value, "H:mm");
        }  
    },
    data: [ {
        type: "stepArea",
        showInLegend: true,
        xValueType: "dateTime",
        legendText: "$$$legendText0$$$",
        dataPoints: [$$$dataPoints0$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        xValueType: "dateTime",
        legendText: "$$$legendText1$$$",
        dataPoints: [$$$dataPoints1$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        xValueType: "dateTime",
        legendText: "$$$legendText2$$$",
        dataPoints: [$$$dataPoints2$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        legendText: "$$$legendText3$$$",
        dataPoints: [$$$dataPoints3$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        legendText: "$$$legendText4$$$",
        dataPoints: [$$$dataPoints4$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        legendText: "$$$legendText5$$$",
        dataPoints: [$$$dataPoints5$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        legendText: "$$$legendText6$$$",
        dataPoints: [$$$dataPoints6$$$]
    }, {
        type: "stepArea",
        showInLegend: true,
        legendText: "$$$legendText7$$$",
        dataPoints: [$$$dataPoints7$$$]
    }]
});
var$$$divName$$$.render();
';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_EIGHT_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateStepAreaChartByDay($actSample,
        $divName, $title = 'Activity class times', $legendText = array('Non wear', 'Sleep', 'Sedentary', 'Light activity', 'Continuous moderate', 'Intermittent moderate', 'Continuous vigorous', 'Intermittent vigorous')) {

        $dataSet = $this->_generateDayCourse($actSample);

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], 8, $divName,
            $title, $legendText, false);
    }

    private function _generateDayCourse($actSample) {
        $x = array();
        $y = array();

        $activityInfo = $actSample['activity_info'];

        $valueMapping = array(
            8 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4,
            5 => 5, 6 => 6, 7 => 7
        );

        for ($i = 0; $i < count($activityInfo); $i++) {
            $currentMapping = $valueMapping[$activityInfo[$i]['value']];

            $x[$i] = sprintf('new Date(2000,1,1,%d,%d,0,0)', 
                $activityInfo[$i]['time_stamp']['time']['hour'],
                $activityInfo[$i]['time_stamp']['time']['minute']);

            for ($j = 0; $j < count($valueMapping); $j++) {
                if ($j == $currentMapping) {
                    $y[$i][$j] = 1;
                }
                else {
                    $y[$i][$j] = 0;
                }
            }
        }
        $x[$i] = 'new Date(2000,1,1,23,59,59,999)';
        for ($j = 0; $j < count($valueMapping); $j++) {
            if ($j == $currentMapping) {
                $y[$i][$j] = 1;
            }
            else {
                $y[$i][$j] = 0;
            }
        }

        return array('x' => $x, 'y' => $y);
    }
}
