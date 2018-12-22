<?php
namespace App\Service\Archive;

use App\Service\NBinary;

class Txd {

    private function parseHeader( NBinary &$binary ){

        return [
            'magic'             => $binary->consume(4,  NBinary::STRING),
            'constNumber'       => $binary->consume(4,  NBinary::INT_32),
            'fileSize'          => $binary->consume(4,  NBinary::INT_32),
            'indexTableOffset'  => $binary->consume(4,  NBinary::INT_32),
            'indexTableOffset2' => $binary->consume(4,  NBinary::INT_32),
            'numIndex'          => $binary->consume(4,  NBinary::INT_32),
            'zero1'             => $binary->consume(8,  NBinary::HEX),
            'numTextures'       => $binary->consume(4,  NBinary::INT_32),
            'firstOffset'       => $binary->consume(4,  NBinary::INT_32),
            'lastTOffset'       => $binary->consume(4,  NBinary::INT_32),
            'zero3'             => $binary->consume(4, NBinary::HEX),
        ];
    }

    private function getIndexTable( $offset, $count, NBinary &$binary ){
        $binary->jumpTo($offset);

        /**
         * Read the bottom table, start at offset + 4byte per entry
         */
        $indexTable = $binary->consume($offset + ($count * 4),  NBinary::HEX);

        $indexTable = new NBinary(hex2bin($indexTable));

        $results = [
            'pointerFirstTexture' => $indexTable->consume(4,  NBinary::INT_32),
            'pointerLastTexture' => $indexTable->consume(4,  NBinary::INT_32),
            'entries' => []
        ];

        for($i = 0; $i < $count; $i = $i + (4 * 4)){
            $entry = [
                'pointerNextTexture' => $indexTable->consume(4,  NBinary::INT_32),
                'pointerPrevTexture' => $indexTable->consume(4,  NBinary::INT_32),
                'pointerHeaderEnd' => $indexTable->consume(4,  NBinary::INT_32),
                'pointerHeaderStart' => $indexTable->consume(4,  NBinary::INT_32),
            ];

            $results['entries'][] = $entry;
        }

        return $results;
    }

    private function parseTexture( $startOffset, $indexTableOffset, NBinary &$binary ){

        $binary->jumpTo($startOffset);

        $texture = [
            'nextOffset'        => $binary->consume(4,  NBinary::INT_32),
            'prevOffset'        => $binary->consume(4,  NBinary::INT_32),
            'name'              => $binary->consume(64, NBinary::STRING),
            'width'             => $binary->consume(4,  NBinary::INT_32),
            'height'            => $binary->consume(4,  NBinary::INT_32),
            'bitPerPixel'       => $binary->consume(4,  NBinary::INT_32),
            'pitchOrLinearSize' => $binary->consume(4,  NBinary::INT_32),
            'maybeDxtVersion'   => $binary->consume(4,  NBinary::HEX),
            'unkown'            => $binary->consume(4,  NBinary::HEX),
            'headerEndOffset'   => $binary->consume(4,  NBinary::INT_32),
            'headerStartOffset' => $binary->consume(4,  NBinary::INT_32),
            'unkown2'           => $binary->consume(4,  NBinary::HEX),
            'unkown3'           => $binary->consume(4,  NBinary::HEX)
        ];

        // 112 == any already read bytes
        $currentPos = $startOffset + 112 + ($texture['headerEndOffset'] - $texture['headerStartOffset']) ;

        $binary->jumpTo($currentPos);

//        $texture['data'] = $binary->consume(8,  NBinary::HEX);
//var_dump($indexTableOffset - $currentPos);
//exit;

        if ($texture['nextOffset'] > $texture['prevOffset']){

            $texture['data'] = $binary->consume($texture['nextOffset'] - $currentPos,  NBinary::BINARY);
        }else{
//            var_dump($indexTableOffset - $currentPos);
//            exit;
            $texture['data'] = $binary->consume($indexTableOffset - $currentPos,  NBinary::BINARY);

        }


        return $texture;
    }

    public function unpack($binary){

        $jumper = new NBinary($binary);
        $binary = new NBinary($binary);

        $header = $this->parseHeader($binary);

//        $tableData = $this->getIndexTable($header['indexTableOffset'], $header['numIndex'], $jumper);
//
//        foreach ($tableData['entries'] as $entry) {
//            $jumper->jumpTo($entry['pointerHeaderStart']);
//            $headerStartOffset = $jumper->consume(4, NBinary::INT_32);
//
//            $jumper->jumpTo($entry['pointerHeaderEnd']);
//            $headerEndOffset = $jumper->consume(4, NBinary::INT_32);
//
//            $header = $jumper->range($headerStartOffset, $headerEndOffset);
//
//            $jumper->jumpTo($entry['pointerNextTexture']);
//            $pointerNextTextureOffset = $jumper->consume(4, NBinary::INT_32);
//
//
//            var_dump($pointerNextTextureOffset);
//            exit;
//        }

        $currentOffset = $header['firstOffset'];

        $textures = [];
        while($header['numTextures'] > 0) {
            $texture = $this->parseTexture($currentOffset, $header['indexTableOffset'], $binary);
            $textures[] = $texture;

            $currentOffset = $texture['nextOffset'];

            $header['numTextures']--;
        }


        return $textures;
    }

    public function pack( $executions, $envExecutions, $paddings ){

        die("no packing support right now");

    }
}