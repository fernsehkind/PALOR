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

class Settings {
    const DEVICE_DATA_PATH = 'device_data';
    const DATE_AND_TIME_FORMAT = 'j.n.Y, G:i';
    const DATE_FORMAT = 'j.n.Y';
    const TIME_FORMAT = 'G:i';
    public static $CHART_COLOR_ONE_DIM = array('#6699FF', "#CC2222");
    public static $CHART_COLOR_TWO_DIM = array('#555555', '#22CC22');
    public static $CHART_COLOR_EIGHT_DIM = array('#222222', '#226622', '#CC2222', '#22CC22', '#FFA500', '#0022DD', '#800517', '#7D0552');
}

?>
