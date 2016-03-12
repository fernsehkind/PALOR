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

class ActivityClassTimesChartDrawer extends ChartDrawer {

    private $_templateColumn = 'CanvasJS.addColorSet("$$$divName$$$Color", ["$$$colors$$$"]);

var var$$$divName$$$ = new CanvasJS.Chart("$$$divName$$$", {
    title:{ text: "$$$title$$$" },
    colorSet: "$$$divName$$$Color",
    legend: { verticalAlign: "top", horizontalAlign: "right"},
    axisY:{
        maximum: 24,
        interval: 4,
    },
    data: [ {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText0$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints0$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText1$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints1$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText2$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints2$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText3$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints3$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText4$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints4$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText5$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints5$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText6$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints6$$$]
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText7$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints7$$$]
    }]
});
var$$$divName$$$.render();
';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_EIGHT_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateColumnChartByDay($dailySummaries,
        $divName, $title = 'Activity class times', $legendText = array('Non wear', 'Sleep', 'Sedentary', 'Light activity', 'Continuous moderate', 'Intermittent moderate', 'Continuous vigorous', 'Intermittent vigorous')) {

        $dataSet = $this->_generateTimesByDate($dailySummaries);

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], 8, $divName,
            $title, $legendText);
    }

    private function _generateTimesByDate($dailySummaries) {
        $x = array();
        $y = array();

        for ($i = 0; $i < count($dailySummaries); $i++) {
            $x[$i] = PbHelper::toStringPbDate(
                $dailySummaries[$i]['date']);

            $timeStr = array(
                'time_non_wear',
                'time_sleep',
                'time_sedentary',
                'time_light_activity',
                'time_continuous_moderate',
                'time_intermittent_moderate',
                'time_continuous_vigorous',
                'time_intermittent_vigorous'
            );

            for ($j = 0; $j < count($timeStr); $j++) {
                $y[$i][$j] = round(PbHelper::toHoursPbDuration(
                    $dailySummaries[$i]['activity_class_times'][$timeStr[$j]]), 1);
            }
        }

        return array('x' => $x, 'y' => $y);
    }
}
