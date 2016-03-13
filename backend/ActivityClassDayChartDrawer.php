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
    zoomEnabled: true,
    legend: {
        verticalAlign: "top",
        horizontalAlign: "center",
        fontSize: 12
    },
    axisX:{
        title: "Time",
        titleFontSize: 14,
        labelFontSize: 12,
        valueFormatString: "$$$timeFormat$$$",
        labelFormatter: function (e) {
            return CanvasJS.formatDate( e.value, "$$$timeFormat$$$");
        },
        labelAngle: 0,
        interval: 1,
        intervalType: "hour",
    },
    axisY:{
        title: "",
        titleFontSize: 14,
        labelFontSize: 1,
        margin: 20,
        minimum: 0,
        maximum: 1,
        interval: 1,
    },
    toolTip:{
        contentFormatter: function ( e ) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                if (e.entries[i].dataPoint.y == 1) {
                    content += "Start: ";
                }
                else {
                    content += "Stop: ";
                }
                content += CanvasJS.formatDate(e.entries[i].dataPoint.x, "$$$timeFormat$$$");
                content += "<br/>";
                content += e.entries[i].dataSeries.name;
            }
            return content;
        }
    },
    data: [ $$$dataSeries$$$ ]
});
var$$$divName$$$.render();
';

    private $_templateSeries = '{
        type: "stepArea",
        showInLegend: true,
        lineThickness: 0,
        xValueType: "dateTime",
        legendText: "$$$legendText$$$index$$$$$$",
        dataPoints: [$$$dataPoints$$$index$$$$$$],
        name: "$$$legendText$$$index$$$$$$"
    },';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_EIGHT_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateStepAreaChartByDay($actSample,
        $divName, $title = 'Activity class times', $legendText = array('Non wear', 'Sleep', 'Sedentary', 'Light activity', 'Continuous moderate', 'Intermittent moderate', 'Continuous vigorous', 'Intermittent vigorous')) {

        $dataSet = $this->_generateDayCourse($actSample);
        ksort($dataSet['x']);
        ksort($dataSet['y']);

        parent::setTemplate(str_replace('$$$dataSeries$$$',
            $this->_prepareDataSeriesTemplate($dataSet), $this->_templateColumn));

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], $divName,
            $title, $legendText);
    }

    private function _prepareDataSeriesTemplate($dataSet) {
        $dataSeries = '';

        foreach ($dataSet['x'] as $xIdx=>$value) {
            $dataSeries .= str_replace('$$$index$$$',
                $xIdx, $this->_templateSeries);
        }

        return $dataSeries;
    }

    private function _generateDayCourse($actSample) {
        $x = array();
        $y = array();

        $activityInfo = $actSample['activity_info'];

        $valueMapping = array(
            8 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4,
            5 => 5, 6 => 6, 7 => 7
        );

        $xIdxCountArray = array(0, 0, 0, 0, 0, 0, 0, 0);

        $lastMapping = NULL;

        for ($actIdx = 0; $actIdx < count($activityInfo); $actIdx++) {
            $currentMapping = $valueMapping[$activityInfo[$actIdx]['value']];
            $x[$currentMapping][$xIdxCountArray[$currentMapping]] = sprintf
                ('new Date(2000,1,1,%d,%d,0,0)',
                $activityInfo[$actIdx]['time_stamp']['time']['hour'],
                $activityInfo[$actIdx]['time_stamp']['time']['minute']);

            $y[$currentMapping][$xIdxCountArray[$currentMapping]] = 1;

            if ($lastMapping !== NULL) {
                $x[$lastMapping][$xIdxCountArray[$lastMapping]] = sprintf
                    ('new Date(2000,1,1,%d,%d,0,0)',
                    $activityInfo[$actIdx]['time_stamp']['time']['hour'],
                    $activityInfo[$actIdx]['time_stamp']['time']['minute']);

                $y[$lastMapping][$xIdxCountArray[$lastMapping]] = 0;

                $xIdxCountArray[$lastMapping] += 1;
            }

            $xIdxCountArray[$currentMapping] += 1;

            $lastMapping = $currentMapping;
        }

        if (count($activityInfo) > 0) {
            if ($lastMapping !== NULL) {
                $x[$lastMapping][$xIdxCountArray[$lastMapping]] = 'new Date(2000,1,1,23,59,59,999)';
                $y[$lastMapping][$xIdxCountArray[$lastMapping]] = 0;
            }
        }

        foreach($xIdxCountArray as $xIdxCount) {
            if ($xIdxCount == 0) {
                $x[$xIdxCount][0] = 'new Date(2000,1,1,23,59,59,999)';
                $y[$xIdxCount][0] = 0;
            }
        }

        return array('x' => $x, 'y' => $y);
    }
}
