<?php
// Generated by https://github.com/bramp/protoc-gen-php// Please include protocolbuffers before this file, for example:
//   require('protocolbuffers.inc.php');
//   require('device.proto.php');

namespace data {

  use Protobuf;
  use ProtobufEnum;
  use ProtobufMessage;

  // message data.PbVersion
  final class PbVersion extends ProtobufMessage {

    private $_unknown;
    private $major = null; // required uint32 major = 1
    private $minor = null; // required uint32 minor = 2
    private $patch = null; // required uint32 patch = 3
    private $specifier = null; // optional string specifier = 4

    public function __construct($in = null, &$limit = PHP_INT_MAX) {
      parent::__construct($in, $limit);
    }

    public function read($fp, &$limit = PHP_INT_MAX) {
      $fp = \ProtobufIO::toStream($fp, $limit);
      while(!feof($fp) && $limit > 0) {
        $tag = Protobuf::read_varint($fp, $limit);
        if ($tag === false) break;
        $wire  = $tag & 0x07;
        $field = $tag >> 3;
        switch($field) {
          case 1: // required uint32 major = 1
            if($wire !== 0) {
              throw new \Exception("Incorrect wire format for field $field, expected: 0 got: $wire");
            }
            $tmp = Protobuf::read_varint($fp, $limit);
            if ($tmp === false) throw new \Exception('Protobuf::read_varint returned false');
            if ($tmp < Protobuf::MIN_UINT32 || $tmp > Protobuf::MAX_UINT32) throw new \Exception('uint32 out of range');$this->major = $tmp;

            break;
          case 2: // required uint32 minor = 2
            if($wire !== 0) {
              throw new \Exception("Incorrect wire format for field $field, expected: 0 got: $wire");
            }
            $tmp = Protobuf::read_varint($fp, $limit);
            if ($tmp === false) throw new \Exception('Protobuf::read_varint returned false');
            if ($tmp < Protobuf::MIN_UINT32 || $tmp > Protobuf::MAX_UINT32) throw new \Exception('uint32 out of range');$this->minor = $tmp;

            break;
          case 3: // required uint32 patch = 3
            if($wire !== 0) {
              throw new \Exception("Incorrect wire format for field $field, expected: 0 got: $wire");
            }
            $tmp = Protobuf::read_varint($fp, $limit);
            if ($tmp === false) throw new \Exception('Protobuf::read_varint returned false');
            if ($tmp < Protobuf::MIN_UINT32 || $tmp > Protobuf::MAX_UINT32) throw new \Exception('uint32 out of range');$this->patch = $tmp;

            break;
          case 4: // optional string specifier = 4
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->specifier = $tmp;

            break;
          default:
            $limit -= Protobuf::skip_field($fp, $wire);
        }
      }
      if (!$this->validate()) throw new \Exception('Required fields are missing');
    }

    public function write($fp) {
      if (!$this->validate())
        throw new \Exception('Required fields are missing');
      if (!is_null($this->major)) {
        fwrite($fp, "\x08", 1);
        Protobuf::write_varint($fp, $this->major);
      }
      if (!is_null($this->minor)) {
        fwrite($fp, "\x10", 1);
        Protobuf::write_varint($fp, $this->minor);
      }
      if (!is_null($this->patch)) {
        fwrite($fp, "\x18", 1);
        Protobuf::write_varint($fp, $this->patch);
      }
      if (!is_null($this->specifier)) {
        fwrite($fp, "\"", 1);
        Protobuf::write_varint($fp, strlen($this->specifier));
        fwrite($fp, $this->specifier);
      }
    }

    public function size() {
      $size = 0;
      if (!is_null($this->major)) {
        $size += 1 + Protobuf::size_varint($this->major);
      }
      if (!is_null($this->minor)) {
        $size += 1 + Protobuf::size_varint($this->minor);
      }
      if (!is_null($this->patch)) {
        $size += 1 + Protobuf::size_varint($this->patch);
      }
      if (!is_null($this->specifier)) {
        $l = strlen($this->specifier);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      return $size;
    }

    public function validate() {
      if ($this->major === null) return false;
      if ($this->minor === null) return false;
      if ($this->patch === null) return false;
      return true;
    }

    public function clearMajor() { $this->major = null; }
    public function hasMajor() { return $this->major !== null; }
    public function getMajor() { if($this->major !== null) return $this->major; else return 0;}
    public function setMajor($value) { $this->major = $value; }

    public function clearMinor() { $this->minor = null; }
    public function hasMinor() { return $this->minor !== null; }
    public function getMinor() { if($this->minor !== null) return $this->minor; else return 0;}
    public function setMinor($value) { $this->minor = $value; }

    public function clearPatch() { $this->patch = null; }
    public function hasPatch() { return $this->patch !== null; }
    public function getPatch() { if($this->patch !== null) return $this->patch; else return 0;}
    public function setPatch($value) { $this->patch = $value; }

    public function clearSpecifier() { $this->specifier = null; }
    public function hasSpecifier() { return $this->specifier !== null; }
    public function getSpecifier() { if($this->specifier !== null) return $this->specifier; else return "";}
    public function setSpecifier($value) { $this->specifier = $value; }

    public function __toString() {
      return ''
           . Protobuf::toString('major', $this->major, 0)
           . Protobuf::toString('minor', $this->minor, 0)
           . Protobuf::toString('patch', $this->patch, 0)
           . Protobuf::toString('specifier', $this->specifier, "");
    }

    // @@protoc_insertion_point(class_scope:data.PbVersion)
  }

  // message data.PbDeviceInfo
  final class PbDeviceInfo extends ProtobufMessage {

    private $_unknown;
    private $bootloaderVersion = null; // optional .data.PbVersion bootloader_version = 1
    private $platformVersion = null; // optional .data.PbVersion platform_version = 2
    private $deviceVersion = null; // optional .data.PbVersion device_version = 3
    private $svnRev = null; // optional uint32 svn_rev = 4
    private $electricalSerialNumber = null; // optional string electrical_serial_number = 5
    private $deviceID = null; // optional string deviceID = 6
    private $modelName = null; // optional string model_name = 7
    private $hardwareCode = null; // optional string hardware_code = 8
    private $productColor = null; // optional string product_color = 9
    private $productDesign = null; // optional string product_design = 10
    private $systemId = null; // optional string system_id = 11

    public function __construct($in = null, &$limit = PHP_INT_MAX) {
      parent::__construct($in, $limit);
    }

    public function read($fp, &$limit = PHP_INT_MAX) {
      $fp = \ProtobufIO::toStream($fp, $limit);
      while(!feof($fp) && $limit > 0) {
        $tag = Protobuf::read_varint($fp, $limit);
        if ($tag === false) break;
        $wire  = $tag & 0x07;
        $field = $tag >> 3;
        switch($field) {
          case 1: // optional .data.PbVersion bootloader_version = 1
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $limit -= $len;
            $this->bootloaderVersion = new \data\PbVersion($fp, $len);
            if ($len !== 0) throw new \Exception('new \data\PbVersion did not read the full length');

            break;
          case 2: // optional .data.PbVersion platform_version = 2
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $limit -= $len;
            $this->platformVersion = new \data\PbVersion($fp, $len);
            if ($len !== 0) throw new \Exception('new \data\PbVersion did not read the full length');

            break;
          case 3: // optional .data.PbVersion device_version = 3
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $limit -= $len;
            $this->deviceVersion = new \data\PbVersion($fp, $len);
            if ($len !== 0) throw new \Exception('new \data\PbVersion did not read the full length');

            break;
          case 4: // optional uint32 svn_rev = 4
            if($wire !== 0) {
              throw new \Exception("Incorrect wire format for field $field, expected: 0 got: $wire");
            }
            $tmp = Protobuf::read_varint($fp, $limit);
            if ($tmp === false) throw new \Exception('Protobuf::read_varint returned false');
            if ($tmp < Protobuf::MIN_UINT32 || $tmp > Protobuf::MAX_UINT32) throw new \Exception('uint32 out of range');$this->svnRev = $tmp;

            break;
          case 5: // optional string electrical_serial_number = 5
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->electricalSerialNumber = $tmp;

            break;
          case 6: // optional string deviceID = 6
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->deviceID = $tmp;

            break;
          case 7: // optional string model_name = 7
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->modelName = $tmp;

            break;
          case 8: // optional string hardware_code = 8
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->hardwareCode = $tmp;

            break;
          case 9: // optional string product_color = 9
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->productColor = $tmp;

            break;
          case 10: // optional string product_design = 10
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->productDesign = $tmp;

            break;
          case 11: // optional string system_id = 11
            if($wire !== 2) {
              throw new \Exception("Incorrect wire format for field $field, expected: 2 got: $wire");
            }
            $len = Protobuf::read_varint($fp, $limit);
            if ($len === false) throw new \Exception('Protobuf::read_varint returned false');
            $tmp = Protobuf::read_bytes($fp, $len, $limit);
            if ($tmp === false) throw new \Exception("read_bytes($len) returned false");
            $this->systemId = $tmp;

            break;
          default:
            $limit -= Protobuf::skip_field($fp, $wire);
        }
      }
      if (!$this->validate()) throw new \Exception('Required fields are missing');
    }

    public function write($fp) {
      if (!$this->validate())
        throw new \Exception('Required fields are missing');
      if (!is_null($this->bootloaderVersion)) {
        fwrite($fp, "\x0a", 1);
        Protobuf::write_varint($fp, $this->bootloaderVersion->size());
        $this->bootloaderVersion->write($fp);
      }
      if (!is_null($this->platformVersion)) {
        fwrite($fp, "\x12", 1);
        Protobuf::write_varint($fp, $this->platformVersion->size());
        $this->platformVersion->write($fp);
      }
      if (!is_null($this->deviceVersion)) {
        fwrite($fp, "\x1a", 1);
        Protobuf::write_varint($fp, $this->deviceVersion->size());
        $this->deviceVersion->write($fp);
      }
      if (!is_null($this->svnRev)) {
        fwrite($fp, " ", 1);
        Protobuf::write_varint($fp, $this->svnRev);
      }
      if (!is_null($this->electricalSerialNumber)) {
        fwrite($fp, "*", 1);
        Protobuf::write_varint($fp, strlen($this->electricalSerialNumber));
        fwrite($fp, $this->electricalSerialNumber);
      }
      if (!is_null($this->deviceID)) {
        fwrite($fp, "2", 1);
        Protobuf::write_varint($fp, strlen($this->deviceID));
        fwrite($fp, $this->deviceID);
      }
      if (!is_null($this->modelName)) {
        fwrite($fp, ":", 1);
        Protobuf::write_varint($fp, strlen($this->modelName));
        fwrite($fp, $this->modelName);
      }
      if (!is_null($this->hardwareCode)) {
        fwrite($fp, "B", 1);
        Protobuf::write_varint($fp, strlen($this->hardwareCode));
        fwrite($fp, $this->hardwareCode);
      }
      if (!is_null($this->productColor)) {
        fwrite($fp, "J", 1);
        Protobuf::write_varint($fp, strlen($this->productColor));
        fwrite($fp, $this->productColor);
      }
      if (!is_null($this->productDesign)) {
        fwrite($fp, "R", 1);
        Protobuf::write_varint($fp, strlen($this->productDesign));
        fwrite($fp, $this->productDesign);
      }
      if (!is_null($this->systemId)) {
        fwrite($fp, "Z", 1);
        Protobuf::write_varint($fp, strlen($this->systemId));
        fwrite($fp, $this->systemId);
      }
    }

    public function size() {
      $size = 0;
      if (!is_null($this->bootloaderVersion)) {
        $l = $this->bootloaderVersion->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->platformVersion)) {
        $l = $this->platformVersion->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->deviceVersion)) {
        $l = $this->deviceVersion->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->svnRev)) {
        $size += 1 + Protobuf::size_varint($this->svnRev);
      }
      if (!is_null($this->electricalSerialNumber)) {
        $l = strlen($this->electricalSerialNumber);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->deviceID)) {
        $l = strlen($this->deviceID);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->modelName)) {
        $l = strlen($this->modelName);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->hardwareCode)) {
        $l = strlen($this->hardwareCode);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->productColor)) {
        $l = strlen($this->productColor);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->productDesign)) {
        $l = strlen($this->productDesign);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      if (!is_null($this->systemId)) {
        $l = strlen($this->systemId);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
      return $size;
    }

    public function validate() {
      return true;
    }

    public function clearBootloaderVersion() { $this->bootloaderVersion = null; }
    public function hasBootloaderVersion() { return $this->bootloaderVersion !== null; }
    public function getBootloaderVersion() { if($this->bootloaderVersion !== null) return $this->bootloaderVersion; else return null;}
    public function setBootloaderVersion(\data\PbVersion $value) { $this->bootloaderVersion = $value; }

    public function clearPlatformVersion() { $this->platformVersion = null; }
    public function hasPlatformVersion() { return $this->platformVersion !== null; }
    public function getPlatformVersion() { if($this->platformVersion !== null) return $this->platformVersion; else return null;}
    public function setPlatformVersion(\data\PbVersion $value) { $this->platformVersion = $value; }

    public function clearDeviceVersion() { $this->deviceVersion = null; }
    public function hasDeviceVersion() { return $this->deviceVersion !== null; }
    public function getDeviceVersion() { if($this->deviceVersion !== null) return $this->deviceVersion; else return null;}
    public function setDeviceVersion(\data\PbVersion $value) { $this->deviceVersion = $value; }

    public function clearSvnRev() { $this->svnRev = null; }
    public function hasSvnRev() { return $this->svnRev !== null; }
    public function getSvnRev() { if($this->svnRev !== null) return $this->svnRev; else return 0;}
    public function setSvnRev($value) { $this->svnRev = $value; }

    public function clearElectricalSerialNumber() { $this->electricalSerialNumber = null; }
    public function hasElectricalSerialNumber() { return $this->electricalSerialNumber !== null; }
    public function getElectricalSerialNumber() { if($this->electricalSerialNumber !== null) return $this->electricalSerialNumber; else return "";}
    public function setElectricalSerialNumber($value) { $this->electricalSerialNumber = $value; }

    public function clearDeviceID() { $this->deviceID = null; }
    public function hasDeviceID() { return $this->deviceID !== null; }
    public function getDeviceID() { if($this->deviceID !== null) return $this->deviceID; else return "";}
    public function setDeviceID($value) { $this->deviceID = $value; }

    public function clearModelName() { $this->modelName = null; }
    public function hasModelName() { return $this->modelName !== null; }
    public function getModelName() { if($this->modelName !== null) return $this->modelName; else return "";}
    public function setModelName($value) { $this->modelName = $value; }

    public function clearHardwareCode() { $this->hardwareCode = null; }
    public function hasHardwareCode() { return $this->hardwareCode !== null; }
    public function getHardwareCode() { if($this->hardwareCode !== null) return $this->hardwareCode; else return "";}
    public function setHardwareCode($value) { $this->hardwareCode = $value; }

    public function clearProductColor() { $this->productColor = null; }
    public function hasProductColor() { return $this->productColor !== null; }
    public function getProductColor() { if($this->productColor !== null) return $this->productColor; else return "";}
    public function setProductColor($value) { $this->productColor = $value; }

    public function clearProductDesign() { $this->productDesign = null; }
    public function hasProductDesign() { return $this->productDesign !== null; }
    public function getProductDesign() { if($this->productDesign !== null) return $this->productDesign; else return "";}
    public function setProductDesign($value) { $this->productDesign = $value; }

    public function clearSystemId() { $this->systemId = null; }
    public function hasSystemId() { return $this->systemId !== null; }
    public function getSystemId() { if($this->systemId !== null) return $this->systemId; else return "";}
    public function setSystemId($value) { $this->systemId = $value; }

    public function __toString() {
      return ''
           . Protobuf::toString('bootloader_version', $this->bootloaderVersion, null)
           . Protobuf::toString('platform_version', $this->platformVersion, null)
           . Protobuf::toString('device_version', $this->deviceVersion, null)
           . Protobuf::toString('svn_rev', $this->svnRev, 0)
           . Protobuf::toString('electrical_serial_number', $this->electricalSerialNumber, "")
           . Protobuf::toString('deviceID', $this->deviceID, "")
           . Protobuf::toString('model_name', $this->modelName, "")
           . Protobuf::toString('hardware_code', $this->hardwareCode, "")
           . Protobuf::toString('product_color', $this->productColor, "")
           . Protobuf::toString('product_design', $this->productDesign, "")
           . Protobuf::toString('system_id', $this->systemId, "");
    }

    // @@protoc_insertion_point(class_scope:data.PbDeviceInfo)
  }

}
