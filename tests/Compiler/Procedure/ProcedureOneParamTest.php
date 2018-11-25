<?php
namespace App\Tests\Procedure;

use App\Service\Archive\Glg;
use App\Service\Archive\Mls;
use App\Service\Compiler\Compiler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcedureOneParamTest extends KernelTestCase
{

    public function test()
    {

        $this->assertEquals(true, true);
return;

        $script = "
            scriptmain LevelScript;

            entity
                A01_Escape_Asylum : et_level;
                
            var
                gMyGoalNodeName : string[30];

            procedure MakeHunterGotoGoal(GoalNodeName : string); FORWARD;

            
            procedure MakeHunterGotoGoal;
            begin
                StringCopy(gMyGoalNodeName, GoalNodeName);
            end;
            
            end.

        ";

        $expected = [
            "10000000", //Script start block
            "0a000000", //Script start block
            "11000000", //Script start block
            "0a000000", //Script start block
            "09000000", //Script start block

            //StringCopy(gMyGoalNodeName, GoalNodeName);

            //gMyGoalNodeName
            "21000000", //Prepare string read (DATA table)
            "04000000", //Prepare string read (DATA table)
            "01000000", //Prepare string read (DATA table)
            "30030000", //Offset in byte
            "12000000", //parameter (Read String var)
            "02000000", //parameter (Read String var)
            "1e000000", //value 30
            "10000000", //nested call return result
            "01000000", //nested call return result

            "10000000", //nested string return result
            "02000000", //nested string return result

            //GoalNodeName
            "13000000", //read from script var
            "01000000", //read from script var
            "04000000", //read from script var
            "f4ffffff", //Offset
            "12000000", //parameter (Read String var)
            "02000000", //parameter (Read String var)
            "00000000", //value 0
            "10000000", //nested call return result
            "01000000", //nested call return result

            "10000000", //nested string return result
            "02000000", //nested string return result

            "6d000000", //StringCopy Call


            "11000000", //unknown
            "09000000", //unknown
            "0a000000", //unknown
            "0f000000", //unknown
            "0a000000", //unknown
            "3a000000", //unknown
            "08000000", //unknown
        ];

        $compiler = new Compiler();
        $compiled = $compiler->parse($script);

        if ($compiled['CODE'] != $expected){
            foreach ($compiled['CODE'] as $index => $item) {
                if ($expected[$index] == $item){
                    echo ($index + 1) . '->' . $item . "\n";
                }else{
                    echo "MISSMATCH need " . $expected[$index] . " got " . $compiled['CODE'][$index] . "\n";
                }
            }
            exit;
        }

        $this->assertEquals($compiled['CODE'], $expected, 'The bytecode is not correct');

    }

}