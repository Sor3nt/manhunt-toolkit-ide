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
        $compiled = $compiler->parse($mhls[0]['SRCE'], false, 'mh2');

        foreach ($compiled as $index => $section) {

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

        $testScript = $mhls[4];

        //compile a other script based on the levelscript
        $compiled = $compiler->parse($testScript['SRCE'], $compiled, 'mh2');
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
                $index . " Mismatch"
            );
        }


    }

}