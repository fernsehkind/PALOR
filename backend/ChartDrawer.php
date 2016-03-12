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

    protected function generateChart($x, $y, $yDimCount,
        $divName, $title, $legendText, $xIsText = true) {

        $javascriptCode = $this->_template;

        $javascriptCode = str_replace('$$$divName$$$', $divName,
            $javascriptCode);

        $javascriptCode = str_replace('$$$title$$$', $title,
            $javascriptCode);

        $javascriptCode = str_replace('$$$colors$$$',
            implode('","', $this->_colors), $javascriptCode);

        $legendTextSearch = $this->_createSearchWord('legendText',
            $yDimCount);
        for ($i = 0; $i < $yDimCount; $i++) {
            if ($yDimCount > 1) {
                $javascriptCode = str_replace($legendTextSearch[$i],
                    $legendText[$i], $javascriptCode);
            }
            else {
                $javascriptCode = str_replace($legendTextSearch[$i],
                    $legendText, $javascriptCode);
            }
        }

        $dataPointSearch = $this->_createSearchWord('dataPoints',
            $yDimCount);
        for ($i = 0; $i < $yDimCount; $i++) {
            $javascriptCode = str_replace($dataPointSearch[$i],
                $this->_generateDataPoints($x, $y, $i, $yDimCount > 1, $xIsText),
                $javascriptCode);
        }

        return $javascriptCode;
    }

    private function _generateDataPoints($x, $y, $yDim,
        $multidimensional, $xIsText) {

        $dataPointsStr = '';

        if (!$multidimensional) {
            for ($i = 0; $i < count($x); $i++) {
                if ($xIsText) {
                    $dataPointsStr .= sprintf('{ label: "%s", y: %f },',
                        $x[$i], $y[$i]);
                }
                else {
                    $dataPointsStr .= sprintf('{ x: %s, y: %f },',
                        $x[$i], $y[$i]);
                }
            }
        }
        else {
            for ($i = 0; $i < count($x); $i++) {
                if ($xIsText) {
                    $dataPointsStr .= sprintf('{ label: "%s", y: %f },',
                        $x[$i], $y[$i][$yDim]);
                }
                else {
                    $dataPointsStr .= sprintf('{ x: %s, y: %f },',
                        $x[$i], $y[$i][$yDim]);
                }
            }
        }

        return $dataPointsStr;
    }

    private function _createSearchWord($search, $searchCount) {
        if ($searchCount > 1) {
            $ret = array();
            for ($i = 0; $i < $searchCount; $i++) {
                $ret[$i] = sprintf('$$$%s%d$$$', $search, $i);
            }
            return $ret;
        }
        else {
            return array(sprintf('$$$%s$$$', $search));
        }
    }
}
