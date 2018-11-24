<?php
namespace App\Service\Archive\Mls;

use App\Service\Helper;

class Build {


    /**
     * @param $scripts
     * @return string
     */
    public function build( $scripts){
        $mls =
            "\x4D\x48\x4C\x53" .      // MHLS
            "\x03\x00\x09\x00"        // MHLS Version (3.9)
        ;

        ksort($scripts);

        foreach ($scripts as $index => $records) {

            $scriptCode = $this->buildSCPT( $records );
            $scriptCode .= $this->buildNAME( $records );
            $scriptCode .= $this->buildENTT( $records );
            $scriptCode .= $this->buildCODE( $records );
            $scriptCode .= $this->buildDATA( $records );
            $scriptCode .= $this->buildSMEM( $records );
            $scriptCode .= $this->buildDebug( $records );
            $scriptCode .= $this->buildSTAB( $records );

            $header = "\x4D\x48\x53\x43";

            // add MSHC size
            $header .= (pack("L", strlen(bin2hex($scriptCode )) / 2));

            $mls .= $header . $scriptCode;
        }

        return $mls;
    }



    private function buildSCPT( $records ){
        if (is_string($records['SCPT'])){
            $scptEntries = \json_decode($records['SCPT'], true);
        }else{
            $scptEntries = $records['SCPT'];
        }

        $code = "";

        foreach ($scptEntries as $scptEntry) {

            // add the name - section is 32-byte long
            $code .= hex2bin(Helper::pad(current(unpack("H*", $scptEntry['name'])), 64 * 2));

            $code .= hex2bin($scptEntry['onTrigger']);
            $code .= hex2bin(Helper::fromIntToHex($scptEntry['scriptStart']));
        }


        // SCPT Header
        $section = "\x53\x43\x50\x54";

        // add SCPT  size
        $section .= (pack("L", strlen(bin2hex($code)) / 2));

        // add SCPT section
        $section .= $code;

        return $section;
    }

    private function buildNAME( $records ){

        $data = current(unpack("H*", $records['NAME']));
        $length = strlen($data);
        $name = Helper::pad($data, $length + (4 - $length % 4 ));

        // NAME Header
        $section = "\x4E\x41\x4D\x45";

        $section .= (pack("L", strlen($name) / 2));

        $section .= hex2bin($name) ;

        return $section;
    }

    private function buildENTT( $records ){


        if (is_string($records['ENTT'])){
            $records['ENTT'] = \json_decode($records['ENTT'], true);
        }

        $typeHex = "00000000";
        if ($records['ENTT']['type'] == "levelscript") $typeHex = "02000000";

        // ENTT Header
        $section = "\x45\x4E\x54\x54";

        // add ENTT size (always 68 bytes)
        $section .= hex2bin(Helper::pad(dechex(68)));

        // add ENTT value
        $section .= hex2bin($typeHex);

        $section .= hex2bin(Helper::pad(current(unpack("H*", $records['ENTT']['name'])), 64 * 2));

        return $section;

    }

    private function buildCODE( $records ){
        if (!is_array($records['CODE'])) $records['CODE'] = explode("\n", $records['CODE']);

        $codeData = implode("", $records['CODE']);

        // CODE Header
        $section = "\x43\x4F\x44\x45";

        // add CODE  size
        $section .= (pack("L", strlen($codeData) / 2));

        // add code value
        $section .= hex2bin($codeData);

        return $section;
    }

    private function buildDATA( $records ){

        if (isset($records['DATA'])){
            if (!is_array($records['DATA'])) $records['DATA'] = explode("\n", $records['DATA']);

            $stringArraySizes = 0;

            if (isset($records['STAB'])){
                if (is_string($records['STAB'])){
                    $stab = \json_decode($records['STAB'], true);
                }else{
                    $stab = $records['STAB'];
                }

                foreach ($stab as $item) {
                    if ($item["size"] !== false){
                        $stringArraySizes += $item["size"];
                    }else{
                        if (isset($item['occurrences']) && count($item['occurrences']) > 0) {
                            // i am not sure about this part, we missed bytes, this solves it...
                            $stringArraySizes += 2 * count($item['occurrences']);
                        }else {
                            $stringArraySizes += 4;
                        }
                    }
                }
            }

            $dataCode = "";

            foreach ($records['DATA'] as $name) {

                $name = current(unpack("H*", $name));
                $name .= "00";
                $nameLength = strlen($name);

                // add NAME size (its always / max 16)
                $dataCodeTmp = Helper::pad($name);
                $dataCode .= (Helper::pad($dataCodeTmp , $nameLength +  (8 - $nameLength % 8), false, 'da'));
            }

            $dataCode .= str_repeat('da', $stringArraySizes);

            $dataCodeLength = strlen($dataCode) ;

            $dataCode = Helper::pad($dataCode, $dataCodeLength +  (8 - $dataCodeLength % 8), false, 'da');

            if (substr($dataCode, -8) == "dadadada"){
                $dataCode = substr($dataCode, 0, -8);
            }

            // DATA Header
            $scriptCode = "\x44\x41\x54\x41";

            // add DATA size
            $scriptCode .= (pack("L", strlen($dataCode) / 2));

            // add DATA value
            $scriptCode .= hex2bin($dataCode);

            return $scriptCode;
        }

        return "";

    }

    private function buildSMEM( $records ){

        // SMEM Header
        $section = "\x53\x4D\x45\x4D";

        // add SMEM size (always 4 bytes)
        $section .= "\x04\x00\x00\x00";

        // add SMEM value
        $section .= (pack("L", $records['SMEM']));

        return $section;
    }


    /**
     * This section contains the plain source code and some IDE related parameters
     * To bad that we dont have a Manhunt2.exe that loads the DBUG section.
     *
     * Currently its a useless part, we can not access this from the game, only read and learn.
     *
     * @param $records
     * @return string
     */
    private function buildDebug( $records ){
        /*
         * Build DBUG sub section SRCE
         */

        /*********************
         *
         * Build SRCE section
         *
         *********************/
        // add SRCE size
        $data = current(unpack("H*", $records['SRCE']));

        // SRCE header
        $srceCode = "\x53\x52\x43\x45";

        $srceCode .= (pack("L", strlen($data) / 2));

        // add SRCE value
        $srceCode .= hex2bin($data);
        return $srceCode;
    }

    /**
     * @param $records
     * @return string
     * @throws \Exception
     */
    private function buildSTAB( $records ){

        if (isset($records['STAB'])){

            if (is_string($records['STAB'])){
                $stabData = \json_decode($records['STAB'], true);
            }else{
                $stabData = $records['STAB'];
            }

            $stabCode = "";
            foreach ($stabData as $indexStab => $record) {

                // add name
                $name = current(unpack("H*", $record['name']));

                //REMOVE this IF we dont need this, just added for debugging
                if (isset($record['unknown'])){
                    $stabCode .= hex2bin($name . '00'. $record['unknown']);
                }else{
                    $stabCode .= hex2bin(Helper::pad($name , 32 * 2)) ;
                }

                // add offset
                if ($record['offset'] === false){
                    $stabCode .= "\xff\xff\xff\xff";
                }else{
                    $stabCode .= hex2bin( $record['offset'] );
                }

                // add size
                if ($record['size'] === false){
                    $stabCode .= "\xff\xff\xff\xff";
                }else{
                    $stabCode .= hex2bin(Helper::fromIntToHex( $record['size']));
                }

                if (isset($record['unknownType'])){
                    // add type2 ( still unknown )
                    $stabCode .= hex2bin($record['unknownType']);

                }

                switch ($record['objectType']){

                    case 'integer':
                        $stabCode .= "\x00\x00\x00\x00";
                        break;
                    case 'level_var boolean':
                        $stabCode .= "\x01\x00\x00\x00";
                        break;
                    case 'game_var real':
                        $stabCode .= "\x02\x00\x00\x00";
                        break;
                    case 'boolean':
                        $stabCode .= "\x03\x00\x00\x00";
                        break;
                    case 'level_var integer':
                        $stabCode .= "\x04\x00\x00\x00";
                        break;
                    case 'string':
                        $stabCode .= "\x05\x00\x00\x00";
                        break;
                    case 'vec3d':
                        $stabCode .= "\x06\x00\x00\x00";
                        break;
                    case 'game_var integer':
                        $stabCode .= "\x07\x00\x00\x00";
                        break;
                    case 'level_var tlevelstate':
                    case 'tLevelState':
                        $stabCode .= "\x08\x00\x00\x00";
                        break;
                    case 'unknown 0a':
                        $stabCode .= "\x0a\x00\x00\x00";
                        break;
                    case 'unknown fe':
                        $stabCode .= "\xfe\xff\xff\xff";
                        break;
                    case 'unknown ff':
                        $stabCode .= "\xff\xff\xff\xff";
                        break;
                    default:
                        throw new \Exception(sprintf('Unknown object type requested: %s', ($record['objectType']) ));
                        break;

                }

                if (count($record['occurrences']) ){

                    // add occurrence count
                    $stabCode .= hex2bin(Helper::fromIntToHex( count($record['occurrences'])));

                    // add occurrence position
                    foreach ($record['occurrences'] as $occurrence) {
                        $stabCode .= hex2bin(Helper::fromIntToHex( $occurrence));
                    }

                }else{
                    // add empty occurrence
                    $stabCode .= "\x00\x00\x00\x00";
                }

            }

            // STAB Header
            $scriptCode = "\x53\x54\x41\x42";

            // add STAB size
            $scriptCode .= (pack("L", strlen(bin2hex($stabCode )) / 2));

            // add STAB value
            $scriptCode .= $stabCode;

            return $scriptCode;
        }

        return "";
    }

}
