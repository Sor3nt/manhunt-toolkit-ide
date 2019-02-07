<?php
namespace App\Tests\Manhunt1;

use App\MHT;
use App\Service\Compiler\Compiler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GenInteriorWoodDoorTest extends KernelTestCase
{

    public function test()
    {

        $script = "

SCRIPTMAIN DOORScript;

ENTITY
Gen_Interior_WoodDoor_(D)16	:	et_name;

VAR
builddoor	:	EntityPtr; { hab 32 brauch 28 (4 zuviel)}

SCRIPT OnCreate;
begin
    { string 28}
	builddoor := getEntity('Gen_Interior_WoodDoor_(D)16'); 
	UnfreezeEntity(builddoor);
	SetDoorState(builddoor,DOOR_CLOSED);
	UnLockEntity(builddoor);
	SetDoorOpenAngleOut(builddoor,89.0);
end;
		
END. {End of file}

        ";

        $expected = [
            "10000000",
            "0a000000",
            "11000000",
            "0a000000",
            "09000000",
            "21000000",
            "04000000",
            "01000000",
            "00000000",
            "12000000",
            "02000000",
            "1c000000",
            "10000000",
            "01000000",
            "10000000",
            "02000000",
            "76000000",
            "16000000",
            "04000000",
            "1c000000",
            "01000000",
            "14000000",
            "01000000",
            "04000000",
            "1c000000",
            "10000000",
            "01000000",
            "37010000",
            "14000000",
            "01000000",
            "04000000",
            "1c000000",
            "10000000",
            "01000000",
            "12000000",
            "01000000",
            "02000000",
            "10000000",
            "01000000",
            "96000000",
            "14000000",
            "01000000",
            "04000000",
            "1c000000",
            "10000000",
            "01000000",
            "98000000",
            "14000000",
            "01000000",
            "04000000",
            "1c000000",
            "10000000",
            "01000000",
            "12000000",
            "01000000",
            "0000b242",
            "10000000",
            "01000000",
            "d3010000",
            "11000000",
            "09000000",
            "0a000000",
            "0f000000",
            "0a000000",
            "3b000000",
            "00000000"
        ];

        $compiler = new Compiler();
        $compiled = $compiler->parse($script, false, MHT::GAME_MANHUNT, MHT::PLATFORM_PC);


        if ($compiled['CODE'] != $expected){
            $index = 0;
            foreach ($compiled['CODE'] as $index => $item) {
                if ($expected[$index] == $item){
                    echo ($index + 1) . '->' . $item . " " . $item->debug . "\n";
                }else{
                    echo "MISSMATCH need " . $expected[$index] . " got " . $compiled['CODE'][$index] . " " . $compiled['CODE'][$index]->debug . "\n";
                }
            }

            exit;
        }

        $this->assertEquals($compiled['CODE'], $expected, 'The bytecode is not correct');
    }


}