<?php
namespace App\Service\Archive\Mls;


use App\Service\Binary;

class Extract {


    public function __construct( $binaryData, $game = "mh2")
    {

        /** @var Binary $remain */
        $binary = new Binary( $binaryData );

        // detect the current version, wii or pc/ps2/psp?
        $platform = $binary->substr(4, 4, $remain) == "00090003" ? "wii" : "pc";

        $nextSection = $remain->substr(0, 4)->toString();

        $mhscs = [];

        // there is only one script inside the MLS
        if ($nextSection === "SCPT"){
            $mhscs[] = $this->parseBody($remain, $game);

            // there is multiple scripts inside the MLS
        }else if ($nextSection === "MHSC"){
            do{

                list(,$data) = $this->getLabelSizeData( $remain, $remain, $platform);

                $mhscs[] = $this->parseBody($data, $game, $platform);

            }while($remain->length() > 0);
        }

        return $mhscs;
    }


}
