<?php
namespace App\Service\Archive\Mls;


use App\Service\Binary;
use App\Service\Helper;

class Extract {

    private $binary;

    private $game;
    private $platform;

    public function __construct( $binaryData, $game = "mh2")
    {

        $this->game = $game;

        $this->binary = new Binary( $binaryData );

    }

    public function get(){

        /** @var Binary $remain */
        // detect the current version, wii or pc/ps2/psp?
        $this->platform = $this->binary->substr(4, 4, $remain) == "00090003" ? "wii" : "pc";

        $nextSection = $remain->substr(0, 4)->toString();

        $mhscs = [];

        // there is only one script inside the MLS
        if ($nextSection === "SCPT"){
            $mhscs[] = $this->parse($remain);

            // there is multiple scripts inside the MLS
        }else if ($nextSection === "MHSC"){
            do{

                list(,$data) = $this->getLabelSizeData( $remain, $remain);

                $mhscs[] = $this->parse($data);

            }while($remain->length() > 0);
        }

        return $mhscs;
    }


    private function getLabelSizeData( Binary $data, Binary &$remain = null){

        $label = $data->substr(0, 4, $data);
        $size = $data->substr(0, 4, $data, $this->platform == "wii");

        return [
            $label->toString(),
            $data->substr(0, $size->toInt(), $remain)
        ];
    }

    private function parse(Binary $remain ){
        /** @var Binary $code */
        /** @var Binary $sectionCode */
        /** @var Binary $data */

        $unpacked = [];

        do{
            list($scriptLabel, $data) = $this->getLabelSizeData( $remain, $remain);

            $unpacked[$scriptLabel] = [];

            switch($scriptLabel){

                case 'DMEM': break;
                case 'SCPT': $unpacked['SCPT'] = $this->parseSCPT($data); break;
                case 'NAME': $unpacked['NAME'] = $this->parseNAME($data); break;
                case 'ENTT': $unpacked['ENTT'] = $this->parseENTT($data, $unpacked['NAME']); break;
                case 'CODE': $unpacked['CODE'] = $this->parseCODE($data); break;
                case 'SMEM': $unpacked['SMEM'] = $this->parseSMEM($data); break;
                case 'DBUG': $unpacked['SRCE'] = $this->parseDBUG($data); break;

                case 'DATA':

                    //keep until mh1 is implemented
                    $unpacked['DATARAW'] = $data->toHex();
                    $unpacked['DATA'] = $this->parseDATA($data);

                    break;

                case 'STAB':
                    //keep until mh1 is implemented
                    $unpacked['STAB_RAW'] = $data->toBinary();
                    $unpacked['STAB'] = $this->parseSTAB($data);

                    break;

                default:
                    throw new \Exception(sprintf('Unknown Script Section %s', $scriptLabel ));
                    break;
            }

        }while($remain->length() > 0);

        return $unpacked;
    }

    private function parseSCPT( Binary $data ){

        $parts = $data->split(72);

        $result = [];

        foreach ($parts as $index => $part) {

            $name = $part->substr(0, 64, $part);

            $onTriggerOffset = $part->substr(0, 4, $part, $this->platform == "wii");
            $position = $part->substr(0, 4, $part, $this->platform == "wii");

            $result[] = [
                'name' => $name->toString(),
                'onTrigger' => $onTriggerOffset->toHex(),
                'scriptStart' => $position->toInt()
            ];
        }

        return $result;
    }

    private function parseNAME( Binary $data ){
        return $data->substr(0, "\x00", $data)->toString();
    }

    private function parseENTT( Binary $data, $name ){

        $data->skipBytes(4);

        return [
            'name' => $data->toString(),
            'type' => $name == "levelscript" ? "levelscript" : "other"
        ];

    }

    private function parseCODE( Binary $data ){

        $result = [];

        $split = $data->split(4, $this->platform == "wii");
        foreach ($split as $value) {
            $result[] = $value->toHex();
        }

        return $result;
    }

    private function parseDATA( Binary $data ){
        /*
         * No fixed space ? we need to search and grab the stuff
         */

        $result = [];
        do{
            $name = $data->substr(0, "\x00", $data);

            $nextByte = $data->substr(0,1)->toBinary();

            while($nextByte == "\xBC" || $nextByte == "\x20" || $nextByte == "\xDA" || $nextByte == "\x00"){
                $data = $data->skipBytes(1);
                $nextByte = $data->substr(0,1)->toBinary();
            }

            $result[] = $name->toString();

        }while($data->length() > 0);

        return $result;
    }

    private function parseSTAB( Binary $data ){
        $entries = [];
        do {

            $entry = [];

            /**
             * section 1 is 32 byte long
             *
             * - name (dynamic length) terminated by \x00
             * - unknown data
             *
             * section 2 is 20 byte long + extra bytes ( 4-byte blocks )
             *
             * - 4-bytes defined at (byte offset from CODE) or FF FF FF FF
             * - 4-bytes Length
             * - 4-bytes ???         wenn Occurrence vorhanden sind is das immer FFFF
             * - 4-bytes Value Type (int,bool,float, string, tLevelState ....)
             * - 4-bytes Occurrence/Usage Count
             * - [4-bytes ... ] byte offset of the occurred call in CODE section (MH2 only)
             *
             *
             * the name "me" has always the same setup, except the 4-byte which define the offset
             */

            /** @var Binary $section2 */

            $section1 = $data->substr(0, 32, $data);
            $section2 = $data->substr(0, $this->game == "mh1" ? 16 : 20, $data);

            $unknown1 = new Binary();
            $name = $section1->substr(0, "\x00", $unknown1);

            $entry['name'] = $name->toString();

            /** @var Binary $unknown1 */
            $unknown1 = $unknown1->skipBytes(1);

            $section2 = $section2->split(4);

            /** @var Binary[] $section2 */

            if ($section2[0]->toHex() == "ffffffff"){
                $entry['offset'] = false;
            }else{
                $entry['offset'] = $section2[0]->toHex();
                if ($this->platform == "wii") {
                    $entry['offset'] = Helper::toBigEndian($entry['offset']);
                }
            }

            if ($section2[1]->toHex() == "ffffffff"){
                $entry['size'] = false;
            }else{
                if ($this->platform == "wii") {
                    $entry['size'] = (new Binary(Helper::toBigEndian($section2[1]->toHex()), true))->toInt();
                }else{
                    $entry['size'] = $section2[1]->toInt();
                }
            }

            if ($this->game == "mh1"){
                $valueType = $section2[2]->toHex();
                $occurrenceCount = $section2[3]->toInt();

            }else{
                if ($this->platform == "wii") {
                    $entry['unknownType'] = Helper::toBigEndian($section2[2]->toHex());
                    $valueType = Helper::toBigEndian($section2[3]->toHex());
                    $occurrenceCount = (new Binary(Helper::toBigEndian($section2[4]->toHex())))->toInt();

                }else{
                    $entry['unknownType'] = $section2[2]->toHex();
                    $valueType = $section2[3]->toHex();
                    $occurrenceCount = $section2[4]->toInt();
                }
            }

            switch ($valueType){
                case "00000000";
                    $objectType = "integer";
                    break;

                case "01000000";
                    $objectType = "level_var boolean";
                    break;

                case "02000000";
                    $objectType = "game_var real";
                    break;

                case "03000000";
                    $objectType = "boolean";
                    break;

                case "04000000";
                    $objectType = "level_var integer";
                    break;

                case "05000000";
                    $objectType = "string";
                    break;

                case "06000000";
                    $objectType = "vec3d";
                    break;

                case "07000000";
                    $objectType = "game_var integer";
                    break;

                case "0a000000";
                    $objectType = "unknown 0a";
                    break;


                case "feffffff";
                    $objectType = "unknown fe";
                    break;

                case "ffffffff";
                    $objectType = "unknown ff";
                    break;

                case "50bf2b02";
                    $objectType = "unknown 50bf2b02";
                    break;

                case "20536372";
                    $objectType = "unknown 20536372";
                    break;

                case "08000000";
                    $objectType = "tLevelState";
                    break;

                default:
                    var_dump($name->toBinary());
                    throw new \Exception(sprintf('Unknown object type sequence: %s', $valueType ));
                    break;

            }

            $entry['objectType'] = $objectType;
            $entry['occurrences'] = [];

            if ($occurrenceCount > 0){
                $occurrencesRaw = $data->substr(0, $occurrenceCount * 4, $data)->split(4);
                foreach ($occurrencesRaw as $occurrence) {
                    $entry['occurrences'][] = $occurrence->toInt();
                }
            }

            //these are the values inside the first 32-byte name section, appear right after the name -- looks like garbage
            if ($unknown1->toString() != ""){
                $entry['unknown'] = "";

                $split = $unknown1->split(4, $this->platform == "wii");
                foreach ($split as $item) {
                    $entry['unknown'] .= $item->toHex();
                }
            }

            $entries[] = $entry;


        }while($data->length());

        return $entries;
    }

    private function parseSMEM( Binary $data ){
        if ($this->platform == "wii") $data = new Binary(Helper::toBigEndian($data->toHex()), true);
        return $data->toInt();

    }

    private function parseDBUG( Binary $data ){

        list(,$sectionCode) = $this->getLabelSizeData( $data, $data);
        return $sectionCode->toBinary();
    }
}
