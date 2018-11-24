<?php
namespace App\Tests\Archive\Mls\Build\Manhunt2;

use App\Service\Archive\Mls\Build;
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

        $builder = new Build();
        $binary = $builder->build($mhls);


        $this->assertEquals(
            md5($binary),
            'a3b90b413661d5fb11fbec8a629223d1'
        );

        file_put_contents('/Users/matthias/mh2/levels/A01_Escape_Asylum/A01_Escape_Asylum.mls', $binary);

    }

}