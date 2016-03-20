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

class StepsDayChartDrawer extends ChartDrawer {

    private $_templateChart = 'CanvasJS.addColorSet("$$$divName$$$Color", ["$$$colors$$$"]);

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
        labelFontSize: 12,
        margin: 20,
    },
    toolTip:{
        contentFormatter: function ( e ) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                content += CanvasJS.formatDate(e.entries[i].dataPoint.x, "$$$timeFormat$$$");
                content += "<br/>";
                content += e.entries[i].dataSeries.name;
                content += ": ";
                content += e.entries[i].dataPoint.y;
            }
            return content;
        }
    },
    data: [ 
        $$$dataSeries0$$$
    ]
});
var$$$divName$$$.render();
';

    private $_templateSeriesSteps = '{
        type: "stepArea",
        showInLegend: true,
        lineThickness: 2,
        xValueType: "dateTime",
        legendText: "$$$legendText$$$index$$$$$$",
        dataPoints: [$$$dataPoints$$$index$$$$$$],
        name: "$$$legendText$$$index$$$$$$",
    },';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_ONE_DIM);
        parent::setTemplate($this->_templateChart);
    }

    public function generateLineChartByDay($actSample,
        $divName, $title = 'Steps', $legendText = array('Steps')) {

        $dataSet = $this->_generateSteps($actSample);
        ksort($dataSet['x']);
        ksort($dataSet['y']);

        $template = str_replace('$$$dataSeries0$$$',
            $this->_prepareDataSeriesTemplate(
                $dataSet,
                $this->_templateSeriesSteps,
                0),
            $this->_templateChart);

        parent::setTemplate($template);

        return parent::generateChart($dataSet['x'], $dataSet['y'],
            $divName, $title, $legendText, '');
    }

    private function _prepareDataSeriesTemplate($dataSet, $template, $startIndex = 0) {
        $dataSeries = '';

        for ($i = 0; $i < count($dataSet['x']); $i++) {
            $dataSeries .= str_replace('$$$index$$$',
                $i + $startIndex, $template);
        }

        return $dataSeries;
    }

    private function _generateSteps($actSample) {
        $x = array();
        $y = array();

        $endTime = new \DateTime();
        $endTime->setDate($actSample['start_time']['date']['year'],
            $actSample['start_time']['date']['month'],
            $actSample['start_time']['date']['day']);
        $endTime->setTime(23, 59, 59);    

        $time = new \DateTime();
        $time->setDate($actSample['start_time']['date']['year'],
            $actSample['start_time']['date']['month'],
            $actSample['start_time']['date']['day']);
        $time->setTime($actSample['start_time']['time']['hour'],
            $actSample['start_time']['time']['minute'],
            $actSample['start_time']['time']['seconds']);

        $stepsInterval = $actSample['steps_recording_interval'];
        $stepsIntervalSeconds = $stepsInterval['hours'] * 3600
            + $stepsInterval['minutes'] * 60
            + $stepsInterval['seconds'];
        $dateIntervalStepsInSeconds = new \DateInterval(sprintf('PT%dS', $stepsIntervalSeconds));

        $steps = $actSample['steps_samples'];
        $stepCount = 0;

        for ($stepIdx = 0; $stepIdx < count($steps); $stepIdx++) {
            $stepCount += $steps[$stepIdx];

            $x[0][$stepIdx] = sprintf('new Date(%d,%d,%d,%d,%d,%d,0)',
                $time->format('Y'), $time->format('n'),
                $time->format('j'), $time->format('G'),
                $time->format('i'), $time->format('s'));
            $y[0][$stepIdx] = $stepCount;

            $time->add($dateIntervalStepsInSeconds);
        }

        if ($time < $endTime) {
            $x[0][$stepIdx] = sprintf('new Date(%d,%d,%d,%d,%d,%d,0)',
                $endTime->format('Y'), $endTime->format('n'),
                $endTime->format('j'), $endTime->format('G'),
                $endTime->format('i'), $endTime->format('s'));
            $y[0][$stepIdx] = $stepCount;
        }

        return array('x' => $x, 'y' => $y);
    }
}
