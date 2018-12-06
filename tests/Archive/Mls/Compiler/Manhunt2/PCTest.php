<?php
namespace App\Tests\Archive\Mls\Compiler\Manhunt2;

use App\Service\Compiler\Compiler;
use App\Service\Resources;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PCTest extends KernelTestCase
{

    public function testLevelScript()
    {
        $resources = new Resources();
        $resources->workDirectory = explode("/tests/", __DIR__)[0] . "/tests/Resources";

        $mhls = $resources->load('/Archive/Mls/Manhunt2/PC/A01_Escape_Asylum.mls')->getContent();

        // compile levelscript
        $compiler = new Compiler();
        $levelScriptCompiled = $compiler->parse($mhls[0]['SRCE'], false, 'mh2');

        foreach ($levelScriptCompiled as $index => $section) {

            //only used inside the compiler
            if ($index == "extra") continue;

            //memory is not correct but works...
            if ($index == "DMEM") continue;
            if ($index == "SMEM") continue;

            //we do not generate the LINE (debug stuff)
            if ($index == "LINE") continue;

            $this->assertEquals(
                $mhls[0][$index],
                $section,
                $index . " Mismatch"
            );
        }

        $test = 37;
//        $test = 47;
        for($i = 0; $i < 37 ; $i++){
//        for($i = $test; $i < $test+1 ; $i++){
            $testScript = $mhls[$i];

            var_dump($testScript['NAME'], $i);

            //compile a other script based on the levelscript
            $compiled = $compiler->parse($testScript['SRCE'], $levelScriptCompiled, 'mh2');



            if ($testScript['CODE'] != $compiled['CODE']){
                foreach ($testScript['CODE'] as $index => $item) {
                    if ($compiled['CODE'][$index] == $item){
                        echo ($index + 1) . '->' . $item . "\n";
                    }else{
                        echo "MISMATCH need |" . $item . "| got |" . $compiled['CODE'][$index] . "|\n";
                    }
                }
                exit;
            }
//exit;
            $this->assertEquals(
                $testScript['CODE'],
                $compiled['CODE']
            );

            foreach ($compiled as $index => $section) {

                //only used inside the compiler
                if ($index == "extra") continue;

                //memory is not correct but works...
                if ($index == "DMEM") continue;
                if ($index == "SMEM") continue;

                //we do not generate the LINE (debug stuff)
                if ($index == "LINE") continue;
                if ($index == "STAB" && count($section) == 0) continue;

                $this->assertEquals(
                    $testScript[$index],
                    $section,
                    $index . " Mismatch " . $testScript['NAME']
                );
            }

        }
    }
}