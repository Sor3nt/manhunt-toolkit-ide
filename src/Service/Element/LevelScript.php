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

    /** @var LevelScriptInspector  */
    private $levelScriptInspector;


    public function __construct( Resources $resources, LevelScriptInspector $levelScriptInspector )
    {
        $this->resources = $resources;
        $this->levelScriptInspector = $levelScriptInspector;
    }

    public function getScriptList( $level )
    {
        $levelFile = sprintf('/levels/%s/%s.mls', $level, $level);

        $resource = $this->resources->load($levelFile);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        $results = [];
        foreach ($levelScripts as $index => $mhsc) {

            $source = Compiler::prepare($mhsc['DBUG']['SRCE']);

            $tokenizer = new Tokenizer();
            $tokens = $tokenizer->run($source);

            $results[] = $this->levelScriptInspector->getEntityName($tokens);
        }

        sort($results);

        return $results;

    }

    public function getScript($level, $script){
        $levelFile = sprintf('/levels/%s/%s.mls', $level, $level);

        $resource = $this->resources->load($levelFile);

        /** @var array $levelScripts */
        $levelScripts = $resource->getContent();

        $results = [];
        foreach ($levelScripts as $index => $mhsc) {


            $source = Compiler::prepare($mhsc['DBUG']['SRCE']);

            $tokenizer = new Tokenizer();
            $tokens = $tokenizer->run($source);

            $entityName = $this->levelScriptInspector->getEntityName($tokens);

            if ($entityName == $script) return $mhsc['DBUG']['SRCE'];
        }

        return false;
    }


}