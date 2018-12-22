<?php
namespace App\Service;

use App\Service\Archive\ZLib;

class NBinary{
    const INT_8 = 'INT_8';
    const INT_16 = 'INT_16';
    const U_INT_8 = 'U_INT_8';
    const LITTLE_U_INT_16 = 'LITTLE_U_INT_16';
    const LITTLE_U_INT_32 = 'LITTLE_U_INT_32';
//    const LITTLE_INT_32 = 'LITTLE_INT_32';
    const INT_32 = 'INT_32';
    const FLOAT_32 = 'FLOAT_32';
    const STRING = 'STRING';
    const HEX = 'RAW';
    const BINARY = 'BINARY';

    public $hex = "";
    protected $_hex = "";

    public function __construct( $binary = null ){
        if (is_null($binary)) return;

        $this->hex = bin2hex($binary);

        if (substr($this->hex, 0, 8) === "5a32484d"){
            $this->hex = bin2hex(ZLib::uncompress( $binary ));
        }

        $this->_hex = $this->hex;
    }

    public function length(){
        return strlen($this->hex) / 2;
    }

    public function getAsArray(){
        return str_split($this->hex, 2);
    }

    public function jumpTo( $offset ){
        $this->hex = $this->_hex;
        $this->hex = substr($this->hex, $offset * 2);
    }

    public function range( $fromOffset, $toOffset ){
        $this->hex = $this->_hex;
        return substr($this->hex, $fromOffset * 2, ($toOffset - $fromOffset) * 2);
    }

    public function write($bytes, $type){

        switch ($type){
            case self::LITTLE_U_INT_16:
                $this->hex .= bin2hex(pack("v", $bytes));
                break;
            case self::LITTLE_U_INT_32:
                $this->hex .= bin2hex(pack("V", $bytes));
                break;
//            case self::LITTLE_INT_32:
//                var_dump(bin2hex(pack("V", $bytes)));
//                exit;
//                $this->hex .= bin2hex(pack("l", $bytes));
//
//                break;

            case self::BINARY:
                $this->hex .= bin2hex($bytes);
                break;


        }

    }

    public function consume( $bytes, $type){
        $result = substr($this->hex, 0, $bytes * 2);
        $this->hex = substr($this->hex, $bytes * 2);

        switch ($type){
            case self::INT_8:
                return current(unpack("c", hex2bin($result)));
                break;
            case self::INT_16:
                return current(unpack("s", hex2bin($result)));
                break;
            case self::U_INT_8:
                return current(unpack("C", hex2bin($result)));
                break;
            case self::LITTLE_U_INT_16:
                return current(unpack("v", hex2bin($result)));
                break;
            case self::LITTLE_U_INT_32:
                return current(unpack("V", hex2bin($result)));
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