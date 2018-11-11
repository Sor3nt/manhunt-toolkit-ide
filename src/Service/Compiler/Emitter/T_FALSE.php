<?php
namespace App\Service\Compiler\Emitter;


use App\Service\Helper;

class T_FALSE {

    static public function map( $node, \Closure $getLine, \Closure $emitter, $data ){
        return [
            $getLine('12000000'),
            $getLine('01000000'),
            $getLine(Helper::fromIntToHex( 0 ))
        ];

    }

}