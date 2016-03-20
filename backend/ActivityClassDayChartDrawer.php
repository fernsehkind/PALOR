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

    const ACTIVITY_STOP_VALUE = 0;
    const ACTIVITY_START_VALUE = 1;
    const INACTIVITY_VALUE = 0.75;

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
                else if (e.entries[i].dataPoint.y == 0) {
                    content += "Stop: ";
                }
                content += CanvasJS.formatDate(e.entries[i].dataPoint.x, "$$$timeFormat$$$");
                content += "<br/>";
                content += e.entries[i].dataSeries.name;
            }
            return content;
        }
    },
    data: [ 
        $$$dataSeries0$$$
        $$$dataSeries1$$$
    ]
});
var$$$divName$$$.render();
';

    private $_templateSeriesActivity = '{
        type: "stepArea",
        showInLegend: true,
        lineThickness: 0,
        xValueType: "dateTime",
        legendText: "$$$legendText$$$index$$$$$$",
        dataPoints: [$$$dataPoints$$$index$$$$$$],
        name: "$$$legendText$$$index$$$$$$",
    },';

    private $_templateSeriesInactivity = '{
        type: "scatter",
        showInLegend: true,
        lineThickness: 0,
        xValueType: "dateTime",
        markerColor: "#FF0000",
        markerSize: 20,
        markerType: "triangle",
        legendText: "$$$legendText$$$index$$$$$$",
        dataPoints: [$$$dataPoints$$$index$$$$$$],
        name: "$$$legendText$$$index$$$$$$"
    },';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_EIGHT_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateStepAreaChartByDay($actSample,
        $divName, $link, $title = 'Activity class times', $legendText = array('Non wear', 'Sleep', 'Sedentary', 'Light activity', 'Continuous moderate', 'Intermittent moderate', 'Continuous vigorous', 'Intermittent vigorous', 'Incactivity trigger')) {

        $dataSetActivity = $this->_generateDayCourseActivity($actSample);
        ksort($dataSetActivity['x']);
        ksort($dataSetActivity['y']);

        $template = str_replace('$$$dataSeries0$$$',
            $this->_prepareDataSeriesTemplate(
                $dataSetActivity,
                $this->_templateSeriesActivity,
                0),
            $this->_templateColumn);

        $dataSetInactivity = $this->_generateDayCourseInactivity($actSample);
        ksort($dataSetActivity['x']);

        if ($dataSetInactivity !== NULL) {
            $template = str_replace('$$$dataSeries1$$$',
                $this->_prepareDataSeriesTemplate(
                    $dataSetInactivity,
                    $this->_templateSeriesInactivity,
                    count($dataSetActivity['x'])), $template);
        }
        else {
            $template = str_replace('$$$dataSeries1$$$', '', $template);
        }

        parent::setTemplate($template);

        $dataSetX = array_merge($dataSetActivity['x'], $dataSetInactivity['x']);
        $dataSetY = array_merge($dataSetActivity['y'], $dataSetInactivity['y']);

        return parent::generateChart($dataSetX, $dataSetY,
            $divName, $title, $legendText, $link);
    }

    private function _prepareDataSeriesTemplate($dataSet, $template, $startIndex = 0) {
        $dataSeries = '';

        for ($i = 0; $i < count($dataSet['x']); $i++) {
            $dataSeries .= str_replace('$$$index$$$',
                $i + $startIndex, $template);
        }

        return $dataSeries;
    }

    private function _generateDayCourseActivity($actSample) {
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

            $y[$currentMapping][$xIdxCountArray[$currentMapping]] = self::ACTIVITY_START_VALUE;

            if ($lastMapping !== NULL) {
                $x[$lastMapping][$xIdxCountArray[$lastMapping]] = sprintf
                    ('new Date(2000,1,1,%d,%d,0,0)',
                    $activityInfo[$actIdx]['time_stamp']['time']['hour'],
                    $activityInfo[$actIdx]['time_stamp']['time']['minute']);

                $y[$lastMapping][$xIdxCountArray[$lastMapping]] = self::ACTIVITY_STOP_VALUE;

                $xIdxCountArray[$lastMapping] += 1;
            }

            $xIdxCountArray[$currentMapping] += 1;

            $lastMapping = $currentMapping;
        }

        if (count($activityInfo) > 0) {
            if ($lastMapping !== NULL) {
                $x[$lastMapping][$xIdxCountArray[$lastMapping]] = 'new Date(2000,1,1,23,59,59,999)';
                $y[$lastMapping][$xIdxCountArray[$lastMapping]] = self::ACTIVITY_STOP_VALUE;
            }
        }

        foreach($xIdxCountArray as $xIdx=>$count) {
            if ($count == 0) {
                $x[$xIdx][0] = 'new Date(2000,1,1,23,59,59,999)';
                $y[$xIdx][0] = self::ACTIVITY_STOP_VALUE;
            }
        }

        return array('x' => $x, 'y' => $y);
    }

    private function _generateDayCourseInactivity($actSample) {
        $x = array();
        $y = array();

        $inactivityInfo = $actSample['inactivity_trigger'];

        if (count($inactivityInfo) == 0) {
            return NULL;
        }

        for ($inactIdx = 0; $inactIdx < count($inactivityInfo); $inactIdx++) {
            $x[0][$inactIdx] = sprintf
                ('new Date(2000,1,1,%d,%d,0,0)',
                $inactivityInfo[$inactIdx]['time_stamp']['time']['hour'],
                $inactivityInfo[$inactIdx]['time_stamp']['time']['minute']);

            $y[0][$inactIdx] = self::INACTIVITY_VALUE;
        }

        return array('x' => $x, 'y' => $y);

    }

}
