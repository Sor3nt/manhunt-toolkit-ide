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

use App\Service\Compiler\FunctionMap\ManhuntDefault;

class LevelScriptInspector
{

    public function inspect( $tokens ){
        return [
            'entityName' => $this->getEntityName($tokens),
            'definedScripts' => $this->getDefinedScripts($tokens),
            'scriptReferences' => $this->getScriptReferences($tokens)
        ];
    }

    public function getScriptReferences( $tokens ){
        $results = [];

        foreach ($tokens as $index => $token) {
            if ($token['type'] == Token::T_FUNCTION && strtolower($token['value']) == "runscript"){
                $entityName = $tokens[$index + 2]['value'];
                $scriptName = $tokens[$index + 3]['value'];

                $entityName = substr($entityName,1,-1);
                $scriptName = substr($scriptName,1,-1);

                if (!isset($results[$entityName]))  $results[$entityName] = [];
                if (!isset($results[$entityName][$scriptName]))  $results[$entityName][$scriptName] = [];

                $results[$entityName][$scriptName] = [
                    'scriptName' => $scriptName,
//                    'lineNumber' => $token['lineNumber']
                ];
            }
        }

        return $results;
    }

    public function getDefinedScripts( $tokens ){

        $scripts = [];
        foreach ($tokens as $token) {
            if ($token['type'] == Token::T_SCRIPT_NAME){

                $type = "regular";
                if (isset(ManhuntDefault::$functionEventDefinition[strtolower($token['value'])])){
                    $type = "event";
                }

                $scripts[] = [
                    'value' => $token['value'],
                    'type' => $type,
                    'lineNumber' => $token['lineNumber']
                ];
            }
        }

        return $scripts;
    }

    public function getEntityName( $tokens ){

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