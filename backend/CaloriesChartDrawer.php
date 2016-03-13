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

class CaloriesChartDrawer extends ChartDrawer {

    private $_templateColumn = 'CanvasJS.addColorSet("$$$divName$$$Color", ["$$$colors$$$"]);

var var$$$divName$$$ = new CanvasJS.Chart("$$$divName$$$", {
    title:{ text: "$$$title$$$" },
    colorSet: "$$$divName$$$Color",
    legend: {
        verticalAlign: "top",
        horizontalAlign: "center",
        fontSize: 12
    },
    axisX: {
        title: "Date",
        titleFontSize: 14,
        labelFontSize: 12,
        valueFormatString: "$$$dateFormat$$$",
        labelFormatter: function (e) {
            return CanvasJS.formatDate( e.value, "$$$dateFormat$$$");
        },
        intervalType: "day"
    },
    axisY: {
        title: "$$$legendText0$$$",
        titleFontSize: 14,
        labelFontSize: 12,
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
            }
            return content;
        }
    },
    data: [ {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText0$$$",
        dataPoints: [$$$dataPoints0$$$],
        name: "$$$legendText0$$$"
    }, {
        type: "stackedColumn",
        showInLegend: true,
        legendText: "$$$legendText1$$$",
        dataPoints: [$$$dataPoints1$$$],
        name: "$$$legendText1$$$"
    }]
});
var$$$divName$$$.render();
';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_TWO_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateColumnChartByDay($dailySummaries,
        $divName, $title = 'Calories', $legendText =
        array('BMR calories', 'Activity calories')) {

        $dataSet = $this->_generateCaloriesByDate($dailySummaries);

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], $divName,
            $title, $legendText);
    }

    private function _generateCaloriesByDate($dailySummaries) {
        $x = array();
        $y = array();

        for ($i = 0; $i < count($dailySummaries); $i++) {
            if ($dailySummaries[$i] === NULL) {
                continue;
            }
            $x[0][$i] = sprintf('new Date(%d, %d, %d)',
                $dailySummaries[$i]['date']['year'],
                $dailySummaries[$i]['date']['month'] - 1,
                $dailySummaries[$i]['date']['day']);
            $x[1][$i] = $x[0][$i];

            $y[0][$i] = $dailySummaries[$i]['bmr_calories'];
            $y[1][$i] = $dailySummaries[$i]['activity_calories'];
        }

        return array('x' => $x, 'y' => $y);
    }
}
