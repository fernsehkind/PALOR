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

require_once('FileSystem.php');
require_once('PbHelper.php');

require_once('pb/protocolbuffers.inc.php');
require_once('pb/types.proto.php');
require_once('pb/dailysummary.proto.php');
require_once('pb/device.proto.php');
require_once('pb/syncinfo.proto.php');
require_once('pb/user_database.proto.php');
require_once('pb/user_id.proto.php');

class PbFactory {

    const PATH_DEVICE_ALL = '/DEVICE.BPB';
    const PATH_DEVICE = '%s/%s/DEVICE.BPB';
    const PATH_SYNC_INFO = '%s/%s/SYNCINFO.BPB';
    const PATH_USER_DATABASE = '%s/%s/U/UDB.BPB';
    const PATH_USER_ID = '%s/%s/U/%d/USERID.BPB';
    const PATH_DAILY_SUMMARY = '%s/%s/U/%d/%s/DSUM/DSUM.BPB';

    public static $_fileNameAssignments;

    public static function getAllDevices($dataPath) {
        $dirs = FileSystem::getDeviceFolders($dataPath);
        $devices = array();
        foreach ($dirs as $dir) {
            $device = self::parse($dir . self::PATH_DEVICE_ALL);
            if ($device !== NULL) {
                $devices[] = $device;
            }
        }
        return $devices;
    }

    public static function getDevice($dataPath, $id) {
        $path = sprintf(self::PATH_DEVICE, $dataPath, $id);
        return self::parse($path);
    }

    public static function getSyncInfo($dataPath, $id) {
        $path = sprintf(self::PATH_SYNC_INFO, $dataPath, $id);
        return self::parse($path);
    }

    public static function getUserDatabase($dataPath, $id) {
        $path = sprintf(self::PATH_USER_DATABASE, $dataPath, $id);
        return self::parse($path);
    }

    public static function getDailySummaryOfDate($dataPath, $id, $userId,
        $date) {
        $path = sprintf(self::PATH_DAILY_SUMMARY, $dataPath,
            $id, $userId, $date->format('Ymd'));
        return self::parse($path);
    }

    public static function getDailySummaries($dataPath, $id, $userId,
        $startDate, $endDate) {

        //$startDate->setTime(0, 0);
        //$endDate->setTime(0, 0);

        $currentDate = $startDate;
        $summaries = array();

        while ($currentDate <= $endDate) {
            $summary = self::getDailySummaryOfDate($dataPath, $id, $userId,
                $currentDate);
            if ($summary !== NULL) {
                $summaries[] = $summary;
            }
            else {
                $summaries[] = NULL;
            }
            $currentDate->add(new \DateInterval('P1D'));
        }

        return $summaries;
    }

    public static function getDailySummariesLast31Days($dataPath,
        $id, $userId) {
        $endDate = new \DateTime();
        $startDate = clone $endDate;
        $startDate->sub(new \DateInterval('P31D'));
        return self::getDailySummaries($dataPath, $id, $userId,
            $startDate, $endDate);
    }

    public static function getDailySummariesMonth($dataPath, $id, $userId,
        $year, $month) {
        $startDate = new \DateTime();
        $startDate->setDate($year, $month, 1);
        $endDate = new \DateTime();
        $endDate->setDate($year, $month, cal_days_in_month(
            CAL_GREGORIAN, $month, $year));
        return self::getDailySummaries($dataPath, $id, $userId,
            $startDate, $endDate);
    }

    public static function parse($file) {
        try {
            $basename = strtolower(basename($file));
            foreach (self::$_fileNameAssignments as $fileNameAssignment) {
                if ($basename == $fileNameAssignment['fileName']) {
                    return call_user_func(
                        array('\Palor\PbFactory', '_parseObj'),
                        $file, $fileNameAssignment['class']);
                }
            }
        }
        catch (\Exception $e) {
            return NULL;
        }

        return NULL;
    }

    private static function _parseObj($fs, $class) {
        $obj = new $class();
        $obj->read(self::_getFileStream($fs));
        return $obj;
    }

    private static function _getFileStream($fs) {
        if (!FileSystem::fileExists($fs)) {
            throw new \Exception('File does not exist.');
        }
        return fopen($fs, 'r');
    }
}

PbFactory::$_fileNameAssignments = array(
    array(
        'fileName' => 'device.bpb',
        'class' => '\data\PbDeviceInfo'
    ),
    array(
        'fileName' => 'syncinfo.bpb',
        'class' => '\data\PbSyncInfo'
    ),
    array(
        'fileName' => 'udb.bpb',
        'class' => '\data\PbUserDb'
    ),
    array(
        'fileName' => 'userid.bpb',
        'class' => '\data\PbUserIdentifier'
    ),
    array(
        'fileName' => 'dsum.bpb',
        'class' => '\data\PbDailySummary'
    ),
);

?>
