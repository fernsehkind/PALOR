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

class ChartDrawer {

    protected $_colors;
    protected $_template;

    protected function setColors($colors) {
        $this->_colors = $colors;
    }

    protected function setTemplate($template) {
        $this->_template = $template;
    }

    protected function generateChart($x, $y,
        $divName, $title, $legendText) {

        $javascriptCode = $this->_template;

        $javascriptCode = str_replace('$$$divName$$$', $divName,
            $javascriptCode);

        $javascriptCode = str_replace('$$$title$$$', $title,
            $javascriptCode);

        $javascriptCode = str_replace('$$$colors$$$',
            implode('","', $this->_colors), $javascriptCode);

        $javascriptCode = str_replace('$$$dateFormat$$$',
            Settings::DATE_FORMAT_JS, $javascriptCode);

        $javascriptCode = str_replace('$$$timeFormat$$$',
            Settings::TIME_FORMAT_JS, $javascriptCode);

        $javascriptCode = str_replace('$$$hourFormat$$$',
            Settings::HOUR_FORMAT_JS, $javascriptCode);

        foreach($x as $xIdx=>$value) {
            $legendTextSearch = self::_createSearchWord('legendText', $xIdx);
            $javascriptCode = str_replace($legendTextSearch,
                $legendText[$xIdx], $javascriptCode);

            $dataPointSearch = self::_createSearchWord('dataPoints', $xIdx);
            $javascriptCode = str_replace($dataPointSearch,
                $this->_generateDataPoints($x[$xIdx], $y[$xIdx]),
                $javascriptCode);
        }

        return $javascriptCode;
    }

    private function _generateDataPoints($x, $y) {

        $dataPointsStr = '';

        foreach($x as $xIdx=>$value) {
            if (is_string($x[$xIdx])) {
                $dataPointsStr .= sprintf('{ x: %s, y: %f},',
                    $x[$xIdx], $y[$xIdx]);
            }
            else {
                $dataPointsStr .= sprintf('{ x: %d, y: %f, label: "%s" },',
                    $xIdx, $y[$xIdx], $x[$xIdx]);
            }
        }
        return $dataPointsStr;
    }

    private function _createSearchWord($search, $idx) {
        return sprintf('$$$%s%d$$$', $search, $idx);
    }
}
