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

class StepsChartDrawer extends ChartDrawer {

    private $_templateColumn = 'CanvasJS.addColorSet("$$$divName$$$Color", ["$$$colors$$$"]);

var var$$$divName$$$ = new CanvasJS.Chart("$$$divName$$$", {
    title:{ text: "$$$title$$$" },
    colorSet: "$$$divName$$$Color",
    legend: { verticalAlign: "top", horizontalAlign: "right"},
    data: [ {
        type: "column",
        showInLegend: true,
        legendText: "$$$legendText$$$",
        dataPoints: [$$$dataPoints$$$]
    }]
});
var$$$divName$$$.render();
';

    public function __construct() {
        parent::setColors(Settings::$CHART_COLOR_ONE_DIM);
        parent::setTemplate($this->_templateColumn);
    }

    public function generateColumnChartByDay($dailySummaries,
        $divName, $title = 'Steps', $legendText = 'Steps') {

        $dataSet = $this->_generateStepsByDate($dailySummaries);

        return parent::generateChart($dataSet['x'],
            $dataSet['y'], 1, $divName,
            $title, $legendText);
    }

    private function _generateStepsByDate($dailySummaries) {
        $x = array();
        $y = array();

        for ($i = 0; $i < count($dailySummaries); $i++) {
            $x[$i] = PbHelper::toStringPbDate(
                $dailySummaries[$i]['date']);
            $y[$i] = $dailySummaries[$i]['steps'];
        }

        return array('x' => $x, 'y' => $y);
    }
}
