<?php
namespace App\Service\Compiler\Emitter;

use App\Service\Compiler\Evaluate;
use App\Service\Compiler\FunctionMap\Manhunt;
use App\Service\Compiler\FunctionMap\Manhunt2;
use App\Service\Compiler\FunctionMap\ManhuntDefault;
use App\Service\Compiler\Token;

class T_VARIABLE {

    static public function getMapping( $node, \Closure $emitter = null , $data ){

        $constantsDefault = ManhuntDefault::$constants;
        $constants = Manhunt2::$constants;
        if (GAME == "mh1") $constants = Manhunt::$constants;


        $value = $node['value'];
        $valueLower = strtolower($value);


        if (isset($data['customData']['customFunctions']) && isset($data['customData']['customFunctions'][ $valueLower ])) {
            $mapped = [
                'offset' => $data['customData']['customFunctions'][$valueLower],
                'section' => 'script',
                'type' => 'custom_functions'
            ];


        }else if (isset($data['customData']['procedureVars']) && isset($data['customData']['procedureVars'][ $value ])) {
            $mapped = $data['customData']['procedureVars'][$value];

            $mapped['section'] = 'script';
            $mapped['type'] = 'procedure';

        }else if (isset($data['customData']['customFunctionVars']) && isset($data['customData']['customFunctionVars'][ $value ])) {
            $mapped = $data['customData']['customFunctionVars'][$value];

            $mapped['section'] = 'script';
            $mapped['type'] = 'customFunction';

        }else if (isset($data['variables'][ $value ])){
            $mapped = $data['variables'][ $value ];

        }else if (isset($constantsDefault[ $value ])) {
            $mapped = $constantsDefault[ $value ];
            $mapped['section'] = "header";
            $mapped['type'] = "constant";

        }else if (isset($constants[ $value ])) {
            $mapped = $constants[ $value ];
            $mapped['section'] = "header";
            $mapped['type'] = "constant";

        }else if (isset($data['const'][ $value ])){
            $mapped = $data['const'][ $value ];
            $mapped['section'] = "script";


            if ($mapped['type'] == Token::T_INT) {
                $mapped['valueType'] = "integer";

            }else if ($mapped['type'] == Token::T_STRING){
                $mapped['valueType'] = "string";

            }else if ($mapped['type'] == Token::T_FLOAT){
                $mapped['valueType'] = "float";

            }

            $mapped['type'] = "constant";

        }else if (strpos($value, '.') !== false){

            $mapped = Evaluate::getObjectToAttributeSplit($value, $data);

        }else if (
            isset($node['target']) &&
            isset($data['types'][ $node['target'] ])
        ){

            $variableType = $data['types'][$node['target']];
            $mapped = $variableType[ strtolower($value) ];
        }else{
            throw new \Exception(sprintf("T_VARIABLE: unable to find variable offset for %s", $value));
        }

        return $mapped;
    }

    static public function map( $node, \Closure $getLine, \Closure $emitter, $data ){

        $mapped = self::getMapping($node, $emitter, $data);

        $typeHandler = "App\\Service\\Compiler\\Emitter\\Types\\";
        $typeHandler .= "T_";
        $typeHandler .= strtoupper($mapped['section']);

        if (isset($mapped['isLevelVar']) && $mapped['isLevelVar'] == true){
            $typeHandler .= "_LEVEL_VAR";

        }

        if (isset($mapped['abstract'])){
            $typeHandler .= "_" . strtoupper($mapped['abstract']);

        }else{
            $typeHandler .= "_" . strtoupper($mapped['type']);
        }

        $typeHandler = str_replace(' ', '_', $typeHandler);

        if (class_exists($typeHandler)){
            $code = $typeHandler::map($node, $getLine, $emitter, $data);
        }else{
            var_dump($mapped, $data);
            throw new \Exception($typeHandler . " Not implemented!");
        }

        return $code;
    }

}