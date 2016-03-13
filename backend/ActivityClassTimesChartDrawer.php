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
    title:{ text: "$$$title$$$", fontSize: 24 },
    colorSet: "$$$divName$$$Color",
    legend: {
        verticalAlign: "top",
        horizontalAlign: "center",
        fontSize: 12
    },
    axisX:{
        title: "Date",
        titleFontSize: 14,
        labelFontSize: 12,
        valueFormatString: "$$$dateFormat$$$",
        labelFormatter: function (e) {
            return CanvasJS.formatDate( e.value, "$$$dateFormat$$$");
        },
    },
    axisY:{
        title: "Duration in h",
        titleFontSize: 14,
        labelFontSize: 12,
        maximum: 24,
        interval: 1,
    },
    toolTip:{
        contentFormatter: function ( e ) {
            var content = " ";
            for (var i = 0; i < e.entries.length; i++) {
                content += "Date: ";
                content += CanvasJS.formatDate(e.entries[i].dataPoint.x, "$$$dateFormat$$$");
                content += "<br/>";
                content += e.entries[i].dataSeries.name + ": ";
                content += e.entries[i].dataPoint.y;
                content += " h";
            }
            return content;
        }
    },
    data: [ $$$dataSeries$$$ ]
});
var$$$divName$$$.render();
';

    private $_templateSeries = '{
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText$$$index$$$$$$",
        yValueFormatString: "0.0 h",
        dataPoints: [$$$dataPoints$$$index$$$$$$],
        name: "$$$legendText$$$index$$$$$$",
        click: function(e){
            var link = "$$$link$$$&day=" + e.dataPoint.x.getDate();
            link += "&month=" + (e.dataPoint.x.getMonth() + 1);
            link += "&year=" + e.dataPoint.x.getFullYear();
            window.open(link, "_self");
        },
    },';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_EIGHT_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateColumnChartByDay($dailySummaries,
        $divName, $link, $title = 'Activity class times', $legendText = array('Non wear', 'Sleep', 'Sedentary', 'Light activity', 'Continuous moderate', 'Intermittent moderate', 'Continuous vigorous', 'Intermittent vigorous')) {

        $dataSet = $this->_generateTimesByDate($dailySummaries);

        parent::setTemplate(str_replace('$$$dataSeries$$$',
            $this->_prepareDataSeriesTemplate($dataSet),
            $this->_templateColumn));

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], $divName, $title, $legendText,
            $link);
    }

    private function _prepareDataSeriesTemplate($dataSet) {
        $dataSeries = '';

        foreach ($dataSet['x'] as $xIdx=>$value) {
            $dataSeries .= str_replace('$$$index$$$',
                $xIdx, $this->_templateSeries);
        }

        return $dataSeries;
    }

    private function _generateTimesByDate($dailySummaries) {
        $x = array();
        $y = array();

        for ($i = 0; $i < count($dailySummaries); $i++) {
            if ($dailySummaries[$i] === NULL) {
                continue;
            }

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
                $x[$j][$i] = sprintf
                    ('new Date(%d,%d,%d)',
                    $dailySummaries[$i]['date']['year'],
                    $dailySummaries[$i]['date']['month'] - 1,
                    $dailySummaries[$i]['date']['day']);

                $y[$j][$i] = round(PbHelper::toHoursPbDuration(
                    $dailySummaries[$i]['activity_class_times'][$timeStr[$j]]), 1);
            }
        }

        return array('x' => $x, 'y' => $y);
    }
}
