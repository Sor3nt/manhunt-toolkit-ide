<?php

namespace App\Service\Element;

use App\Service\Compiler\Compiler;
use App\Service\Compiler\Token;
use App\Service\Compiler\Tokenizer;
use App\Service\Resources;

/**
 * Class Level
 * @package App\Service
 *
 * Handler for the level script files (mls)
 */

class LevelScript
{

    /** @var Resources  */
    private $resources;


    /**
     * LevelScript constructor.
     * @param Resources $resources
     */
    public function __construct( Resources $resources ) {
        $this->resources = $resources;
    }

    /**
     * @param $level
     * @return array
     */
    public function getScriptList( $level )
    {
        $levelFile = sprintf('/levels/%s/%s.mls', $level, $level);

        $resource = $this->resources->load($levelFile);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        $results = [];
        foreach ($levelScripts as $index => $mhsc) {
            $results[] = $this->getEntityName($mhsc['DBUG']['SRCE']);
        }

        sort($results);

        return $results;
    }

    /**
     * @param $level
     * @param $script
     * @return bool
     */
    public function getScript($level, $script){
        $levelFile = sprintf('/levels/%s/%s.mls', $level, $level);

        $resource = $this->resources->load($levelFile);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        foreach ($levelScripts as $index => $mhsc) {

            $entityName = $this->getEntityName($mhsc['DBUG']['SRCE']);

            if ($entityName == $script) return $mhsc['DBUG']['SRCE'];
        }

        return false;
    }


    /**
     * @param $source
     * @return string
     */
    private function getEntityName( $source ){

        $source = Compiler::prepare($source);

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->run($source);

        $name = "";
        foreach ($tokens as $index => $token) {
            if ($token['type'] == Token::T_DEFINE_SECTION_ENTITY){

                $current = $index + 1;

                while($tokens[$current]['type'] != Token::T_DEFINE){
                    $name .= $tokens[$current]['value'];
                    $current++;
                }

                return $name;
            }
        }

        return "unknown";
    }


}