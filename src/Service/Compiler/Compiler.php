<?php
namespace App\Service\Compiler;

use App\Service\Compiler\FunctionMap\Manhunt;
use App\Service\Compiler\FunctionMap\Manhunt2;
use App\Service\Compiler\FunctionMap\ManhuntDefault;
use App\Service\Compiler\Services\Procedure;
use App\Service\Helper;

class Compiler {


    /**
     * @param $source
     * @return mixed
     */
    static public function prepare($source){

        $source = str_replace([
            "if (GetEntity('Syringe_(CT)')) <> NIL then",
            "if (GetEntity('Syringe_(CT)')) = nil then",
            "if(",
            "while(",
            "PLAYING  TWITCH"
        ],[
            "if GetEntity('Syringe_(CT)') <> NIL then",
            "if GetEntity('Syringe_(CT)') = nil then",
            "if (",
            "while (",
            "PLAYING__TWITCH"  // we replace this because the next operation will remove the whitespaces

        ], $source);


        // remove double whitespaces
        $source = preg_replace("/\s+/", ' ', $source);

        // remove comments / unused code

        $source = preg_replace("/({([^{^}])*)*{([^{^}])*}(([^{^}])*})*/m", "", $source);

        if (preg_last_error() == PREG_JIT_STACKLIMIT_ERROR){
            die("PHP7 issue, pls disable pcre.jit=0 in your php.ini");
        }

        $source = str_replace([
            "PLAYING__TWITCH",
            "end end"
        ],[
            "PLAYING  TWITCH",
            "end; end",
        ], $source);

        // replace line ends with new lines
        $source = preg_replace("/;/", ";\n", $source);

        return trim($source);
    }

    /**
     * @param $type
     * @return int
     */
    private function getMemorySizeByType($type ){

        if (substr($type, 0, 7) == "string[") {
            return (int)explode("]", substr($type, 7))[0];
        }

        switch ($type){
            case 'vec3d':
                return 12; // 3 floats a 4-bytes
                break;

            default:
                return 4;
                break;

        }
    }

    private function getHeaderVariables( $tokens, $types ){

        $current = 0;
        $currentSection = 'header';

        $vars = [];

        $inside = false;

        while ($current < count($tokens)) {

            $token = $tokens[ $current ];

            // we need to know the current section for the defined vars
            if (
                $inside &&
                (
                    $token['type'] == Token::T_DEFINE_SECTION_TYPE ||
                    $token['type'] == Token::T_DEFINE_SECTION_ENTITY ||
                    $token['type'] == Token::T_DEFINE_SECTION_CONST ||
                    $token['type'] == Token::T_PROCEDURE ||
                    $token['type'] == Token::T_SCRIPT
                )

            ){
                return $vars;
            }

            if ($token['type'] == Token::T_SCRIPT || $token['type'] == Token::T_PROCEDURE) return $vars;

            if ($token['type'] == Token::T_DEFINE_SECTION_VAR) $inside = true;

            if ($inside == false){
                $current++;
                continue;
            }

            if ($token['type'] == Token::T_VARIABLE && $tokens[$current + 1]['type'] == Token::T_DEFINE){

                $variables = [
                    $token
                ];

                $prevToken = $tokens[ $current - 1];
                $innerCurrent = $current;
                while($prevToken['type'] == Token::T_VARIABLE){
                    $variables[] = $prevToken;
                    $innerCurrent--;
                    $prevToken = $tokens[ $innerCurrent - 1];
                }

                $variables = array_reverse($variables);

                foreach ($variables as $variable) {
                    if (!$this->isVariableInUse($tokens, $variable['value'])) continue;

                    $variableType = strtolower($tokens[$current + 2]['value']);

                    $row = [
                        'section' => $currentSection,
                        'type' => substr($variableType, 0, 7) == "string[" ? 'stringarray' : $variableType,
                        'size' => $this->getMemorySizeByType($variableType)
                    ];

                    if (isset($types[ $variableType ] )) $row['abstract'] = 'state';

                    $vars[$variable['value']] = $row;
                }
            }

            $current++;
        }

        return $vars;
    }

    private function isVariableInUse($tokens, $var){

        $result = $this->recursiveSearch($tokens, [
            Token::T_VARIABLE,
            Token::T_ASSIGN
        ]);

        foreach ($result as $token) {

            if ($token['value'] == $var){
                return true;
            }
        }

        return false;
    }

    private function getConstants($tokens, &$smemOffset){

        $current = 0;

        $vars = [];
        $currentSection = false;

        while ($current < count($tokens)) {

            $token = $tokens[ $current ];

            // we need to know the current section for the defined vars
            if ($token['type'] == Token::T_DEFINE_SECTION_CONST){
                $currentSection = 'const';
                $current++;
                continue;
            }

            if ($currentSection == "const"){

                if (
                    $token['type'] == Token::T_DEFINE_SECTION_VAR ||
                    $token['type'] == Token::T_DEFINE_SECTION_ENTITY ||
                    $token['type'] == Token::T_DEFINE_SECTION_TYPE ||
                    $token['type'] == Token::T_SCRIPT
                ){
                    break;
                }else{
                    $variable = $token['value'];

                    $vars[$variable] = $tokens[$current + 2];

                    $current = $current + 3;

                    $varVal = $vars[$variable]['value'];

                    if (substr($varVal, 0, 7) == "string["){
                        $smemOffset += $this->getMemorySizeByType($varVal);;
                    }else if (
                        $vars[$variable]['type'] == Token::T_INT ||
                        $vars[$variable]['type'] == Token::T_FLOAT
                    ) {
                        $smemOffset += 4;

                    }

                }
            }

            $current++;
        }

        return $vars;
    }


    private function getStrings($tokens, &$smemOffset){

        $response =  $this->recursiveSearch($tokens, [Token::T_STRING]);

        $result = [];

        foreach ($response as $item) {

            $value = str_replace('"', '', $item['value']);

            $result[$value] = $value;
        }

        $strings = array_unique($result);
        foreach ($strings as &$string) {

            $length = strlen($string) + 1;
            $string = [
                'offset' => Helper::fromIntToHex($smemOffset),
                'length' => strlen($string)
            ];

            if (4 - $length % 4 != 0){
                $length += 4 - $length % 4;
            }

            $smemOffset += $length;

        }

        return $strings;
    }

    private function getTypes($tokens){

        $types = [];

        $current = 0;
        $offset = 0;
        $inside = false;
        $currentTypeSection = false;

        while( $current < count($tokens)){

            $token = $tokens[ $current ];

            if ($token['type'] == Token::T_DEFINE_SECTION_TYPE) {
                $inside = true;

            }else if (
                $inside && (
                    $token['type'] == Token::T_DEFINE_SECTION_VAR ||
                    $token['type'] == Token::T_DEFINE_SECTION_ENTITY ||
                    $token['type'] == Token::T_DEFINE_SECTION_CONST ||
                    $token['type'] == Token::T_SCRIPT
                )
            ){
                return $types;

            }else if (
                $token['type'] == Token::T_BRACKET_OPEN ||
                $token['type'] == Token::T_BRACKET_CLOSE
            ){
                // do nothing
            }else if (
                $token['type'] == Token::T_LINEEND
            ){

                $currentTypeSection = false;
            }else if ($inside){

                if ($token['type'] == Token::T_IS_EQUAL){
                    $beforeToken = $tokens[ $current - 1 ];

                    $offset = 0;
                    $currentTypeSection = strtolower($beforeToken['value']);

                    $types[ $currentTypeSection ]  = [];


                }else if ($currentTypeSection && $token['type'] == Token::T_VARIABLE){

                    $types[ $currentTypeSection ][ strtolower($token['value']) ] = [
                        'type' => 'level_var tLevelState',
                        'section' => "header",
                        'offset' => Helper::fromIntToHex($offset)
                    ];

                    $offset++;
                }
            }

            $current++;
        }

        return $types;
    }

    /**
     * @param $tokens
     * @return array
     * @throws \Exception
     */
    private function getEntity($tokens){
        $current = 0;

        $scriptName = strtolower($tokens[1]['value']);

        while($current < count($tokens)){

            $token = $tokens[$current];

            if ($token['type'] == Token::T_DEFINE_SECTION_ENTITY){

                return [
                    'name' => strtolower($tokens[$current + 1]['value']),
                    'type' => $scriptName == "levelscript" ? "levelscript" : "other"
                ];
            }

            $current++;
        }

        throw new \Exception('Compiler could not find / parse the Entity section');
    }

    public function parse($source, $levelScript = false, $game = "mh2"){

        if (!defined('GAME')){
            define('GAME', $game);
        }

        $originalSource = $source;

        // cleanup the source code
        $source = $this->prepare($source);
//        var_dump($source);
//        exit;

        // convert script code into tokens
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->run($source);

        $types = $this->getTypes($tokens);
        // extract every header and script variable definition
        $headerVariables = $this->getHeaderVariables($tokens, $types);

        if ($levelScript != false){
            /**
             * the given script is not the level script but maybe use also the levelscript variables (globals)
             * overwrite the given variables to correct the offsets
             */
            foreach ($levelScript['extra']['headerVariables'] as $levelHeaderVariableName => $levelHeaderVariable) {
                foreach ($headerVariables as $headerVariableName => &$headerVariable) {

                    if (strpos(strtolower($headerVariable['type']), 'level_var') !== false){

                        if ($levelHeaderVariableName == $headerVariableName){
                            $headerVariable['offset'] = $levelHeaderVariable['offset'];
                        }
                    }
                }
            }
        }

        $smemOffset = 0;

        $const = $this->getConstants($tokens, $smemOffset);
        $headerStrings = [];

        $result = [];

        foreach ($const as $item) {

            if ($item['type'] == Token::T_STRING){
                $value = str_replace('"', '', $item['value']);

                $result[$value] = $value;
            }
        }

        $strings = array_unique($result);
        foreach ($strings as $string) {

            $length = strlen($string) + 1;
            $headerStrings[$string] = [
                'offset' => Helper::fromIntToHex($smemOffset),
                'length' => strlen($string)
            ];

            if (4 - $length % 4 != 0){
                $length += 4 - $length % 4;
            }

            $smemOffset += $length;
        }


        $tokens = $tokenizer->fixShortStatementMissedLineEnd($tokens);
        $tokens = $tokenizer->fixProcedureEndCall($tokens);
        $tokens = $tokenizer->fixTypeMapping($tokens, $types);
        $tokens = $tokenizer->fixHeaderBracketMismatches($tokens);

        // parse the token list to a ast
        $parser = new Parser( );
        $ast = $parser->toAST($tokens);


        $header = [];
        $currentSection = "header";

        $sectionCode = [];
        $start = 1;

        $lineCount = 1;

        $strings4Scripts = [];

        foreach ($ast["body"] as $index => $token) {

            if (
                $token['type'] == Token::T_SCRIPT ||
                $token['type'] == Token::T_PROCEDURE ||
                $token['type'] == Token::T_FUNCTION
            ){
                $strings4Scripts[$token['value']] = $this->getStrings($token['body'], $smemOffset);
            }
        }

        $ast = $parser->handleForward($ast);

        $procedures = $parser->getProcedures($ast);

        foreach ($headerVariables as $name => &$item) {

            if (!isset($item['offset'])){
                $item['offset'] = Helper::fromIntToHex($smemOffset);

            }

            $size = $item['size'];

            if ($size % 4 !== 0){
                $size += $size % 4;
            }

            $smemOffset += $size;
        }

        $scriptBlockSizes = [];
        $lastScriptEnd = 0;

        foreach ($ast["body"] as $index => $token) {

            if (
                $token['type'] == Token::T_SCRIPT ||
                $token['type'] == Token::T_PROCEDURE ||
                $token['type'] == Token::T_FUNCTION
            ){

                if ($token['type'] == Token::T_PROCEDURE){
                    $procedures[strtolower($token['value'])] = $lineCount - 1;
                }



                $currentSection = "script";
                $scriptName = $token['value'];

                /**
                 * Calculate string offsets
                 */
                $scriptVar = $this->getScriptVar($token['body']);

                $smemOffset2 = 0;
                $scriptVarFinal = [];
                foreach ($scriptVar as $name => &$item) {
                    $smemOffset2 += $item['size'];

                    $item['offset'] = Helper::fromIntToHex($smemOffset2);
                    if ($smemOffset2 % 4 !== 0){
                        $smemOffset2 += $smemOffset2 % 4;
                    }
                    $scriptVarFinal[$name ] = $item;
                }


                foreach ($headerVariables as $_name => $_item) {

                    if ($this->isVariableInUse($token['body'], $_name)){
                        $scriptVarFinal[$_name ] = $_item;
                    }
                }

                /**
                 * Translate Token AST to Bytecode
                 */
                $emitter = new Emitter(
                    $scriptVarFinal,
                    array_merge($strings4Scripts[$scriptName], $headerStrings),
                    $types,
                    $const,
                    $lineCount
                );

                $code = $emitter->emitter([
                    'type' => "root",
                    'body' => [
                        $token
                    ]
                ], true, [ 'procedures' => $procedures ]);

                if ($token['type'] == Token::T_SCRIPT){
                    $scriptBlockSizes[$scriptName] = $lastScriptEnd;
                }

                $lastScriptEnd = count($code) * 4;

                foreach ($code as $line) {
                    if ($line->lineNumber !== $start){
                        var_dump( $token);
                        var_dump( $code);
                        var_dump( $line, $start);
                        throw new \Exception('Calculated line number did not match with the generated one');
                    }

                    $start++;
                    $sectionCode[] = $line->hex;
                }

                if (isset($line)) $lineCount = $line->lineNumber + 1;


            }else if ($currentSection == "header"){
                $header[] = $token;
            }else{
                throw new \Exception(sprintf('Compiler: parse unknown type for emitter %s', $token['type']));
            }
        }

        return [
            'extra' => [
                'headerVariables' => $headerVariables
            ],
            'CODE' => $sectionCode,
            'DATA' => $this->generateDATA($strings4Scripts),
            'STAB' => $this->generateSTAB($headerVariables, $sectionCode),
            'SCPT' => $this->generateSCPT($scriptBlockSizes, $game),
            'ENTT' => $this->getEntity($tokens),
            'SRCE' => $originalSource,

            //todo: value did not match...
            'SMEM' => 78596,
            'DMEM' => 78596,
            'LINE' => []

        ];
    }

    public function recursiveSearch($tokens, $searchType, $ignoreTypes = []){


        $result = [];
        foreach ($tokens as $token) {

            if (count($searchType) == 0 || in_array($token['type'],$searchType)){
                if (in_array($token['type'],$ignoreTypes)){
                    continue;
                }else{
                    $result[] = $token;
                }
            }

            if (isset($token['variable'])){
                $response =  $this->recursiveSearch([$token['variable']], $searchType, $ignoreTypes);
                foreach ($response as $item) {
                    $result[] = $item;
                }
            }

            if (isset($token['start'])){
                $response =  $this->recursiveSearch([$token['start']], $searchType, $ignoreTypes);
                foreach ($response as $item) {
                    $result[] = $item;
                }
            }

            if (isset($token['end'])){
                $response =  $this->recursiveSearch([$token['end']], $searchType, $ignoreTypes);
                foreach ($response as $item) {
                    $result[] = $item;
                }
            }

            if (isset($token['params'])) {
                $response =  $this->recursiveSearch($token['params'], $searchType, $ignoreTypes);
                foreach ($response as $item) {
                    $result[] = $item;
                }

            }else if (isset($token['body'])){
                $response =   $this->recursiveSearch($token['body'], $searchType, $ignoreTypes);
                foreach ($response as $item) {
                    $result[] = $item;
                }
            }else if (isset($token['cases'])){

                if (isset($token['switch'])){
                    $response =   $this->recursiveSearch([$token['switch']], $searchType, $ignoreTypes);
                    foreach ($response as $item) {
                        $result[] = $item;
                    }
                }

                foreach ($token['cases'] as $case) {

                    if (!isset($case['condition'])){
                        $response = $this->recursiveSearch($case['body'], $searchType, $ignoreTypes);
                        foreach ($response as $item) {
                            $result[] = $item;
                        }
                    }

                    if (isset($case['condition'])){
                        $response = $this->recursiveSearch($case['condition'], $searchType, $ignoreTypes);
                        foreach ($response as $item) {
                            $result[] = $item;
                        }

                        $response = $this->recursiveSearch($case['isTrue'], $searchType, $ignoreTypes);
                        foreach ($response as $item) {
                            $result[] = $item;
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function getScriptVar( $tokens ){

        $otherTokens = [];
        $varSection = [];

        foreach ($tokens as $token) {
            if ($token['type'] == Token::T_DEFINE_SECTION_VAR) {
                $varSection = $token['body'];
            }else{
                $otherTokens[] = $token;
            }
        }

        $tokens = $varSection;
        $current = 0;
        $vars = [];

        while ($current < count($tokens)) {

            $token = $tokens[ $current ];

            if ($token['type'] == Token::T_VARIABLE && $tokens[$current + 1]['type'] == Token::T_DEFINE_TYPE){

                $variables = [ $token ];

                $oriPos = $current;
                if (isset($tokens[ $current - 1])){

                    $prevToken = $tokens[ $current - 1];
                    while($prevToken['type'] == Token::T_VARIABLE){
                        $variables[] = $prevToken;
                        $current--;
                        if (!isset($tokens[ $current - 1])) break;
                        $prevToken = $tokens[ $current - 1];
                    }
                }

                $variables = array_reverse($variables);

                $current = $oriPos + 1;

                $variableType = strtolower($tokens[$current]['value']);

                foreach ($variables as $variable) {
                    $variable = $variable['value'];

                    $row = [
                        'section' => 'script',
                        'type' => $variableType
                    ];

                    if (substr($variableType, 0, 7) == "string["){
                        $row['type'] = 'stringarray';
                    }

                    $row['size'] = $this->getMemorySizeByType($variableType);

                    $vars[$variable] = $row;
                }
            }

            $current++;
        }

        return $vars;
    }

    public function generateSCPT( $scriptBlockSizes, $game ){

        $scpt = [];
        $scriptSize = 0;
        foreach ($scriptBlockSizes as $name => $item) {
            $scriptSize += $item;

            $functionEventDefinitionDefault = ManhuntDefault::$functionEventDefinition;
            $functionEventDefinition = Manhunt2::$functionEventDefinition;
            if ($game == "mh1") $functionEventDefinition = Manhunt::$functionEventDefinition;

            if (isset($functionEventDefinitionDefault[strtolower($name)])) {
                $onTrigger = $functionEventDefinitionDefault[strtolower($name)];
            }else if (isset($functionEventDefinition[strtolower($name)])){
                $onTrigger = $functionEventDefinition[strtolower($name)];
            }else{
                $onTrigger = $functionEventDefinition['__default__'];
            }

            $scpt[] = [
                'name' => strtolower($name),
                'onTrigger' => $onTrigger,
                'scriptStart' => $scriptSize
            ];

        }

        return $scpt;
    }


    public function generateDATA( $strings4Scripts ){
        $result = [];

        foreach ($strings4Scripts as $strings) {
            foreach ($strings as $value => $string) {
                $result[] = $value;
            }
        }

        return $result;
    }


    public function generateSTAB( $headerVariables, $sectionCode ){

        $result = [];


        foreach ($headerVariables as $name => $variable) {

            $occur = [];

            $varType = $variable['type'];
            $hierarchieType = '01000000';

            if (substr($varType, 0, 9) == "level_var"){
                $varType = substr($varType, 10);

                foreach ($sectionCode as $index => $code) {

                    if ($code == $variable['offset']){
                        $occur[] = $index * 4;
                    }
                }

                $hierarchieType = "ffffffff";
                $variable['offset'] = "ffffffff";
                $variable['size'] = "ffffffff";
            }

            if (strtolower($varType) == "tlevelstate") $varType = "tLevelState";
            if ($varType == "stringarray") $varType = "string";

            /**
             * todo: not important, the type should say tLevelState but its messed up by the state handling
             */
            if (isset($variable['abstract']) && $variable['abstract'] == "state") {
                $varType = "tLevelState";
            }

            $row = [
                'name' => strtolower($name),
                'offset' => $variable['offset'],
                'size' => $variable['size'],

                'hierarchieType' => $hierarchieType,
                'objectType' => ($varType),

                'occurrences' => $occur
            ];


            //todo...
//            if (strtolower($name) == "ldebuggingflag"){
//                $row['unknown'] = '012000b6012000dd03200072192000b319';
//            }

            $result[] = $row;
        }
        usort($result, function($a,$b){
            return $a['name'] > $b['name'];
        });

        return $result;
    }

}