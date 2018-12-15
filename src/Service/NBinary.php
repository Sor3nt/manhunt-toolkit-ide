<?php
namespace App\Service;

use App\Service\Archive\ZLib;

class NBinary{
    const INT_8 = 'INT_8';
    const INT_16 = 'INT_16';
    const INT_32 = 'INT_32';
    const FLOAT_32 = 'FLOAT_32';
    const STRING = 'STRING';
    const HEX = 'RAW';
    const BINARY = 'BINARY';

    private $hex = "";
    protected $_hex;

    public function __construct( $binary ){
        $this->hex = bin2hex($binary);

        if (substr($this->hex, 0, 8) === "5a32484d"){
            $this->hex = bin2hex(ZLib::uncompress( $binary ));
        }

        $this->_hex = $this->hex;
    }

    public function jumpTo( $offset ){
        $this->hex = $this->_hex;
        $this->hex = substr($this->hex, $offset * 2);
    }

    public function consume( $bytes, $type){
        $result = substr($this->hex, 0, $bytes * 2);
        $this->hex = substr($this->hex, $bytes * 2);

        switch ($type){
            case self::INT_8:
                return is_int($result) ? pack("c", $result) :  current(unpack("c", hex2bin($result)));
                break;
            case self::INT_16:
                return is_int($result) ? pack("s", $result) : current(unpack("s", hex2bin($result)));
                break;
            case self::INT_32:
                return (int) current(unpack("L", hex2bin($result)));
                break;
            case self::FLOAT_32:
                return (float) current(unpack("f", hex2bin($result)));
                break;
            case self::STRING:

                $bin = hex2bin($result);
                if (mb_strpos($bin, "\x00") !== false){
                    return mb_substr($bin, 0, mb_strpos($bin, "\x00"));
                }else{
                    return trim(hex2bin($result));
                }

                break;
            case self::BINARY:
                return hex2bin($result);
                break;
            case self::HEX:
                return $result;
                break;
        }

        return $result;
    }
}