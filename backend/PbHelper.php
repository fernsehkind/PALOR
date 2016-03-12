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

require_once('Settings.php');

class PbHelper {

    const FUNC_HAS = 'has';
    const FUNC_GET = 'get';
    const FUNC_COUNT = 'Count';
    const FUNC_ARRAY = 'Array';

    const NOT_EXISTING_VALUE = '-';

    public static function getPbActivityClassTimes($pbObj) {
        $array = array();

        $varNames = array(
            'time_non_wear',
            'time_sleep',
            'time_sedentary',
            'time_light_activity',
            'time_continuous_moderate',
            'time_intermittent_moderate',
            'time_continuous_vigorous',
            'time_intermittent_vigorous'
        );

        foreach ($varNames as $varName) {
            $array[$varName] = self::getPbDuration(self::getVar($pbObj,
                self::varNameToCamelCase($varName)));
        }

        return $array;
    }

    public static function getPbActivityGoalSummary($pbObj) {
        $array = self::createArrayFromPb($pbObj,
            array('activity_goal', 'achieved_activity'));

        $varNames = array('time_to_go_up', 'time_to_go_walk', 'time_to_go_jog');
        foreach ($varNames as $varName) {
            $array[$varName] = self::getPbDuration(self::getVar($pbObj,
                self::varNameToCamelCase($varName)));
        }

        return $array;
    }

    public static function getPbDailySummaries($pbObjArray) {
        $array = array();

        for($i = 0; $i < count($pbObjArray); $i++) {
            if ($pbObjArray[$i] != NULL) {
                $array[$i] = self::getPbDailySummary($pbObjArray[$i]);
            }
            else {
                $array[$i] = NULL;
            }
        }
        return $array;
    }

    public static function getPbDailySummary($pbObj) {
        $array = self::createArrayFromPb($pbObj,
            array(
                'steps',
                'activity_calories',
                'training_calories',
                'bmr_calories',
                'activity_distance'
            ));

        $array['date'] = self::getPbDate(self::getVar($pbObj,
            self::varNameToCamelCase('date')));
        $array['activity_goal_summary'] = self::getPbActivityGoalSummary(
            self::getVar($pbObj,
                self::varNameToCamelCase('activity_goal_summary')));
        $array['activity_class_times'] = self::getPbActivityClassTimes(
            self::getVar($pbObj,
                self::varNameToCamelCase('activity_class_times')));

        return $array;
    }

    public static function getPbDate($pbObj) {
        return self::createArrayFromPb($pbObj, array('year', 'month', 'day'));
    }

    public static function getPbDeviceInfo($pbObj) {
        $array = self::createArrayFromPb($pbObj,
            array(
                'svn_rev',
                'electrical_serial_number',
                'deviceID',
                'model_name',
                'hardware_code',
                'product_color',
                'product_design',
                'system_id'
            ));

        $array['bootloader_version'] = self::getPbVersion(self::getVar($pbObj,
            self::varNameToCamelCase('bootloader_version')));
        $array['platform_version'] = self::getPbVersion(self::getVar($pbObj,
            self::varNameToCamelCase('platform_version')));
        $array['device_version'] = self::getPbVersion(self::getVar($pbObj,
            self::varNameToCamelCase('device_version')));

        return $array;
    }

    public static function getPbDuration($pbObj) {
        return self::createArrayFromPb($pbObj,
            array(
                'hours',
                'minutes',
                'seconds',
                'millis'
            ));
    }

    public static function getPbSyncInfo($pbObj) {
        $array = self::createArrayFromPb($pbObj, array('full_sync_required'));
        $array['last_modified'] = self::getPbSystemDateTime(
            self::getVar($pbObj,
                self::varNameToCamelCase('last_modified')));
        $array['last_synchronized'] = self::getPbSystemDateTime(
            self::getVar($pbObj,
                self::varNameToCamelCase('last_synchronized')));
        $array['changed_path'] = self::getArray($pbObj,
            self::varNameToCamelCase('changed_path'));
        return $array;
    }

    public static function getPbSystemDateTime($pbObj) {
        if ($pbObj === NULL) return NULL;

        $array = self::createArrayFromPb($pbObj, array('trusted'));

        $array['date'] = self::getPbDate(self::getVar($pbObj,
            self::varNameToCamelCase('date')));
        $array['time'] = self::getPbTime(self::getVar($pbObj,
            self::varNameToCamelCase('time')));

        return $array;
    }

    public static function getPbTime($pbObj) {
        return self::createArrayFromPb($pbObj,
            array(
                'hour',
                'minute',
                'seconds',
                'millis'
            ));
    }

    public static function getPbUserDatabase($pbObj) {
        return self::getVar($pbObj,
            self::varNameToCamelCase('current_user_index'));
    }

    public static function getPbVersion($pbObj) {
        return self::createArrayFromPb($pbObj,
            array('major', 'minor', 'patch', 'specifier'));
    }

    public static function toStepsStatsPbDailySummaries($items) {
        return self::getStats($items, 'steps');
    }

    public static function toStringPbDate($item) {
        $timestamp = self::toTimestampPbDate($item);
        if ($timestamp === NULL) {
            return self::NOT_EXISTING_VALUE;
        }
        else {
            return date(Settings::DATE_FORMAT, $timestamp);
        }
    }
    public static function toStringPbSystemDateTime($item) {
        $timestamp = self::toTimestampPbSystemDateTime($item);
        if ($timestamp === NULL) {
            return self::NOT_EXISTING_VALUE;
        }
        else {
            return date(Settings::DATE_AND_TIME_FORMAT,
                $timestamp);
        }
    }

    public static function toStringPbVersion($item) {
        $result = sprintf("%s.%s.%s",
            $item['major'],
            $item['minor'],
            $item['patch']);

        if ($item['specifier'] != NULL) {
            $result = $result . '(' . $specifier . ')';
        }

        return $result;
    }

    public static function toStringBoolYesNo($item) {
        return self::toStringBool($item, 'Yes', 'No');
    }

    public static function toTimestampPbDate($item) {
        if ($item === NULL) {
            return NULL;
        }

        return mktime(0,0,0, $item['month'], $item['day'], $item['year']);
    }

    public static function toTimestampPbSystemDateTime($item) {
        if ($item === NULL) {
            return NULL;
        }

        return mktime(
            $item['time']['hour'],
            $item['time']['minute'],
            $item['time']['seconds'],
            $item['date']['month'],
            $item['date']['day'],
            $item['date']['year']);
    }

    public static function toStringBool($item, $trueValue, $falseValue) {
        if ($item === NULL) {
            return self::NOT_EXISTING_VALUE;
        }
        else {
            if ($item) {
                return $trueValue;
            }
            else {
                return $falseValue;
            }
        }
    }

    public static function toMinutesPbDuration($item) {
        $minutes = 0.;

        $minutes += $item['hours'] * 60;
        $minutes += $item['minutes'];
        $minutes += $item['seconds'] / 60.;

        return $minutes;
    }

    public static function toHoursPbDuration($item) {
        $hours = 0.;

        $hours += $item['hours'];
        $hours += $item['minutes'] / 60.;
        $hours += $item['seconds'] / 3600.;

        return $hours;
    }

    private static function createArrayFromPb($pbObj, $varNames) {
        $array = array();
        foreach ($varNames as $varName) {
            $array[$varName] = self::getVar($pbObj,
                self::varNameToCamelCase($varName));
        }
        return $array;
    }

    private static function varNameToCamelCase($def) {
        $def = ucfirst($def);

        $upperCaseNeeded = False;
        for ($i = 0; $i < strlen($def); $i++) {
            if ($def[$i] == '_') {
                $upperCaseNeeded = True;
            }
            else {
                if ($upperCaseNeeded) {
                    $def[$i] = strtoupper($def[$i]);
                }
                $upperCaseNeeded = False;
            }
        }

        return str_replace('_', '', $def);
    }

    private static function getVar($pbObj, $varName, $notExistValue = null) {
        $methodHas = array($pbObj, self::FUNC_HAS . $varName);
        $methodGet = array($pbObj, self::FUNC_GET . $varName);
        if (!method_exists($pbObj, $methodHas[1])) {

            return call_user_func($methodGet);
        }
        else {
            if (call_user_func($methodHas)) {
                return call_user_func($methodGet);
            }
            else {
                return $notExistValue;
            }
        }
    }

    private static function getArray($pbObj, $function) {
        $method_count =
            array($pbObj, self::FUNC_GET . $function . self::FUNC_COUNT);
        $method_array =
            array($pbObj, self::FUNC_GET . $function . self::FUNC_ARRAY);

        if (call_user_func($method_count) == 0) {
            return array();
        }
        else {
            return call_user_func($method_array);
        }
    }

    private static function getStats($items, $fieldName) {
        $min = NULL;
        $max = NULL;
        $sum = 0;
        $count = 0;

        foreach ($items as $item) {
            $current = $item[$fieldName];
            if ($current === NULL) {
                continue;
            }
            $count += 1;

            $sum += $current;
            if (($min === NULL) || ($min > $current)) {
                $min = $current;
            }
            if (($max === NULL) || ($max < $current)) {
                $max = $current;
            }
        }

        if ($count == 0) {
            return NULL;
        }
        else {
            $ret = array();
            $ret['min'] = $min;
            $ret['max'] = $max;
            $ret['avg'] = $sum / $count;
            return $ret;
        }
    }
}

?>
