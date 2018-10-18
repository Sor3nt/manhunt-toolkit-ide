<?php

namespace App\Service\Parser;



class Ini
{

    public static function clean( $content ){
        //remove any comments
        $content = preg_replace('/#(.*)/', '', $content);

        return $content;
    }

    public static function attributes( $content ){


        $content = self::clean($content);

        preg_match_all('/(\w+)\s(.*)/', $content, $matches);

        list(, $attributes, $values) = $matches;

        $result = [];
        foreach ($attributes as $index => $attribute) {
            $result[] = [ $attribute => trim($values[$index]) ];

        }

        return $result;
    }

}