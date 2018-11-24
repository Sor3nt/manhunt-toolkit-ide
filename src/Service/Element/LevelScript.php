<?php

namespace App\Service\Element;

use App\Service\Archive\Mls;
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

    /** @var Compiler  */
    private $compiler;

    /** @var Mls  */
    private $mls;

    /**
     * LevelScript constructor.
     * @param Resources $resources
     * @param Compiler $compiler
     * @param Mls $mls
     */
    public function __construct( Resources $resources, Compiler $compiler, Mls $mls ) {
        $this->resources = $resources;
        $this->mls = $mls;
        $this->compiler = $compiler;
    }

    public function getMls( $level ){

        $levelFile = sprintf('/levels/%s/%s.mls', $level, $level);

        return $this->resources->load($levelFile);
    }

    /**
     * @param $level
     * @return array
     */
    public function getScriptList( $level )
    {
        $resource = $this->getMls($level);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        $results = [];
        foreach ($levelScripts as $index => $mhsc) {
            $results[] = $this->getEntityName($mhsc['SRCE']);
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
        $resource = $this->getMls($level);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        foreach ($levelScripts as $index => $mhsc) {

            $entityName = $this->getEntityName($mhsc['SRCE']);

            if ($entityName == $script) return $mhsc;
        }

        return false;
    }

    public function replaceScript($level, $script, $newMhsc){
        $resource = $this->getMls($level);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        foreach ($levelScripts as $index => &$mhsc) {

            $entityName = $this->getEntityName($mhsc['SRCE']);

            if ($entityName == $script){
                $mhsc = $newMhsc;
            }
        }

        $resource->setContent($levelScripts);

        return $resource;
    }

    public function compile($source, $levelScript = false, $game = "mh2"){
        return $this->compiler->parse($source, $levelScript, $game);
    }

    public function pack($scripts){
        return $this->mls->pack($scripts);
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